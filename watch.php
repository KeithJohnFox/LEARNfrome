<?php 
require_once("includes/header.php"); 
require_once("includes/classes/Video.php"); 
require_once("includes/classes/VideoPlayer.php"); 
require_once("includes/classes/VideoInfoSection.php"); 
require_once("includes/classes/Comment.php"); 
require_once("includes/classes/CommentSection.php"); 

//Gets id for video
if(!isset($_GET["id"])) {
    echo "No video url passed into page, video id missing";
    exit();
}

//Store video in video var by retrieveing video id and userloggedin
$video = new Video($con, $_GET["id"], $userLoggedInObj);
//update viewcount
$video->incrementViews();
$category = $video->getCategory();

?>
<!-- Java Script files  -->
<script src="assets/js/videoPlayerActions.js"></script>
<script src="assets/js/commentActions.js"></script>

<div class="mainbody">
    <div class="watchLeftColumn">
        <center>
        <?php
            //Video Player Call
            $videoPlayer = new VideoPlayer($video);
            echo $videoPlayer->create(true); //Auto play = true
        ?>
        </center>
    </div>
    <div class="videoContainer">
        <div class="videoContent">
            <?php
                //Tutorial Details section eg title, views etc
                $videoPlayer = new VideoInfoSection($con, $video, $userLoggedInObj);
                echo $videoPlayer->create();
                
                //Comment Section Object
                $commentSection = new CommentSection($con, $video, $userLoggedInObj);
                echo $commentSection->create(); 
            ?>
        <div class = "bottomSection">
            <span class="viewCount">
                
            </span>
        </div>
        </div>

        <div class="suggestions">
            <?php
            $videoGrid = new VideoGrid($con, $userLoggedInObj);
            //ECHOS ALL TUTORIALS RELATED TO CATEGORY FROM MOST VIEWED DOWN
            echo $videoGrid->getCategoryTutorials($category, null, null, false);
            ?>
        </div> 
    </div>
</div>




                