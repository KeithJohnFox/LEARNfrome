<?php
    require_once("../classes/Video.php");
    require_once("../../config/config.php");
    require_once("../classes/User.php"); 

    $username = $_SESSION['username'];
    $videoId = $_POST['videoId'];

    $userLoggedInObj = new User($con, $username);
    $video = new Video($con, $videoId, $userLoggedInObj);

    echo $video->dislike();
?>