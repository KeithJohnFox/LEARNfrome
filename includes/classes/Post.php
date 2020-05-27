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
    public function submitPost($body, $user_to, $imageName) {   //Body is the text they posted, User_to is who they posted to or it will contain none if its to nobody
        //Secuirty Sanitization to string
        $body = strip_tags($body);  //removes any html tags
        $body = mysqli_real_escape_string($this->con, $body);   //gets connection variable and grabs the body text variable
        $body = str_replace('\r\n', '\n', $body); //This allows posts to have line breaks, anytime it finds carried return followed by a line break (\r\n) it replaces it with \n
        $body = nl2br($body);   //nl2br (new line 2 break <br>) is a built in funtion that replaces new lines with line breaks

        $check_empty = preg_replace('/\s+/', '', $body);    //If there spaces in string this deletes them. /\s+/ slashes surrounds text to replace & s for spaces

        //This Code checks If Post is empty if not continues to do post
        if($check_empty != "" || $imageName != "") {


            //Regex (code allows youtube videos to be posted on newsfeed
            $body_array = preg_split("/\s+/", $body); //Regex "/\s+/" splits up the spaces

            foreach($body_array as $key => $value) { //key keeps track of what number the element is
                //if value contains www.youtube.com/watch?v= which is always in a youtube link then we know its a youtube video
                //In order to play youtube video in website we need to use embed in the link so it can be played within an iframe, so we need to replace watch?v= with embed/
                if(strpos($value, "www.youtube.com/watch?v=") !== false) {
                    $link = preg_split("!&!", $value);
                    $value = preg_replace("!watch\?v=!", "embed/", $link[0]);
                    //iframe styling
                    $value = "<br><br><br><iframe width=\'640\' height=\'360\' frameBorder=\'0\' src=\'" . $value . "\'></iframe><br>";
                    //body array at position key in loop equals value var
                    $body_array[$key] = $value;
                }
            }
            //This saves the body array to the original body var
            $body = implode(" ", $body_array);

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
            $query = mysqli_query($this->con, "INSERT INTO posts VALUES(NULL, '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");


            //Returns Id of the post when submitted
            $returned_id = mysqli_insert_id($this->con);

            //Insert notification
            if($user_to != 'none') {
                $notification = new Notification($this->con, $added_by);
                //sends id of post submitted to insertNotification function
                $notification->insertNotification($returned_id, $user_to, "profile_post");
            }

            //Update post count for user
            $num_posts = $this->user_obj->getNumPosts();    //Gets number of posts from getNumPosts function in User Class
            $num_posts++;   //Increments Num posts By 1
            $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");  //Adds updated number of posts to user database

            //Stop words are words that have no meaning or real value to bbe displayed as trending
            //I copied this list off the udemy course content
            $stopWords = "a about above across after again am against all almost alone along already
			 also although always among am an and another any anybody anyone anything anywhere are 
			 area areas around as ask asked asking asks at away b back backed backing backs be became
			 because become becomes been before began behind being beings best better between big 
			 both but by c came can cannot case cases certain certainly clear clearly come could
			 d did differ different differently do does done down down downed downing downs during
			 e each early either end ended ending ends enough even evenly ever every everybody
			 everyone everything everywhere f face faces fact facts far felt few find finds first
			 for four from full fully further furthered furthering furthers g gave general generally
			 get gets give given gives go going good goods got great greater greatest group grouped
			 grouping groups h had has have having he her here herself high higher
		     highest him himself his how however i im if important in interest interested interesting
			 interests into is it its itself j just k keep keeps kind knew know known knows
			 large largely last later latest least less let lets like likely long longer
			 longest m made make making man many may me member members men might more most
			 mostly mr mrs much must my myself n necessary need needed needing needs never
			 new new newer newest next no nobody non noone not nothing now nowhere number
			 numbers o of off often old older oldest on once one only open opened opening
			 opens or order ordered ordering orders other others our out over p part parted
			 parting parts per perhaps place places point pointed pointing points possible
			 present presented presenting presents problem problems put puts q quite r
			 rather really right right room rooms s said same saw say says second seconds
			 see seem seemed seeming seems sees several shall she should show showed
			 showing shows side sides since small smaller smallest so some somebody
			 someone something somewhere state states still still such sure t take
			 taken than that the their them then there therefore these they thing
			 things think thinks this those though thought thoughts three through
	         thus to today together too took toward turn turned turning turns two
			 u under until up upon us use used uses v very w want wanted wanting
			 wants was way ways we well wells went were what when where whether
			 which while who whole whose why will with within without work
			 worked working works would x y year years yet you young younger
			 youngest your yours z lol haha omg hey ill iframe wonder else like 
             hate sleepy reason for some little yes bye choose";

            //Convert stop words into array - split at white space
            $stopWords = preg_split("/[\s,]+/", $stopWords);

            //Remove all punctionation
            $no_punctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

            //Predict whether user is posting a url. If so, do not check for trending words
            if(strpos($no_punctuation, "height") === false && strpos($no_punctuation, "width") === false
                && strpos($no_punctuation, "http") === false && strpos($no_punctuation, "youtube") === false){
                //Convert users post (with punctuation removed) into array - split at white space
                $keywords = preg_split("/[\s,]+/", $no_punctuation);

                //Loop through each word in stop words string
                foreach($stopWords as $value) {
                    foreach($keywords as $key => $value2){
                        if(strtolower($value) == strtolower($value2))
                            $keywords[$key] = "";
                    }
                }

                foreach ($keywords as $value) {
                    $this->calculateTrend(ucfirst($value));
                }

            }


        }
    }

    //Calculate what words are trending most
    public function calculateTrend($term) {

        if($term != '') {
            // $query = mysqli_query($this->con, "SELECT * FROM trends WHERE title='$term'");

            $stmt = $this->con->prepare("SELECT * FROM trends WHERE title= ?");
			$stmt->bind_param("s", $term);
            $stmt->execute();
            $query = $stmt->get_result();

            // Add word into table if it has not been added yet
            if(mysqli_num_rows($query) == 0){
                $insert_query = $this->con->prepare("INSERT INTO trends(title,hits) VALUES(?, '1')");
                $insert_query->bind_param("s", $term);
                $insert_query->execute();
            }   
            else {
                // if the word already exist then add 1 to number of hits for that word
                $insert_query = $this->con->prepare("UPDATE trends SET hits=hits+1 WHERE title= ?");
                $insert_query->bind_param("s", $term);
                $insert_query->execute();
            }
        }

    }

    //Load Posts on user's profile page
    public function loadProfilePosts($data, $limit) {

        $page = $data['page'];
        $profileUser = $data['profileUsername'];
        $userLoggedIn = $this->user_obj->getUsername();

        if($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;


        $str = ""; //String to return

        $stmt = $this->con->prepare("SELECT * FROM posts WHERE deleted='no' AND ((added_by= ? AND user_to='none') OR user_to= ?)  ORDER BY id DESC");
        $stmt->bind_param("ss", $profileUser, $profileUser);
        $stmt->execute();
        $result = $stmt->get_result();
        $query = mysqli_num_rows($result);

        if($query > 0) {

            $num_iterations = 0; //Number of results checked (not necasserily posted)
            $count = 1;

            while($row = mysqli_fetch_array($result)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                if($num_iterations++ < $start)
                    continue;

                //Once 10 posts have been loaded, break
                if($count > $limit) {
                    break;
                }
                else {
                    $count++;
                }

                if($userLoggedIn == $added_by)
                    $delete_button = "<button class='delete_button' id='post$id'>X</button>";
                else
                    $delete_button = "";
                
                $stmt = $this->con->prepare("SELECT first_name, last_name, profile_pic FROM users WHERE username= ?");
                $stmt->bind_param("s", $added_by);
                $stmt->execute();
                $user_details_query = $stmt->get_result();

                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];


                ?>
                <script>
                    function toggle<?php echo $id; ?>() {

                        var target = $(event.target);
                        if (!target.is("a")) {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if(element.style.display == "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }
                    }

                </script>
                <?php
                //Outputs all posts on user profile
                $stmt = $this->con->prepare("SELECT * FROM comments WHERE post_id= ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $comments_check = $stmt->get_result();
                
                $comments_check_num = mysqli_num_rows($comments_check);


                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
                $end_date = new DateTime($date_time_now); //Current time
                $interval = $start_date->diff($end_date); //Difference between dates
                if($interval->y >= 1) {
                    if($interval == 1)
                        $time_message = $interval->y . " year ago"; //1 year ago
                    else
                        $time_message = $interval->y . " years ago"; //1+ year ago
                }
                else if ($interval->m >= 1) {
                    if($interval->d == 0) {
                        $days = " ago";
                    }
                    else if($interval->d == 1) {
                        $days = $interval->d . " day ago";
                    }
                    else {
                        $days = $interval->d . " days ago";
                    }


                    if($interval->m == 1) {
                        $time_message = $interval->m . " month". $days;
                    }
                    else {
                        $time_message = $interval->m . " months ". $days;
                    }

                }
                else if($interval->d >= 1) {
                    if($interval->d == 1) {
                        $time_message = "Yesterday";
                    }
                    else {
                        $time_message = $interval->d . " days ago";
                    }
                }
                else if($interval->h >= 1) {
                    if($interval->h == 1) {
                        $time_message = $interval->h . " hour ago";
                    }
                    else {
                        $time_message = $interval->h . " hours ago";
                    }
                }
                else if($interval->i >= 1) {
                    if($interval->i == 1) {
                        $time_message = $interval->i . " minute ago";
                    }
                    else {
                        $time_message = $interval->i . " minutes ago";
                    }
                }
                else {
                    if($interval->s < 30) {
                        $time_message = "Just now";
                    }
                    else {
                        $time_message = $interval->s . " seconds ago";
                    }
                }

                $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>";

                ?>
                <script>

                    $(document).ready(function() {

                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("Are you sure you want to delete this post?", function(result) {

                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

                                if(result)
                                    location.reload();

                            });
                        });


                    });

                </script>
                <?php

            } //End while loop

            if($count > $limit)
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
            else
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
        }

        echo $str;


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

        //We gather all posts from users who accounts have not been deleted in descending order
        $stmt = $this->con->prepare("SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
        $stmt->execute();
        $data_query = $stmt->get_result();
        
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
              $imagePath = $row['image'];

              //Preparing user_to string so it can be used even if it is not posted to a user
              //This is for when you post on your own profile not on somebody elses, we set user_to to none then
              if($row['user_to'] == "none"){  //If the user_to equals to none (By default is in database) meaning user made a post on their own profile
                  $user_to = "";  //Then set user_to to equal to an empty string
              }
              //If user_to is set to another user
              else {
                  $user_to_obj = new User($this->con, $row['user_to']);     //Instance of Class passing connection var and the user_to name into a variable user to obj
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

                $stmt = $this->con->prepare("SELECT first_name, last_name, profile_pic FROM users WHERE username= ?");
                $stmt->bind_param("s", $added_by);
                $stmt->execute();
                $user_details_query = $stmt->get_result();

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

                  $stmt = $this->con->prepare("SELECT * FROM comments WHERE post_id= ?");
                  $stmt->bind_param("i", $id);
                  $stmt->execute();
                  $comments_check = $stmt->get_result();
                  $comments_check_num = mysqli_num_rows($comments_check);

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

                //If there is an image upload to post create a div with image path
                if($imagePath != ""){
                    $imageDiv = "<div class='postImage'>
                                    <img src='$imagePath'>
                                </div>";
                }
                else {
                    $imageDiv = "";
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
                                $imageDiv   
                                <br>
                                <br>
                            </div>
                            <div class='newsfeedPostOptions'>
                                Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
                                <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
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

    public function getSinglePost($post_id){
        //Get user logged in object
        $userLoggedIn = $this->user_obj->getUsername();

        //query to update notifications to opened through id
        $opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

        $str = ""; //String return
        //get all posts that have not been deleted
        $stmt = $this->con->prepare("SELECT * FROM posts WHERE deleted='no' AND id= ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $data_query = $stmt->get_result();

        if(mysqli_num_rows($data_query) > 0) {
            $row = mysqli_fetch_array($data_query);
            $id = $row['id'];
            $body = $row['body'];
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];

            //Prepare user_to string so it can be included even if not posted to a user
            if($row['user_to'] == "none") {
                $user_to = "";
            }
            else {
                $user_to_obj = new User($this->con, $row['user_to']);
                $user_to_name = $user_to_obj->getFirstAndLastName();
                $user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
            }

            //Check if user who posted, has their account closed
            $added_by_obj = new User($this->con, $added_by);
            if($added_by_obj->isClosed()) {
                return;
            }

            $user_logged_obj = new User($this->con, $userLoggedIn);
            if($user_logged_obj->isFriend($added_by)){


                if($userLoggedIn == $added_by)
                    $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
                else
                    $delete_button = "";


                $stmt = $this->con->prepare("SELECT first_name, last_name, profile_pic FROM users WHERE username= ?");
                $stmt->bind_param("s", $added_by);
                $stmt->execute();
                $user_details_query = $stmt->get_result();


                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];

                ?>
                <script>
                    function toggle<?php echo $id; ?>() {
                        var target = $(event.target);
                        if (!target.is("a")) {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if(element.style.display == "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }
                    }
                </script>
                <?php
                
                //Retreives comments on posts
                $stmt = $this->con->prepare("SELECT * FROM comments WHERE post_id= ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $comments_check = $stmt->get_result();

                $comments_check_num = mysqli_num_rows($comments_check);

                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
                $end_date = new DateTime($date_time_now); //Current time
                $interval = $start_date->diff($end_date); //Difference between dates
                if($interval->y >= 1) {
                    if($interval == 1)
                        $time_message = $interval->y . " year ago"; //1 year ago
                    else
                        $time_message = $interval->y . " years ago"; //1+ year ago
                }
                else if ($interval->m >= 1) {
                    if($interval->d == 0) {
                        $days = " ago";
                    }
                    else if($interval->d == 1) {
                        $days = $interval->d . " day ago";
                    }
                    else {
                        $days = $interval->d . " days ago";
                    }


                    if($interval->m == 1) {
                        $time_message = $interval->m . " month ". $days;
                    }
                    else {
                        $time_message = $interval->m . " months ". $days;
                    }

                }
                else if($interval->d >= 1) {
                    if($interval->d == 1) {
                        $time_message = "Yesterday";
                    }
                    else {
                        $time_message = $interval->d . " days ago";
                    }
                }
                else if($interval->h >= 1) {
                    if($interval->h == 1) {
                        $time_message = $interval->h . " hour ago";
                    }
                    else {
                        $time_message = $interval->h . " hours ago";
                    }
                }
                else if($interval->i >= 1) {
                    if($interval->i == 1) {
                        $time_message = $interval->i . " minute ago";
                    }
                    else {
                        $time_message = $interval->i . " minutes ago";
                    }
                }
                else {
                    if($interval->s < 30) {
                        $time_message = "Just now";
                    }
                    else {
                        $time_message = $interval->s . " seconds ago";
                    }
                }

                $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>";

                ?>

                <script>
                    $(document).ready(function() {
                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("Are you sure you want to delete this post?", function(result) {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
                                if(result)
                                    location.reload();
                            });
                        });
                    });
                </script>
                <?php
            }
            else {
                echo "<p>You cannot see post because you are not currently friends with the user.</p>";
                return;
            }
        }
        else {
            echo "<p>No post found.</p>";
            return;
        }
        echo $str;
    }
}

?>
