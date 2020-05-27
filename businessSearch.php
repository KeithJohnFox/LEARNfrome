<?php
    require_once("includes/header.php");
    require_once("includes/classes/Video.php");
?>

<div class="tutorialPage">
    <div class="searchBackground">
        <div class="businessBackground">
            <div class="searchBarContainer">
                <form action="tutorialSearch.php" method="GET">
                    <input type="text" class="searchBar" name="term" placeholder="Search Tutorial...">
                    <button class="searchButton">
                        <img src="assets/images/icons/magnifying_glass.png">
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="back" >
            <a class="backpage" href="tutorials.php">
                <img src="assets/images/icons/back.png">  
                GO BACK
            </a>   
    </div>
    
    <div class="listingContainer">
        <div class="videoSection">
            <?php
                //VideoGrid Instance
                $videoGrid = new VideoGrid($con, $userLoggedInObj->getUsername());
                echo $videoGrid->businessCategory(null, "Business Tutorials", false);
            ?>
        </div>
    </div>
</div>




