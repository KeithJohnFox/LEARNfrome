<?php
require_once("../../config/config.php");
include("../classes/User.php");    //Includes User Class to index page
include("../classes/Post.php");    //Includes Post Class to index page
include("../classes/Notification.php");    //Includes Notification Class to index page

if(isset($_POST['post_body'])) {
    $post = new Post($con, $_POST['user_from']);
    $post->submitPost($_POST['post_body'], $_POST['user_to'], '');

}
?>