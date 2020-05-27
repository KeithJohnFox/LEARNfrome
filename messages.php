<?php 
include("includes/header.php");
require_once("includes/Classes/ProfileData.php"); 

//New Message Object for Message class
$messageObject = new Message($con, $userLoggedIn);

if(isset($_GET['u']))
	$user_to = $_GET['u'];
else {
    //This will retrieve the most recent user you have interacted with (By calling function in Message class)
	$user_to = $messageObject->getMostRecentUser();
	//If it doesnt find anyone meaning "false", then user to is sending new message 'new'
	if($user_to == false)
		$user_to = 'new';
}
//If user is not new then create a new user object
if($user_to != "new")
	$user_to_obj = new User($con, $user_to);

if(isset($_POST['post_message'])) {

	if(isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$messageObject->sendMessage($user_to, $body, $date);
	}

}

$profileData = new ProfileData($con, $userLoggedIn);
$followerCount = $profileData->getFollowerCount();

?>
<div class="messageContainer">
	<div class="secMessage">
        <div id="userBox" class="user_details column">
            <!-- href will direct to the profile page because of the .htaccess file allowing username in url to link to their profile -->
            <a href="<?php echo $userLoggedIn; ?>"> <img src="<?php echo $user['profile_pic']; ?>" alt="User Profile Picture"></a>

            <div class="user_details_left_right">
                <a href="<?php echo $userLoggedIn; ?>">
                <?php
                    echo $user['first_name'] . " " . $user['last_name'];
                ?>
                </a>
                <br>
                <?php echo "Followers: " . $followerCount . "<br>" .
                        "Posts: " . $user['num_posts']. "<br>" .
                        "Likes: " . $user['num_likes'] . "<br>";
                ?>
                </div>
        </div>
  </div>

 <!--If it is a new user / new message to user-->
 <div class="main_column column" id="main_column">
   <?php  
   if($user_to != "new"){
     echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr><br>";
           //Load Messages div / echo message from getMessage Function in Message Class
     echo "<div class='loaded_messages' id='scroll_messages'>";
       echo $messageObject->getMessages($user_to);
     echo "</div>";
   }
   else {
     echo "<h4>New Message</h4>";
   }
   ?>


<div class="message_post">
        <form action="" method="POST">
            <?php
            if($user_to == "new") {
                echo "Select the friend you would like to message <br><br>";
                ?>
                To: <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='seach_text_input'>

                <?php
                echo "<div class='results'></div>";
            }
            else {
                echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
                echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
            }

            ?>
        </form>

</div>
 <!--        Script tag shows the latest messages by scrolling conversation at the bottom to show most recent messages-->
   <script>
     var div = document.getElementById("scroll_messages");
     div.scrollTop = div.scrollHeight;
   </script>

 </div>

 <!--    This code Get latest conversation with a user and display in a box with new message of that convo-->
 <div class="user_details column" id="conversations">
     <h4>Conversations</h4>

     <div class="loaded_conversations">
       <?php echo $messageObject->getConvos(); ?>
     </div>
     <br>
     <a href="messages.php?u=new">New Message</a>

   </div>
</div>
</div>
