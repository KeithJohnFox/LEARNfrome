<?php
require 'config/config.php';
include("includes/classes/User.php");    //Includes User Class to index page
include("includes/classes/Post.php");    //Includes Post Class to index page

//If this session variable is set make the userloggedIn variable username
//This stops users access the website without logging in
if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    //querys users information into user var for user name to display on nav bar in the nav section
    $user_details_query = mysqli_query($con , "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
}
else {
    // Username is not set, send them back to register page
    header("Location: register.php");
}
?>

<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
<!--    comment section styling of font only - other styling in style.css file-->
    <style type="text/css">
        /*Star means all*/
        *{
            font-size: 11px;
            font-family: Arial, Helvetica, Sans-serif;
        }
    </style>

    <!-- JavaScript -->
    <script>
        function toggle() {
            var element = document.getElementById("comment_section");
            //if the paragraph tags are showing hide display, else show block
            if(element.style.display == "block"){
                element.style.display = "none";
            }
            else {
                element.styledisplay = "block;"
            }
        }
    </script>
    <?php
        //This gets id of post
        if(isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];
        }
        $user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id = '$post_id'");
        $row = mysqli_fetch_array($user_query);
        $posted_to = $row['added_by'];
        if(isset($_POST['postComment' . $post_id])) {
            $post_body = $_POST['post_body'];
            $post_body = mysqli_escape_string($con, $post_body);
            $date_time_now = date("Y-m-d H:i:s");
            $insert_post = mysqli_query($con, "INSERT INTO comments VALUES (NULL, '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");
            echo "<p>Comment Posted! </p>";
        }
    ?>
    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
        <textarea name="post_body"></textarea>
        <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
    </form>


    <!-- Loads comments on Posts in Profile Page -->
    <?php
        //Query gets comments by id in order and store in variable
        $get_postedComments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
        //Count takes number of rows in id of posts
        $count = mysqli_num_rows($get_postedComments);

        //If there are rows containing comments from count variable
        if($count != 0){
            //while there are comments set in array from query variable
            while($comments = mysqli_fetch_array($get_postedComments)) {
                //Loop through each row and store column name in tabe into the variables below
                $comment_body = $comments['post_body'];
                $posted_to = $comments['posted_to'];
                $posted_by = $comments['posted_by'];
                $date_added = $comments['date_added'];
                $removed = $comments['removed'];


                //TIMEFRAME OF POST LOGIC
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_added); //Time of post
                $end_date = new DateTime($date_time_now); //Current time
                $interval = $start_date->diff($end_date); //Difference between two dates

                //Printing How old post is in Years, Months and days
                //If post is older then 1 year or more
                if($interval->y >= 1) {
                    if($interval == 1)
                        $time_message = $interval->y . " year ago"; // 1 year ago
                    else
                        $time_message = $interval->y . " years ago"; // more then 1 year
                }
                //If Post is less than 1 year
                else if ($interval->m >= 1) {
                    //How many days old
                    if($interval->d == 0) { //Less then a day old
                        $days = " ago";
                    }
                    else if($interval->d == 1) {
                        $days = $interval->d . " days ago";
                    }
                    else {
                        $days = $interval->d . " day ago";
                    }

                    //How many months old
                    if($interval->m == 1) {
                        $time_message = $interval->m . " month ". $days;
                    }
                    else {
                        $time_message = $interval->m . " months ". $days;
                    }
                }
                //Post at least a day old
                else if ($interval->d >= 1) {
                    if($interval->d == 1) {
                        $time_message = "Yesterday";
                    }
                    else {
                        $time_message = $interval->d . " days ago";
                    }
                }
                //Post is Hour or more old
                else if($interval->h >= 1) {
                    if($interval->h == 1) {
                        $time_message = $interval->h . " hour ago";
                    }
                    else {
                        $time_message = $interval->h . " hours ago";
                    }
                }
                //Post is Minute or more old
                else if($interval->i >= 1) {
                    if($interval->i == 1) {
                        $time_message = $interval->i . " minute ago";
                    }
                    else {
                        $time_message = $interval->i . " minutes ago";
                    }
                }
                //Post is just now(by 30 seconds) or 30+ seconds ago
                else {
                    if($interval->s < 30) {
                        $time_message = "Just now";
                    }
                    else {
                        $time_message = $interval->s . " seconds ago";
                    }
                }

                $user_obj = new User($con, $posted_by);
                ?>

                <!--Comment Frame Under Posts-->
                <div class="comment_section">
                    <!--    Display User Profile Pic of comment-->
                    <a href="<?php echo $posted_by?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic();?>" title="<?php echo $posted_by; ?>" style="float:left;" height="30"></a>
                    <!--    Display Users First and Last Name-->
                    <a href="<?php echo $posted_by?>" target="_parent"> <b> <?php echo $user_obj->getFirstAndLastName(); ?> </b></a>
                    &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?>
                    <hr>
                </div>
                <?php
            }
        }
        else{
            echo "<div style=\"text-align: center;\"><br><br>No Comments Yet!</div>";
        }
    ?>

</body>
</html>
