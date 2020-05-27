<?php
require_once("includes/header.php");
require_once("includes/classes/VideoPlayer.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
require_once("includes/classes/VideoUploadData.php");
require_once("includes/classes/SelectThumbnail.php");


if(!isset($_GET["videoId"])) {
    echo "No Tutorial selected";
    exit();
}

//Check if enters a video Id that is not there own then display "Not your tutorial"
$video = new Video($con, $_GET["videoId"], $userLoggedInObj);
if($video->getUploadedBy() != $userLoggedInObj->getUsername()) {
    echo "Not your tutorial!";
    exit();
}

$detailsMessage = "";
if(isset($_POST["saveButton"])) {
    $videoData = new VideoUploadData(
        null,
        $_POST["titleInput"],
        $_POST["descriptionInput"],
        $_POST["privacyInput"],
        $_POST["categoryInput"],
        $userLoggedInObj->getUsername()
    );

    if($videoData->updateDetails($con, $video->getId())) {
        $detailsMessage = "<div class='alert alert-success'>
                                <strong>SUCCESS!</strong> Your tutorial details successfully updated!
                            </div>";
        $video = new Video($con, $_GET["videoId"], $userLoggedInObj);
    }
    //Error
    else {
        $detailsMessage = "<div class='alert alert-danger'>
                                <strong>ERROR!</strong> Update not successful something went wrong
                            </div>";
    }
}
?>
<script src="assets/js/editVideoActions.js"></script>
<div class="editWrapper">
    <div class="editVideoContainer">

        <!-- Output message -->
        <!-- <div class="message">
            <?php echo $detailsMessage; ?>
        </div> -->

        <div class="topSection">
            <?php
            $videoPlayer = new VideoPlayer($video);
            echo $videoPlayer->create(false);

            $selectThumbnail = new SelectThumbnail($con, $video);
            echo $selectThumbnail->create();
            ?>
        </div>
            <?php
            echo $detailsMessage;
            $formProvider = new VideoDetailsFormProvider($con);
            echo $formProvider->createEditDetailsForm($video);
            ?>
        </div>
    </div>
</div>