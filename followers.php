<?php
require_once("includes/header.php");

$followerContentProvider= new FollowerContentProvider($con, $userLoggedInObj);
$videos = $followerContentProvider->getVideos();

$videoGrid = new VideoGrid($con, $userLoggedInObj);
?>
<div class="largeVideoGridContainer">
    <?php
    if(sizeof($videos) > 0) {
        echo $videoGrid->createLarge($videos, "New from your followers", false);
    }
    else {
        echo "<div class='successWrapper'>
            <div class='successContainer'>
                <div class='alert alert-danger'>
                    <strong>No Tutorials!</strong> You have not followed any user's tutorials yet, click go to tutorials below and start watching.
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