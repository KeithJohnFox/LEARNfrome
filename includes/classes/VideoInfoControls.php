<?php
require_once("includes/classes/ButtonProvider.php"); 
class VideoInfoControls {

    private $video, $userLoggedInObj;

    public function __construct($video, $userLoggedInObj) {
        $this->video = $video;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    //Function displays like/dislike buttons
    public function create() {

        $likeButton = $this->createLikeButton();
        $dislikeButton = $this->createDislikeButton();
        
        return "<div class='controls'>
                    $likeButton
                    $dislikeButton
                </div>";
    }

    

    // //Like Button functionality
    private function createLikeButton() {
        

        //Data needed
        $text = $this->video->getLikes();
        $videoId = $this->video->getId();
        $action = "likeVideo(this, $videoId)";
        $class = "likeButton";

        //Icon Path
        $imageSrc = "assets/images/icons/thumb-up.png";

        //Display darker icon button for clicked 
        if($this->video->wasLikedBy()) {
            $imageSrc = "assets/images/icons/thumb-up-active.png";
        }
        //Passing data to button provider class
        return ButtonProvider::createButton($text, $imageSrc, $action, $class);
    }

    //Dislike Button functionality
    private function createDislikeButton() {
        //call get Dislikes
        $text = $this->video->getDislikes();
        $videoId = $this->video->getId();
        $action = "dislikeVideo(this, $videoId)";
        $class = "dislikeButton";
        //Icon Image
        $imageSrc = "assets/images/icons/thumb-down.png";

        //Display darker icon button for clicked 
        if($this->video->wasDislikedBy()) {
            $imageSrc = "assets/images/icons/thumb-down-active.png";
        }
        //Passing data to button provider class
        return ButtonProvider::createButton($text, $imageSrc, $action, $class);
    }

}
?>