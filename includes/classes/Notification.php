<?php
class Notification {
    //Private variables
    private $user_obj;
    private $con;

    //Constructor (connection and user details
    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getUnreadNumber() {
        $userLoggedIn = $this->user_obj->getUsername();
        $stmt = $this->con->prepare("SELECT * FROM notifications WHERE viewed='no' AND user_to= ?");
        $stmt->bind_param("s", $userLoggedIn);
        $stmt->execute();
        $query = $stmt->get_result();

        return mysqli_num_rows($query);
    }

    public function getNotifications($data, $limit) {

        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";

        if($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;

        //Query that updates notifications to viewed if opened
        $set_viewed_query = $this->con->prepare("UPDATE notifications SET viewed='yes' WHERE user_to= ?");
        $set_viewed_query->bind_param("s", $userLoggedIn);
        $set_viewed_query->execute();

        //Gets all from notifications that = username logged in
        $stmt = $this->con->prepare("SELECT * FROM notifications WHERE user_to= ? ORDER BY id DESC");
        $stmt->bind_param("s", $userLoggedIn);
        $stmt->execute();
        $query = $stmt->get_result();

        //If there are no notifications print no notifications
        if(mysqli_num_rows($query) == 0) {
            echo "You have no notifications!";
            return;
        }

        $num_iterations = 0; //Number of messages checked
        $count = 1; //Number of messages posted

        //Loop through array query of notifications to display
        while($row = mysqli_fetch_array($query)) {

            if($num_iterations++ < $start)
                continue;

            //Stops at limit of 7 posts, declared in ajax notifications file
            if($count > $limit)
                break;
            else
                $count++;

            //store results in column user from in "user from" variable
            $user_from = $row['user_from'];

            //Store all usernames that are related to the notifications
            $stmt = $this->con->prepare("SELECT * FROM users WHERE username= ?");
            $stmt->bind_param("s", $user_from);
            $stmt->execute();
            $user_data_query = $stmt->get_result();


            //Store in array called user data
            $user_data = mysqli_fetch_array($user_data_query);


            //Timeframe
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($row['datetime']); //Time of post
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
                    $time_message = $interval->m . " months". $days;
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

            //If notification is not opened display blue background colour to indicate notification is not opened
            $opened = $row['opened'];
            $style = ($opened == 'no') ? "background-color: #DDEDFF;" : "";

            //Div displays notification details in drop down menu on nav bar
            $return_string .= "<a href='" . $row['link'] . "'> 
									<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
										<div class='notificationsProfilePic'>
											<img src='" . $user_data['profile_pic'] . "'>
										</div>
										<p class='timestamp_smaller' id='grey'>" . $time_message . "</p>" . $row['message'] . "
									</div>
								</a>";
        }


        //If posts were loaded
        if($count > $limit)
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
        else
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more notifications!</p>";

        return $return_string;
    }

    public function insertNotification($post_id, $user_to, $type) {

        $userLoggedIn = $this->user_obj->getUsername();
        $userLoggedInName = $this->user_obj->getFirstAndLastName();

        $date_time = date("Y-m-d H:i:s");

        //Types of notifications selected by switch statement
        //Outputs username and message
        switch($type) {
            case 'comment':
                $message = $userLoggedInName . " commented on your post";
                break;
            case 'like':
                $message = $userLoggedInName . " liked your post";
                break;
            case 'profile_post':
                $message = $userLoggedInName . " posted on your profile";
                break;
            case 'comment_non_owner':
                $message = $userLoggedInName . " commented on a post you commented on";
                break;
            case 'profile_comment':
                $message = $userLoggedInName . " commented on your profile post";
                break;
        }

        //Page name of post refered too
        $link = "post.php?id=" . $post_id;

        //Query to insert the notification into database
        // $insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES(NULL, '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");

        $insert_query = $this->con->prepare("INSERT INTO notifications VALUES(NULL, ?, ?, ?, ?, ?, 'no', 'no')"); //THIS EDITITED with 0, commentId
        $insert_query->bind_param("sssss", $user_to, $userLoggedIn, $message, $link, $date_time);
        // $insert_query->execute();

        $success = $insert_query->execute();

        if(!$success) {
            echo "Error: " . mysqli_error($this->con);
            return false;
        }
    }

}

?>

