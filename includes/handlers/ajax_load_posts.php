<!-- Infinite Scrolling for statuses on Profile Page using Ajax -->
<!-- **Ajax allows you to make database calls with reloading the page** -->
<?php
  //All files needed are included here
  include("../../config/config.php");
  include("../classes/Post.php");
  include("../classes/User.php");


  $limit = 8; // Loads the number of posts that are called to the page by limit

  //Instance of Post Class
  $posts = new Post($con, $_REQUEST['userLoggedIn']); //gets connection & checks if the user is logged in.
  $posts->loadPostsFriends($_REQUEST, $limit); // gets posts of users friends
 ?>
