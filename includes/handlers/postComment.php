<?php
require_once("../../config/config.php");
require_once("../classes/User.php"); 
require_once("../classes/Comment.php");

if(isset($_POST['commentText']) && isset($_POST['postedBy']) && isset($_POST['videoId'])) {
    
    //Retrieves User logged in Object
    $userLoggedInObj = new User($con, $_SESSION["username"]);

    $query = $con->prepare("INSERT INTO tutorial_comments(postedBy, videoId, responseTo, body)
                            VALUES(?, ?, ?, ?)");
    $query->bind_param("siis", $postedBy, $videoId, $responseTo, $commentText);

    //Variable data to be stored
    $postedBy = $_POST['postedBy'];
    $videoId = $_POST['videoId'];
    $responseTo = isset($_POST['responseTo']) ? $_POST['responseTo'] : 0;
    $commentText = $_POST['commentText'];

    //TUTORIAL COMMENTS SANITIZATION
    $commentText = strip_tags($commentText);  //removes any html tags
    $commentText = mysqli_real_escape_string($con, $commentText);
    $query->execute();

    //New comment stored by last Id stored 
    $newComment = new Comment($con, $con->insert_id, $userLoggedInObj, $videoId);
    //Output the new comment through create function
    echo $newComment->create();
}
else {
    echo "One or more parameters are not passed into subscribe.php the file";
}

?>
