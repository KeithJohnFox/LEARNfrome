<?php
    require_once("includes/header.php");
    require_once("includes/classes/Video.php");
    // require_once("tutorialSearch.php");
?>

<div class="tutorialPage">
    <div class="searchBackground">
        <div class="mainBackground">
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

    <!-- <div class="jumbotron jumbotron-fluid>
        <div class="container">
        
            <h1 class="display-4">Fluid jumbotron</h1>
            <p class="lead">This is a modified jumbotron that occupies the entire horizontal space of its parent.</p>
        </div>
    </div> -->
    
    <div class="categoryBox">
        <div class="boxwrap">
            <a class="navbarCategory" href="codingSearch.php">
              <img src="assets/images/icons/coding.png" alt="CodeIcon">  
                Coding
            </a>
            <a class="navbarCategory" href="designSearch.php">
                <img src="assets/images/icons/designIcon.png" alt="DesignIcon">
                Design
            </a>
            <a class="navbarCategory" href="businessSearch.php">
                <img src="assets/images/icons/businessIcon.png" alt="BusinessIcon">
                Business
            </a>
            <a class="navbarCategory" href="developmentSearch.php">
                <img src="assets/images/icons/personalDev.png" alt="Icon">
                Personal Development
            </a>
            <a class="navbarCategory" href="cookingSearch.php">
                <img src="assets/images/icons/cookingIcon.png" alt="CookingIcon">
                Cooking
            </a>
        </div>
    </div>

    <div class="listingContainer">
        <div class="videoSection">
            <?php

            $followerContentProvider = new FollowerContentProvider($con, $userLoggedInObj);
            $followerVideos = $followerContentProvider->getVideos();

            //VideoGrid Instance
            $videoGrid = new VideoGrid($con, $userLoggedInObj->getUsername());

            if(sizeof($followerVideos) > 0) {
                echo $videoGrid->create($followerVideos, "Your Follower's Tutorials", false);
            }
            
            echo $videoGrid->create(null, "Recommended Tutorials", false);
            ?>
        </div>
    </div>
</div>




