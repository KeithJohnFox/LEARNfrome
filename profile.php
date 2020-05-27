<?php
//adding includes, cuts down on duplicated code, instead i recall the header eg nav bar to pages needed
include("includes/header.php");    //includes just pastes in code from header.php file
require_once("includes/classes/ProfileGenerator.php"); //This class handles output of html and css for (SOME) profile components 
require_once("includes/classes/ProfileData.php");


$message_obj = new Message($con, $userLoggedIn);
//This gets profile user from getUsername in User.php
if(isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    //Make a query to retrieve user information from users table
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
    //Here I store the users details into a user array to be called from
    $user_array = mysqli_fetch_array($user_details_query);
    // Show number of friends (you need -1 as you check for comma there is a , at the start table that needs to be minused or they will have extra friend)
    $num_friends = substr_count($user_array['friend_array'], ",") - 1;
}

//Instances of ProfileGenerator Class
$profileGenerator = new ProfileGenerator($con, $userLoggedInObj, $username);  
$profileGenerator->createUserDetails(); 

$profileData = new ProfileData($con, $username);

// $userobj = new User($con, $userLoggedIn); 
// $totalfollowers = $userobj->getFollowerCount(); 

//If the remove friend button is selected then remove friend
if(isset($_POST['remove_friend'])) {
    //gets the user whos logged in
    $user = new User($con, $userLoggedIn);
    
    //Call removeFriend function to remove user
    $user->removeFriend($username);
}

//If the add friend button is selected then send friend request
if(isset($_POST['add_friend'])) {
    //gets the user whos logged in
    $user = new User($con, $userLoggedIn);
    
    //Call sendRequest function to send request to user
    $user->sendRequest($username);
}

//If you select respond to request button it takes you to "your friend requests page"
if(isset($_POST['respond_request'])) {
    header("Location: requests.php");
}

if(isset($_POST['post_message'])) {
    if(isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($con, $_POST['message_body']);  //Security sanitization
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($username, $body, $date);
    }

    $link = '#profileTabs a[href="#messages_div"]';
    echo "<script> 
          $(function() {
              $('" . $link ."').tab('show');
          });
        </script>";

}

?>

<div class="wrapper">
  <style type="text/css">
    .wrapper{
      padding-left: 0px;
      margin-left: 0px;
    }
  </style>
  
  <!-- Left side profile Bar with user picture and details -->
  <div class="profile_left">
    <!-- Displays User Profile Image -->
    <div class="profile_img"> 
      <img src="<?php echo $user_array['profile_pic']; ?>" alt="User Profile Image">
    </div>
    <div class="profileUsername"> 
      <p><?php echo $username ?></p>
    </div>
    <!-- Under Profile Picture stats showing from database -->
    <div class="profile_info">  
      <p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
      <p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
      <p><?php echo "Friends: " . $num_friends ?></p>
    </div>

    <form action="<?php echo $username; ?>" method="POST">
      <?php 
      //User object to call the user
      $profile_user_obj = new User($con, $username); 
      //If statement calls isClosed function in User class, if function returns true then user is closed / deactivated
      if($profile_user_obj->isClosed()) {
        header("Location: user_closed.php");
      }

//------------------------Add OR REMOVE FRIEND BUTTON--------------------------------------
      //Remember to call function 1. include function class, 2. point to function using'->functionName(parameters)' 
        
      //Here I find out what user logged in
      $logged_in_user_obj = new User($con, $userLoggedIn); 

      //Here we only show add user button if you are on another profile not your own to add friend too
      if($userLoggedIn != $username) {
        //Check if you are friends with user profile selected
        //Call isFriend function to check this, then echo remove friend button if is friend
        if($logged_in_user_obj->isFriend($username)) {
          echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
        }
        //CHeck if that user sent you a friend request, respond friend request button
        else if ($logged_in_user_obj->didReceiveRequest($username)) {
          echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
        }
        //Displays friend request is sent message
        else if ($logged_in_user_obj->didSendRequest($username)) {
          echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
        }
        //If non above then display Add friend button
        else 
          echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';

      }

      ?>
    </form>
    <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something">  
      

      <?php
      if($userLoggedIn != $username) {
          echo '<div class="profile_info_bottom">';
          echo $logged_in_user_obj->getMutualFriends($username) . " Mutual friends";
          echo '</div>'; 
      }
      ?>

<!--      New feature div for side bar-->
      <div class="sidebar_features">
              <div class='quickProfileButtons'>
                  <div class='quickProfileButton'>
                      <a href='index.php'>
                          <img src='assets/images/icons/mainHome.png'>
                          <span>Home</span>
                      </a>
                  </div>
                  <div class='quickProfileButton'>
                      <a href='tutorials.php'>
                          <img src='assets/images/icons/tutIcon.png'>
                          <span>Tutorials</span>
                      </a>
                  </div>
                  <div class='quickProfileButton'>
                      <a href='trending.php'>
                          <img src='assets/images/icons/trendIcon.png'>
                          <span>Trending Tutorials</span>
                      </a>
                  </div>
                  <div class='quickProfileButton'>
                      <a href='followers.php'>
                          <img src='assets/images/icons/followersIcon.png'>
                          <span>Follower's Tutorials</span>
                      </a>
                  </div>
                  <div class='quickProfileButton'>
                      <a href='likedTutorials.php'>
                          <img src='assets/images/icons/likedIcon.png'>
                          <span>Liked Tutorials</span>
                      </a>
                  </div>
                  <div class='quickProfileButton'>
                      <a href='tutorials.php'>
                          <img src='assets/images/icons/settingsIcon.png'>
                          <span>Settings</span>
                      </a>
                  </div>
                  <div class='quickProfileButton'>
                      <a href='includes/handlers/logout.php'>
                          <img src='assets/images/icons/logoutIcon.png'>
                          <span>Logout</span>
                      </a>
                  </div>
              </div>
          
      </div>

  </div>
  <!-- top form box (To post statuses) -->

<div class="profile_main_column column">

    <ul class="nav nav-tabs" role="tablist" id="profileTabs">
        <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a></li>
        <li role='presentation'><a href='#videos' aria-controls='videos' role='tab' data-toggle='tab'>Tutorials</a></li>
        <?php 
          if($userLoggedIn != $username) {
            echo "<li role='presentation'><a href='#messages_div' aria-controls='messages_div' role='tab' data-toggle='tab'>Messages</a></li>";
          }
        ?> 
    </ul>

    <div class="tab-content">

        <div role="tabpanel" class="tab-pane active" id="newsfeed_div">
            <div class="posts_area"></div>
            <img id="loading" src="assets/images/icons/loadposts.gif">
        </div>

        <div role="tabpanel" class="tab-pane fade" id="messages_div">
            <?php
            echo "<h4>You and <a href='" . $username ."'>" . $profile_user_obj->getFirstAndLastName() . "</a></h4><hr><br>";
            echo "<div class='loaded_messages' id='scroll_messages'>";
            echo $message_obj->getMessages($username);
            echo "</div>";
            ?>

            <div class="message_post">
                <form action="" method="POST">
                    <textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>
                    <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
                </form>
            </div>

            <script>
                $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
                    var div = document.getElementById("scroll_messages");
                    div.scrollTop = div.scrollHeight;
                });
            </script>
        </div>

        <?php
          $videos = $profileData->getUsersVideos();
          
          if(sizeof($videos) > 0) {
            $videoGrid = new VideoGrid($con, $userLoggedInObj);
            $videoGridHtml = $videoGrid->create($videos, null, false);
          }
          else {
              $videoGridHtml = "<span>This user has no videos</span>";
          }

        ?>
        
        <!-- Tutorials Section -->
        <div role="tabpanel" class="tab-pane fade" id="videos">
          <?php
            echo $videoGridHtml; 
          ?> 
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="postModalLabel">Post something!</h4>
      </div>

      <div class="modal-body">
      	<p>This post will appear on the user's profile page and also their newsfeed for your others to see!</p>
      	<form class="profile_post" action="" method="POST">
      		<div class="form-group">
      			<textarea class="form-control" name="post_body"></textarea>
      			<input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
      			<input type="hidden" name="user_to" value="<?php echo $username; ?>">
      		</div>
      	</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
      </div>
    </div>
  </div>
</div>




    <!-- Infinite Scoll function -->
    <!-- Loads all posts to limit, with loading gif -->
<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $username; ?>';
    $(document).ready(function() {
        $('#loading').show();
        //Ajax request for loading first posts
        $.ajax({
            url: "includes/handlers/ajax_load_profile_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
            cache:false,

            success: function(data) {
                $('#loading').hide();
                $('.posts_area').html(data);
            }
        });
        $(window).scroll(function() {
            var height = $('.posts_area').height(); //Div containing posts
            var scroll_top = $(this).scrollTop();
            var page = $('.posts_area').find('.nextPage').val();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();

            if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                $('#loading').show();
                var ajaxReq = $.ajax({
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                    cache:false,

                    success: function(response) {
                        $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                        $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
                        $('#loading').hide();
                        $('.posts_area').append(response);
                    }
                });
            } //End if
            return false;
        }); //End (window).scroll(function())
    });
</script>
</div>
</body>
</html>
