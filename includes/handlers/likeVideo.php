<?php
    //Required Files
    require_once("../classes/Video.php");
    require_once("../../config/config.php");
    require_once("../classes/User.php"); 

    //Session variable retrieve user logged in
    $username = $_SESSION["username"];
    $videoId = $_POST['videoId'];

    //Instance of user class
    $userLoggedInObj = new User($con, $username);
    $video = new Video($con, $videoId, $userLoggedInObj);

    
    //Print out like function
    echo $video->like();
?>