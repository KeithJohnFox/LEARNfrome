<?php
class Post {
    //Creating private variables for this class
    private $user_obj;  //user variable
    private $con;   //connection variable

    //CONSTRUCTOR
    //This-> used to represent class variable in the function
    public function __construct($con, $user){   //Takes parameters connection and the user
        $this->con = $con;  //takes connection variable
        $this->user_obj = new User($con, $user);    //Instance of User Class to get user table from database
    }

    // Submitting A Post Code
    public function submitPost($body, $user_to) {   //Body is the text they posted, User_to is who they posted to or it will contain none if its to nobody
        $body = strip_tags($body);  //removes any html tags
        $body = mysqli_real_escape_string($this->con, $body);   //gets connection variable and grabs the body text variable
        $body = str_replace('\r\n', '\n', $body); //This allows posts to have line breaks, anytime it finds carried return followed by a line break (\r\n) it replaces it with \n
        $body = nl2br($body);   //nl2br (new line 2 break <br>) is a built in funtion that replaces new lines with line breaks

        $check_empty = preg_replace('/\s+/', '', $body);    //If there spaces in string this deletes them. /\s+/ slashes surrounds text to replace & s for spaces

        //This Code checks If Post is empty if not continues to do post
        if($check_empty != "") {
            //Current date and time
            $date_added = date("Y-m-d H:i:s");   //H: hours i: minutes s: seconds
            //Get username
            $added_by = $this->user_obj->getUsername();
            //If user is not on own profile, user_to is 'none'
            if($user_to == $added_by) {
                $user_to = "none";
            }

            //Insert post into the database
            //Id, text of message, added by, user tagged, date posted, user closed, deleted, likes
            $query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");
            //Returns Id of the post when submitted
            $returned_id = mysqli_insert_id($this->con);

            //Insert notification

            //Update post count for user
            $num_posts = $this->user_obj->getNumPosts();    //Gets number of posts from getNumPosts function in User Class
            $num_posts++;   //Increments Num posts By 1
            $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");  //Adds updated number of posts to user database

        }
    }

    //Loads all Posts in database related to user
    public function loadPostsFriends($data, $limit){
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();

        //If page = 1 (first page) start at 0
        //If page has been loaded start page -1 and print limit of 12
        if($page == 1){
            $start = 0;
        }
        else {
          $start = ($page - 1) * $limit;
        }

        $str = ""; //String to return
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");   //We gather all posts from users who accounts have not been deleted in descending order

        if(mysqli_num_rows($data_query) > 0) {

            $num_iterations = 0; //number of results checked not always posted
            $count = 1;

        //We are Assigning columns in the database for Posts into variables to use
          while($row = mysqli_fetch_array($data_query)) {
              //Variables relevant to post
              $id = $row['id'];
              $body = $row['body'];
              $added_by = $row['added_by'];
              $date_time = $row['date_added'];

              //Preparing user_to string so it can be used even if it is not posted to a user
              //This is for when you post on your own profile not on somebody elses, we set user_to to none then
              if($row['user_to'] == "none"){  //If the user_to equals to none (By default is in database) meaning user made a post on their own profile
                  $user_to = "";  //Then set user_to to equal to an empty string
              }
              //If user_to is set to another user
              else {
                  $user_to_obj = new User($con, $row['user_to']);     //Instance of Class passing connection var and the user_to name into a variable user to obj
                  $user_to_name = $user_to_obj->getFirstAndLastName();    //Stores first and last name of user to into variable user_to_name
                  $user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";     //Var user_to stores a href of the user to and the users name
              }

              //Checking if user that made a post has their account closed now
              $added_by_obj = new User($this->con, $added_by);  //Addedby object makes instance of User passing Connection var and the added_by var
              if($added_by_obj->isClosed()) {    //isClosed function checks if the database stores value yes or no and returns a boolean true or false
                  continue;   //continues loop again
              }

              //***CHECKS IF USER IS FRIENDS WITH USERNAME(By Running isFriend CLASS), IF TRUE EXECUTE POSTS OF FRIENDS ***
              $user_logged_obj = new User($this->con, $userLoggedIn);
              if($user_logged_obj->isFriend($added_by)){

                if($num_iterations++ < $start)
                  continue;

                //Once 10 posts have been loaded, then breaks
                if($count > $limit) {
                  break;
                }
                else {
                  $count++;
                }

                $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];

                ?>
                <script>
                  function toggle<?php echo $id; ?>() {

                    var target = $(event.target);
                    if(!target.is("a")){
                      var element = document.getElementById("toggleComment<?php echo $id; ?>");
                      //if the paragraph tags are showing hide display, else show block
                      if(element.style.display == "block"){
                          element.style.display = "none";
                      }
                      else{
                        element.style.display = "block";
                      }
                    }
                  }
                </script>
                <?php

                //TIMEFRAME OF POST LOGIC
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
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
                        $time_message = $interval->m . " month". $days;
                    }
                    else {
                        $time_message = $interval->m . " months". $days;
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

                //Outputs Profile Picture, Account details in a styling Box
                $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                            <div class='post_profile_pic'>
                                <img src='$profile_pic' width='50'>
                            </div>

                            <div class='posted_by' style='color:#ACACAC;'>
                                <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                            </div>
                            <div id='post_body'>
                                $body
                                <br>
                            </div>
                        </div>
                        <div class='post_comment' id='toggleComment$id' style='display:none;'>
                          <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                        </div>
                        <hr>";
              }
          } //ENDS IF FRIEND LOOP

          if($count > $limit)
            $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                        <input type='hidden' class='noMorePosts' value='false'>";
          else
            $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> All Posts Shown! </p>";

        }
        echo $str;
    }
}

?>
