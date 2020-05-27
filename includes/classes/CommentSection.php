
<?php
//CommentSection Loads comments for tutorial videos
class CommentSection {
    //Private variables used
    private $con, $video, $userLoggedInObj;

    //Constructor
    public function __construct($con, $video, $userLoggedInObj) {
        $this->con = $con;
        $this->video = $video;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    //Create comment section function
    public function create() {
        return $this->createCommentSection();   //On call return all comments
    }

    //Listing of Comments
    private function createCommentSection() {
        $numComments = $this->video->getNumberOfComments(); //Call num of comments

        $postedBy = $this->userLoggedInObj->getUsername();
        $videoId = $this->video->getId();

        $profileButton = ButtonProvider::createUserProfileButton($this->con, $postedBy);   //User profile link 
        $commentAction = "postComment(this, \"$postedBy\", $videoId, null, \"comments\")";  //Comment Action
        $commentButton = ButtonProvider::createButton("COMMENT", null, $commentAction, "postComment");  //Create Comment Button
        
        //Comment Variables displayed in div
        $comments = $this->video->getComments();    //Retrieve comment
        $commentItems = "";

        //Loop through each comment to be displayed in comment Section Div
        foreach($comments as $comment) {
            $commentItems .= $comment->create();
        }

        //Output Comments HTML
        return "<div class='commentSection'>

                    <div class='header'>
                        <span class='commentCount'>$numComments Comments</span>

                        <div class='commentForm'>
                            $profileButton
                            <textarea class='commentBodyClass' placeholder='Add a public comment'></textarea>
                            $commentButton
                        </div>
                    </div>

                    <div class='comments'>
                        $commentItems
                    </div>

                </div>";
    }

}
?>