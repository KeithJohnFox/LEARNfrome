<?php
require_once("../../config/config.php");

if(isset($_POST['videoId']) && isset($_POST['thumbnailId'])) {
    $videoId = $_POST['videoId'];
    $thumbnailId = $_POST['thumbnailId'];

    $query = $con->prepare("UPDATE thumbnails SET selected=0 WHERE videoId=?");
    $query->bind_param("i", $videoId);
    $query->execute();

    $query = $con->prepare("UPDATE thumbnails SET selected=1 WHERE id=?");
    $query->bind_param("i", $thumbnailId);
    $query->execute();
}
else {
    echo "One or multiple parameters did not passed into updateThumbnail.php the file";
}
?>