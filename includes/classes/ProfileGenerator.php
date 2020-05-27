<?php
require_once("ProfileData.php");    //This class handles the retrieval of the data used in the profile
class ProfileGenerator {

    //Private Variables
    private $con, $userLoggedInObj, $profileData;

    //Function
    public function __construct($con, $userLoggedInObj, $profileUsername) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->profileData = new ProfileData($con, $profileUsername);
    }

    //Main Create Function
    public function create() {
        $profileUsername = $this->profileData->getProfileUsername();
        
        echo $profileUsername;
        if(!$this->profileData->userExists()) {
            return "User does not exist";
        }
        else {
            return "user exisits";
        }

    }

    //Every User has a cover picture on their main profile
    public function createCoverPhotoSection() {
        $coverPhotoSrc = $this->profileData->getCoverPhoto();
      
        $name = $this->profileData->getProfileUserFullName();
        return "<div class='coverPhotoContainer'>
                    <img src='$coverPhotoSrc' class='coverPhoto'>
                    <span class='channelName'>$name</span>
                </div>";
    }

    public function createUserDetails() {
        $followerCount = $this->profileData->getFollowerCount();


        return $followerCount; 
    }


}
?>