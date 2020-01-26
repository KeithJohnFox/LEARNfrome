<?php  
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

$limit = 10; //Number of posts to be loaded per call

//Posts Var new instance of post class takes in database con and userloggedIn (username)
$posts = new Post($con, $_REQUEST['userLoggedIn']);
//Calls loadProfilePosts Function in Post Class
$posts->loadProfilePosts($_REQUEST, $limit);
?>