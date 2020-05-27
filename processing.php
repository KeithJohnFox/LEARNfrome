
<?php
require_once("includes/header.php");
require_once("includes/classes/VideoUploadData.php");
require_once("includes/classes/VideoProcessor.php");
?>

<?php
//If upload tutorial button is pressed
if(!isset($_POST["uploadButton"])) {
    echo "No file sent to page.";
    exit();
}

//Instance of user logged in
$logged_in_user_obj = new User($con, $userLoggedIn); 

// Create file for uploading data
$videoUpoadData = new VideoUploadData(
    $_FILES["fileInput"],
    $_POST["titleInput"],
    $_POST["descriptionInput"],
    $_POST["privacyInput"],
    $_POST["categoryInput"],
    $logged_in_user_obj->getUsername()
);

// Process the video data (upload)
$videoProcessor = new VideoProcessor($con);
$wasSuccessful = $videoProcessor->upload($videoUpoadData);

// Check if the upload was successful
if($wasSuccessful) {
    
    echo "<div class='successWrapper'>
            <div class='successContainer'>
                <div class='alert alert-success'>
                    <strong>SUCCESS!</strong> Your tutorial has successfully Uploaded!
                </div>
                
                <div class='back' >
                        <a class='backpage' href='tutorials.php'>
                            <img src='assets/images/icons/tutIcon.png'>  
                            Go to Tutorials
                        </a>   
                </div>
                <div class='back' >
                        <a class='backpage' href='index.php'>
                            <img src='assets/images/icons/mainHome.png'>  
                            Go Home
                        </a>   
                </div>
            </div>
        </div>";
}   
else {
    echo "<div class='successWrapper'>
            <div class='successContainer'>
                <div class='alert alert-danger'>
                    <strong>PROBLEM!</strong> Something went wrong your tutorial did not upload try again!
                </div>
                
                <div class='back' >
                        <a class='backpage' href='tutorials.php'>
                            <img src='assets/images/icons/tutIcon.png'>  
                            Go to Tutorials
                        </a>   
                </div>
                <div class='back' >
                        <a class='backpage' href='index.php'>
                            <img src='assets/images/icons/mainHome.png'>  
                            Go Home
                        </a>   
                </div>
            </div>
        </div>";
}
?>


