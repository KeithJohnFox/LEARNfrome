<?php

class Video {
    
    private $con, $user, $userLoggedInObj;

    public function __construct($con, $input, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;

        
        
        //This checks if input is an array that will gave all the sql data
        if(is_array($input)) {
            $this->user = $input;
        }
        else {
            $query = $this->con->prepare("SELECT * FROM videos WHERE id = ?");
            $query->bind_param("i", $input);
            $query->execute();
            
            $result = $query->get_result();

            $this->user = mysqli_fetch_array($result);
        }
    }
    
    public function getId() {
        return $this->user["id"];
    }

    public function getTitle() {
        return $this->user["title"];
    }

    public function getUploadedBy() {
        return $this->user["uploadedBy"];
    }

    public function getDescription() {
        return $this->user["description"];
    }

    public function getPrivacy() {
        return $this->user["privacy"];
    }

    public function getFilePath() {
        return $this->user["filePath"];
    }

    public function getTimeStamp() {
        $date = $this->user["uploadDate"];
        return date("M jS, Y", strtotime($date)); // Oct 15, 2020 example
    }

    public function getCategory() {
        return $this->user["category"];
    }

    public function getUploadDate() {
        $date = $this->user["uploadDate"];
        return date("M j, Y", strtotime($date));
    }

    public function getViews() {
        return $this->user["views"];
    }

    public function getDuration() {
        return $this->user["duration"];
    }

    public function incrementViews() {
        $query = $this->con->prepare("UPDATE videos SET views=views+1 WHERE id=?");
        $query->bind_param("i", $videoId);

        $videoId = $this->getId();
        $query->execute();

        $this->user["views"] = $this->user["views"] + 1;

        
    }

    public function getLikes() {
        
        $query = $this->con->prepare("SELECT count(*) as 'count' FROM video_likes WHERE videoId = ?");
        $query->bind_param("i", $videoId);
        $videoId = $this->getId();
        $query->execute();

        $result = $query->get_result();
        $data = mysqli_fetch_array($result);
        return $data["count"];
    }

    public function getDislikes() {
        $query = $this->con->prepare("SELECT count(*) as 'count' FROM video_dislikes WHERE videoId = ?");
        $query->bind_param("i", $videoId);
        $videoId = $this->getId();
        $query->execute();

        $result = $query->get_result();

        $data = mysqli_fetch_array($result);

        return $data["count"];

    }

    public function like() {
        //Id for video
        $id = $this->getId();
        //Get username logged in
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasLikedBy()) {
            // User has already liked
            $query = $this->con->prepare("DELETE FROM video_likes WHERE username= ? AND videoId= ?");
            $query->bind_param("si", $username, $id);
            $query->execute();

            $result = array(
                "likes" => -1,
                "dislikes" => 0
            );
            return json_encode($result);
            
        }
        else {
            $query = $this->con->prepare("DELETE FROM video_dislikes WHERE username= ? AND videoId= ?");
            $query->bind_param("si", $username, $id);
            $query->execute();

            $stmt = $this->con->prepare("INSERT INTO video_likes(username, videoId) VALUES(?, ?)"); //THIS EDITITED with 0, commentId
            $stmt->bind_param("si", $username, $id);
            $stmt->execute();
            $stmt->store_result();
            $count = $stmt->num_rows;
            
            $result = array(
                "likes" => 1,
                "dislikes" => 0 - $count
            );
            return json_encode($result);
        }
    }

    public function dislike() {
        $id = $this->getId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasDislikedBy()) {
            // User has already liked
            $query = $this->con->prepare("DELETE FROM video_dislikes WHERE username= ? AND videoId= ?");
            $query->bind_param("si", $username, $id);
            $query->execute();

            $result = array(
                "likes" => 0,
                "dislikes" => -1
            );
            return json_encode($result);
        }
        else {
            $stmt = $this->con->prepare("DELETE FROM video_likes WHERE username= ? AND videoId= ?");
            $stmt->bind_param("si", $username, $id);
            $stmt->execute();
            $stmt->store_result();
            $count = $stmt->num_rows;

            $query = $this->con->prepare("INSERT INTO video_dislikes(username, videoId) VALUES(?, ?)");
            $query->bind_param("si", $username, $id);
            $query->execute();

            $result = array(
                "likes" => 0 - $count,
                "dislikes" => 1
            );
            return json_encode($result);
        }
    }

    public function wasLikedBy() {
        $id = $this->getId(); //Retreieves Id of video
        $stmt = $this->con->prepare("SELECT * FROM video_likes WHERE username= ? AND videoId= ?"); //Query likes on specified ID video
        $stmt->bind_param("si", $username, $id);
        $username = $this->userLoggedInObj->getUsername();  //Get userloggedIn from UserObject
        $stmt->execute();
        $result = $stmt->get_result();
        $query = mysqli_num_rows($result);
        return $query > 0;
    }

    public function wasDislikedBy() {
        $stmt = $this->con->prepare("SELECT * FROM video_dislikes WHERE username= ? AND videoId= ?");
        $stmt->bind_param("si", $username, $id);

        $id = $this->getId();

        $username = $this->userLoggedInObj->getUsername();
        $stmt->execute();
        $result = $stmt->get_result();
        $query= mysqli_num_rows($result);
        return $query > 0;
    }

    public function getComments() {
        $query = $this->con->prepare("SELECT * FROM tutorial_comments WHERE videoId=? AND responseTo=0 ORDER BY datePosted DESC");
        $query->bind_param("i", $id);

        $id = $this->getId();

        $query->execute();
        $result = $query->get_result();
        $comments = array();

        while($row = mysqli_fetch_assoc($result)) {
            
            $comment = new Comment($this->con, $row, $this->userLoggedInObj, $id);
            array_push($comments, $comment);
        }

        return $comments;
    }

    public function getNumberOfComments() {
        $stmt = $this->con->prepare("SELECT * FROM tutorial_comments WHERE videoId=?");
        $stmt->bind_param("i", $id);
        $id = $this->getId();
        $stmt->execute();

        $stmt->store_result();
        $query = $stmt->num_rows;

        return $query;
    }   

    public function getThumbnail() {
        $stmt = $this->con->prepare("SELECT filePath FROM thumbnails WHERE videoId=? AND selected=1");
        $stmt->bind_param("i", $videoId);
        $videoId = $this->getId();
        $stmt->execute();

        $result = $stmt->get_result();

        $data = mysqli_fetch_array($result);

        return $data["filePath"];
    }

}
?>