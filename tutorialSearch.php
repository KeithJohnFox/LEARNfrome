<?php
require_once("includes/header.php");
require_once("includes/classes/SearchResultsProvider.php");
require_once("includes/classes/Video.php");

if(!isset($_GET["term"]) || $_GET["term"] == "") {
    echo "You must enter a search term";
    exit();
}

$term = $_GET["term"];

// SANITIZATION
$term = strip_tags($term);
$term = preg_replace('/[^\p{L}\p{N}\s]/u', '', $term); //Replace these symbols with nothing
$term = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $term); //Replace javascript tags and symbols with nothing

if(!isset($_GET["orderBy"]) || $_GET["orderBy"] == "views") {
    $orderBy = "views";
}
else {
    $orderBy = "uploadDate";
}

$searchResultsProvider = new SearchResultsProvider($con, $userLoggedInObj);
//Make Query in getTutorial Function 
$videos = $searchResultsProvider->getVideos($term, $orderBy);

//Video grid Instance
$videoGrid = new VideoGrid($con, $userLoggedInObj);
?>



<div class="searchContainer">
    <div class="topPad">

    </div>
    <div class="largeVideoGridContainer">

        <?php

        if(sizeof($videos) > 0) {
            echo $videoGrid->createLarge($videos, sizeof($videos) . " Tutorials Found", true);
        }
        else {
            echo "<div class='successWrapper'>
            <div class='successContainer'>
                <div class='alert alert-danger'>
                    <strong>No Search Results Found!</strong> Try rephrasing your search to find what your looking for.
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
</div>
