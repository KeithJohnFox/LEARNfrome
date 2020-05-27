<?php
require_once("../../config/config.php");

if(isset($_POST['userTo']) && isset($_POST['userFrom'])) {
    $userTo = $_POST['userTo'];
    $userFrom = $_POST['userFrom'];
    
    //check if the user is a follower
    $query = $con->prepare("SELECT * FROM followers WHERE userTo= ? AND userFrom=?");
    $query->bind_param("ss", $userTo, $userFrom);
    $query->execute();
    $result = $query->get_result();
    $row = mysqli_num_rows($result);

    if($row == 0) {
        // Insert follower
        $query = $con->prepare("INSERT INTO followers(userTo, userFrom) VALUES(?, ?)"); //THIS EDITITED with 0, commentId
        $query->bind_param("ss", $userTo, $userFrom);
        $query->execute();
    }
    else {
        // Delete follower
        $query = $con->prepare("DELETE FROM followers WHERE userTo=? AND userFrom=?");
        $query->bind_param("ss", $userTo, $userFrom);
        $query->execute();
    }

    //Display Number of followers
    $query = $con->prepare("SELECT * FROM followers WHERE userTo=?");
    $query->bind_param("s", $userTo);
    $query->execute();
    $result = $query->get_result();
    $row = mysqli_num_rows($result);
    echo $row;
}
//Error Message
else {
    echo "One or more parameters are not being passed into follow.php the file";
}
?>