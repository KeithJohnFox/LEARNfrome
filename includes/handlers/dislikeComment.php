<?php
//Required Files
require_once("../../config/config.php");
require_once("../classes/Comment.php"); 
require_once("../classes/User.php"); 

//Variable data used
$username = $_SESSION["username"];
$videoId = $_POST["videoId"];
$commentId = $_POST["commentId"];

//Comment and User logged in Objects
$userLoggedInObj = new User($con, $username);
$comment = new Comment($con, $commentId, $userLoggedInObj, $videoId);

echo $comment->dislike();
?>