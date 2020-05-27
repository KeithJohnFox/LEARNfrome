<?php
class ButtonProvider {

    public static function createButton($text, $imageSrc, $action, $class) {
        //Image source, if button is clicked show button clicked indicator icon
        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc'>";

        //action stores the action user made eg click button and store to be updated in database
        
        //button div
        return "<button class='$class' onclick='$action'>
                    $image 
                    <span class='text'>$text</span>
                </button>";
    }

    //Creates a link that looks like a button 
    public static function createHyperlinkButton($text, $imageSrc, $href, $class) {
        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc'>";

        //Returns User Profile Image (linkable click)
        return "<a href='$href'>
                    <button class='$class'>
                        $image
                        <span class='text'>$text</span>
                    </button>
                </a>";
    }

    //Displays username and profile picture
    public static function createUserProfileButton($con, $username) {
        $userObj = new User($con, $username);
        $profilePic = $userObj->getProfilePic();
        $link = "$username";

        return "<a href='$link'>
                    <img src='$profilePic' class='profilePicture'>
                </a>";
    }
    
    //Creates an edit button on watch.php page
    public static function createEditVideoButton($videoId) {
        $href = "editTutorial.php?videoId=$videoId";

        $button = ButtonProvider::createHyperlinkButton("EDIT VIDEO", null, $href, "edit button");

        return "<div class='editVideoButtonContainer'>
                    $button
                </div>";
    }
    
    //Creates Follow button on videos.
    public static function createFollowerButton($con, $userToObj, $userLoggedInObj) {
        $userTo = $userToObj->getUsername();
        $userLoggedIn = $userLoggedInObj->getUsername();

        //Check if user follows through isFollowedTo function
        $isFollowedTo = $userLoggedInObj->isFollowedTo($userTo);
        //IF true show "FOLLOWED" or false "FOLLOW"
        $buttonText = $isFollowedTo ? "FOLLOWED" : "FOLLOW";
        $buttonText .= " " . $userToObj->getFollowerCount();

        //2 button classes, if user already follows show unfollow button and visa versa
        $buttonClass = $isFollowedTo ? "unfollow button" : "follow button";
        $action = "follow(\"$userTo\", \"$userLoggedIn\", this)";

        $button = ButtonProvider::createButton($buttonText, null, $action, $buttonClass);

        return "<div class='followButtonContainer'>
                    $button
                </div>";
    }
    
    // public static function createUserProfileNavigationButton($con, $username) {
    //     if(User::isLoggedIn()) {
    //         return ButtonProvider::createUserProfileButton($con, $username);
    //     }
    //     else {
    //         return "<a href='signIn.php'>
    //                     <span class='signInLink'>SIGN IN</span>
    //                 </a>";
    //     }
    // }

}
?>