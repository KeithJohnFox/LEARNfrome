<?php
//HANDLES THE TUTORIAL DISPLAYING EG THUMBNAIL, VIEWS, TITLE, CREATOR 
class VideoGridItem {

    //Private variables
    private $video, $largeMode;

    //Constructor
    public function __construct($video, $largeMode) {
        $this->video = $video;
        $this->largeMode = $largeMode;
    }

    //Creates Tutorial 
    public function create() {
        //Execute Create Functions
        $thumbnail = $this->createThumbnail();
        $details = $this->createDetails();
        //URL to watch tutorial
        $url = "watch.php?id=" . $this->video->getId();

        //Returns HTML for Grid
        return "<a href='$url'> 
                    <div class='videoGridItem'>
                        $thumbnail
                        $details
                    </div>
                </a>";
    }

    //Retireves Tutorial Thumbnail
    private function createThumbnail() {
        
        $thumbnail = $this->video->getThumbnail();
        $duration = $this->video->getDuration();

        return "<div class='thumbnail'>
                    <img src='$thumbnail'>
                    <div class='duration'>
                        <span>$duration</span>
                    </div>
                </div>";
    }

    //Creates Title, Creator, Views, Description, Date
    private function createDetails() {
        $title = $this->video->getTitle();
        $username = $this->video->getUploadedBy();
        $views = $this->video->getViews();
        $description = $this->createDescription();
        $timestamp = $this->video->getTimeStamp();
        $likes = $this->video->getLikes();

        $title = (strlen($title) > 54) ? substr($title, 0, 55) . "..." : $title;

        return "<div class='details'>
                    
                        <h1 class='title'>$title</h1>
                    <div class='box'>
                        <span class='username'>$username</span>
                        <div class='stats'>
                            <span class='viewCount'>$views views - </span>
                            <span class='timeStamp'>$timestamp</span>
                        </div>
                    </div>    
                    <div class='likes'>
                        <span class='like'>
                            <img src='assets/images/icons/like2.png' alt='Like Icon'>
                        $likes</span>
                    </div>
                    $description
                </div>";
    }

    //Displays Tutorial Description
    private function createDescription() {
        //Large Mode shows description of tutorial, not large mode does not meaning its in recommened tutorials in watch page
        if(!$this->largeMode) {
            return "";
        }
        else {
            $description = $this->video->getDescription();
            //if the description is over 350 characters hide the rest as ...
            $description = (strlen($description) > 89) ? substr($description, 0, 90) . "..." : $description;
            //return description
            return "<h4>Description</h4><span class='description'>$description</span>";
        }
    }

}
?>