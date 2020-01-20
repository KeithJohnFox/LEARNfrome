<?php
//adding includes, cuts down on duplicated code, instead i recall the header eg nav bar to pages needed
include("includes/header.php");    //includes just pastes in code from header.php file

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

?>

  <style type="text/css">
    .wrapper{
      padding-left: 0px;
      margin-left: 0px;
    }
  </style>

  <!-- Left side profile Bar with user picture and details -->
  <div class="profile_left">
    <!-- Displays User Profile Image -->
    <img src="<?php echo $user_array['profile_pic']; ?>" alt="User Profile Image">

    <!-- Under Profile Picture stats showing from database -->
    <div class="profile_info">
      <p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
      <p><?php echo "Likes: " . $user_array['num_posts']; ?></p>
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
    
    <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Create a Post">

  </div>
  <!-- top form box (To post statuses) -->
  <div class="main_column column">
    <?php echo $username ?>

  </div>

    <!-- Modal -->
    <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create a Post!</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <p>Posts will be visible on your profile & newsfeed for friends to see. </p>
            <form class="profile_post" action=""></form>
            <div class="form-group">
              <textarea class="form-control" name="post_body"></textarea>  
              <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">  
              <input type="hidden" name="user_to" value="<?php echo $username; ?>">  
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Infinite Scoll function -->
    <!-- Loads all posts to limit, with loading gif -->
<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(document).ready(function() {

        $('#loading').show();

        //Ajax request for loading first posts
        $.ajax({
            url: "includes/handlers/ajax_load_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn,
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
                    url: "includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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
