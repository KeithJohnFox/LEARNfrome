<?php
require_once("includes/header.php");
require_once("includes/classes/LikedTutorialsProvider.php");


$likedTutorialsProvider = new LikedTutorialsProvider($con, $userLoggedInObj);
$videos = $likedTutorialsProvider->getVideos();

$videoGrid = new VideoGrid($con, $userLoggedInObj);
?>
<div class="largeVideoGridContainer">
    <?php
    if(sizeof($videos) > 0) {
        echo $videoGrid->createLarge($videos, "Tutorials that you have liked", false);
    }
    else {

        echo "<div class='successWrapper'>
            <div class='successContainer'>
                <div class='alert alert-danger'>
                    <strong>No liked tutorials!</strong> You have not liked any tutorials yet, click go to tutorials below and start watching.
                </div>
                
                <div class='back' >
                        <a class='backpage' href='tutorials.php'>
                            <img src='assets/images/icons/tutIcon.png'>  
                            Go to Tutorials
                        </a>   
                </div>
                <div class='back' >
                        <a class='backpage' href='index.php'>
                            <img src='assets/images/icons/mainHome.png'>  
                            Go Home
                        </a>   
                </div>
            </div>
        </div>";
    }
    ?>
    
</div>