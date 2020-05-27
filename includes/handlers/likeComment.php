<?php
//Requiredd files
require_once("../../config/config.php");
require_once("../classes/Comment.php"); 
require_once("../classes/User.php"); 

//Variables needed for Object
$username = $_SESSION["username"];
$videoId = $_POST["videoId"];
$commentId = $_POST["commentId"];

//Objects
$userLoggedInObj = new User($con, $username);
$comment = new Comment($con, $commentId, $userLoggedInObj, $videoId);

echo $comment->like();
?>