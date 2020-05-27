<?php
//COMMENT CLASS HANDLES HTML OUTPUT OF TUTORIAL COMMENTS AND ALL FUNCTIONALITY eg Like / Dislike 
require_once("CommentControls.php");
require_once("ButtonProvider.php");

class Comment {

    //Private variables
    private $con, $sqlData, $userLoggedInObj, $videoId;

    //Contructor
    public function __construct($con, $input, $userLoggedInObj, $videoId) {
        //Input is ID of comment, so if input contains id then query all comments
        if(!is_array($input)) {
            $query = $con->prepare("SELECT * FROM tutorial_comments where id=?");
            $query->bind_param("i", $input);
            $query->execute();
            $result = $query->get_result();
            $input = mysqli_fetch_array($result);

        }
        
        $this->sqlData = $input;
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->videoId = $videoId;
    }

    //Creates comment div to be displayed
    public function create() {
        $id = $this->sqlData["id"]; //ID of comment
        $videoId = $this->getVideoId(); //Retreve id of which tutorial has related comment
        $postedBy = $this->sqlData["postedBy"]; // Who posted the comment
        $body = $this->sqlData["body"]; //Retreive text of comment
        $profileButton = ButtonProvider::createUserProfileButton($this->con, $postedBy);    //Profile Image picture link
        $timespan = $this->time_elapsed_string($this->sqlData["datePosted"]);   // retrieve Time 

        $commentControlsObj = new CommentControls($this->con, $this, $this->userLoggedInObj);
        $commentControls = $commentControlsObj->create();

        $numResponses = $this->getNumberOfReplies();
        
        if($numResponses > 0) {
            $viewRepliesText = "<span class='repliesSection viewReplies' onclick='getReplies($id, this, $videoId)'>
                                    View all $numResponses replies</span>";
        }
        else {
            $viewRepliesText = "<div class='repliesSection'></div>";
        }

        //Comments HTML
        return "<div class='itemContainer'>
                    <div class='comment'>
                        $profileButton
                        <div class='mainContainer'>
                            <div class='commentHeader'>
                                <a href='profile.php?username=$postedBy'>
                                    <span class='username'>$postedBy</span>
                                </a>
                                <span class='timestamp'>$timespan</span>
                            </div>
                            <div class='body'>
                                $body
                            </div>
                        </div>
                    </div>
                    $commentControls
                    $viewRepliesText 
                </div>";
    }

    public function getNumberOfReplies() {
        $stmt = $this->con->prepare("SELECT * FROM tutorial_comments WHERE responseTo=?");
        $stmt->bind_param("i", $id);
        $id = $this->sqlData["id"];

        $stmt->execute();

        $stmt->store_result();
        $query = $stmt->num_rows;

        return $query;
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function getId() {
        return $this->sqlData["id"];
    }

    public function getVideoId() {
        return $this->videoId;
    }

    public function wasLikedBy() {
        $id = $this->getId();
        $username = $this->userLoggedInObj->getUsername();
        $stmt = $this->con->prepare("SELECT * FROM video_likes WHERE username=? AND commentId=?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $query = mysqli_num_rows($result);

        return $query > 0;
    }

    public function wasDislikedBy() {
        $stmt = $this->con->prepare("SELECT * FROM video_dislikes WHERE username=? AND commentId=?");
        $stmt->bind_param("si", $username, $id);

        $id = $this->getId();

        $username = $this->userLoggedInObj->getUsername();
        $stmt->execute();
        $result = $stmt->get_result();
        $query = mysqli_num_rows($result);

        return $query > 0;
    }

    public function like() {
        $id = $this->getId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasLikedBy()) {
            // User has already liked
            $query = $this->con->prepare("DELETE FROM video_likes WHERE username=? AND commentId=?");
            $query->bind_param("si", $username, $id);
            $query->execute();

            return -1;
        }
        else {
            $stmt = $this->con->prepare("DELETE FROM video_dislikes WHERE username=? AND commentId=?");
            $stmt->bind_param("si", $username, $id);
            $stmt->execute();
            $stmt->get_result();
            $count = $stmt->num_rows;

            $query = $this->con->prepare("INSERT INTO video_likes(username, commentId) VALUES(?, ?)");
            $query->bind_param("si", $username, $id);
            $query->execute();

            return 1 + $count;
        }
    }

    public function dislike() {
        $id = $this->getId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasDislikedBy()) {
            // User has already liked
            $stmt = $this->con->prepare("DELETE FROM video_dislikes WHERE username=? AND commentId=?");
            $stmt->bind_param("si", $username, $id);
            $stmt->execute();

            return 1;
        }
        else {
            $stmt = $this->con->prepare("DELETE FROM video_likes WHERE username=? AND commentId=?");
            $stmt->bind_param("si", $username, $id);
            $stmt->execute();
            $stmt->get_result();
            $count = $stmt->num_rows;

            $stmt = $this->con->prepare("INSERT INTO video_dislikes(username, commentId) VALUES(?, ?)");
            $stmt->bind_param("si", $username, $id);
            $stmt->execute();

            return -1 - $count;
        }
    }

    public function getLikes() {
        $query = $this->con->prepare("SELECT count(*) as 'count' FROM video_likes WHERE commentId= ?");
        $query->bind_param("i", $commentId);
        $commentId = $this->getId();
        $query->execute();

        $result = $query->get_result();
        $data = mysqli_fetch_array($result);

        $numLikes = $data["count"];

        $query = $this->con->prepare("SELECT count(*) as 'count' FROM video_dislikes WHERE commentId= ?");
        $query->bind_param("i", $commentId);
        $query->execute();

        $result = $query->get_result();
        $data = mysqli_fetch_array($result);
        $numDislikes = $data["count"];
        
        return $numLikes - $numDislikes;
    }

    public function getReplies() {
        $query = $this->con->prepare("SELECT * FROM tutorial_comments WHERE responseTo=? ORDER BY datePosted ASC");
        $query->bind_param("i", $id);

        $id = $this->getId();

        $query->execute();
        $result = $query->get_result();
        $comments = "";
        $videoId = $this->getVideoId();

        while($row = mysqli_fetch_assoc($result)) {
            $comment = new Comment($this->con, $row, $this->userLoggedInObj, $videoId);
            $comments .= $comment->create();
        }

        return $comments;
    }
}
?>