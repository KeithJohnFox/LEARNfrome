<?php
class Message {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	//Gets the most recent user for Messages
	public function getMostRecentUser() {
		$userLoggedIn = $this->user_obj->getUsername();
        //Query to retrieve a message from the user whom is logged in (userLoggedIn var) where the message is sent to or from by that user
		$query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to ='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1");
        //If no messages return false
		if(mysqli_num_rows($query) == 0)
			return false;
        //stores query into array named row
		$row = mysqli_fetch_array($query);
		//store user to column in databse row into var user to
		$user_to = $row['user_to'];
		//Same for user from
		$user_from = $row['user_from'];
        //return user_to or from of which is NOT the userLogged in
		if($user_to != $userLoggedIn)
			return $user_to;
		else 
			return $user_from;

	}
    //Sends a Message
	public function sendMessage($user_to, $body, $date) {
        //If there is text
		if($body != "") {
			$userLoggedIn = $this->user_obj->getUsername();
		
			//SANITIZATION FOR MESSAGES
			$body = strip_tags($body);  //removes any html tags
			$body = mysqli_real_escape_string($this->con, $body); //escapes special characters in a string for use in an SQL query
			   
			//Query Message to the database containing(receipient user, username of user, the text, date sent)
			$query = $this->con->prepare("INSERT INTO messages VALUES(NULL, ?, ?, ?, ?, 'no', 'no', 'no')");
			$query->bind_param("ssss", $user_to, $userLoggedIn, $body, $date);
			$query->execute();

		}
	}

	//GET messages function
	public function getMessages($otherUser) {
	    //First get user who is logged in
		$userLoggedIn = $this->user_obj->getUsername();
		$data = "";

        //This Query sets the message to opened as the message will be loaded next meaning its opened
		$query = $this->con->prepare("UPDATE messages SET opened='yes' WHERE user_to= ? AND user_from= ?");
        $query->bind_param("ss", $userLoggedIn, $otherUser);
        $query->execute();

        //Query to get all messages from both user to and from
		$stmt = $this->con->prepare("SELECT * FROM messages WHERE (user_to= ? AND user_from= ?) OR (user_from= ? AND user_to= ?)");
        $stmt->bind_param("ssss", $userLoggedIn, $otherUser, $userLoggedIn, $otherUser);
        $stmt->execute();
        $result = $stmt->get_result();
        
		//Loop through the array and output all messages and the user to and from
		while($row = mysqli_fetch_array($result)) {
			$user_to = $row['user_to'];
			$user_from = $row['user_from'];
			$body = $row['body'];

			//Conditional Statement, (If user to is userlogged in then div top = the div after the ?, else output div after : ) same as if else
			$div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
			//Data combbines top div and comines body(message)
			$data = $data . $div_top . $body . "</div><br><br>";
		}
		return $data;
	}

	public function getLatestMessage($userLoggedIn, $user2) {
		$details_array = array();

        //Query messages from descending order
		$stmt = $this->con->prepare("SELECT body, user_to, date FROM messages WHERE (user_to= ? AND user_from= ?) OR (user_to= ? AND user_from= ?) ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("ssss", $userLoggedIn, $user2, $user2, $userLoggedIn);
        $stmt->execute();
        $query = $stmt->get_result();

		$row = mysqli_fetch_array($query);
		$sent_by = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";

		//Timeframe
		$date_time_now = date("Y-m-d H:i:s");
		$start_date = new DateTime($row['date']); //Time of post
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

		array_push($details_array, $sent_by);
		array_push($details_array, $row['body']);
		array_push($details_array, $time_message);

		return $details_array;
	}

	public function getConvos() {
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = array();

		//Select user_to or user_from in messages table that contains the user logged in
		$stmt = $this->con->prepare("SELECT user_to, user_from FROM messages WHERE user_to= ? OR user_from= ? ORDER BY id DESC");
        $stmt->bind_param("ss", $userLoggedIn, $userLoggedIn);
        $stmt->execute();
        $query = $stmt->get_result();

		//Loop through query array and if user to not logged in user then push that user_to else push user from in the table
        //Essentially we are pushing user that sent or recieved a message that is not the user logged in so we can display that users name with message
		while($row = mysqli_fetch_array($query)) {
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

			//Here I just state if the user is not already in the array then insert user if user is already in array dont insert again
            //not in array(needle, haystack)
			if(!in_array($user_to_push, $convos)) {
			    //push array (array, var)
				array_push($convos, $user_to_push);
			}
		}

		//Each time we iterate, the username will be reference to the convo
		foreach($convos as $username) {
//		    user found obj is instance of user class
			$user_found_obj = new User($this->con, $username);
			//we call getLastestMessage function and get the message relaated to the paramaters 
			$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

			//MESSAGE PREVIEW: This cuts down the message into 12 characters and then add dots to indicate theres more to the message
			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots; 

			//Outputs div wither user profile , users name and their latest message
			$return_string .= "<a href='messages.php?u=$username'> <div class='user_found_messages'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
								" . $user_found_obj->getFirstAndLastName() . "
								<span class='timestamp_smaller' id='grey'> " . $latest_message_details[2] . "</span>
								<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
								</div>
								</a>";
		}

		return $return_string;

	}

	public function getConvosDropdown($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = array();

		if($page == 1)
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$set_viewed_query = mysqli_query($this->con, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn'");

		$stmt = $this->con->prepare("SELECT user_to, user_from FROM messages WHERE user_to= ? OR user_from= ? ORDER BY id DESC");
        $stmt->bind_param("ss", $userLoggedIn, $userLoggedIn);
        $stmt->execute();
        $query = $stmt->get_result();

		while($row = mysqli_fetch_array($query)) {
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

			if(!in_array($user_to_push, $convos)) {
				array_push($convos, $user_to_push);
			}
		}

		$num_iterations = 0; //Number of messages checked 
		$count = 1; //Number of messages posted

		foreach($convos as $username) {

			if($num_iterations++ < $start)
				continue;

			if($count > $limit)
				break;
			else 
				$count++;
			
			$stmt = $this->con->prepare("SELECT opened FROM messages WHERE user_to= ? AND user_from= ? ORDER BY id DESC");
			$stmt->bind_param("ss", $userLoggedIn, $username);
			$stmt->execute();
			$is_unread_query = $stmt->get_result();

			$row = mysqli_fetch_array($is_unread_query);
			$style = ($row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";

			$user_found_obj = new User($this->con, $username);
			$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots; 

			$return_string .= "<a href='messages.php?u=$username'> 
								<div class='user_found_messages' style='" . $style . "'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
								" . $user_found_obj->getFirstAndLastName() . "
								<br>
								<span class='timestamp_smaller' id='grey'> " . $latest_message_details[2] . "</span>
								
								<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
								</div>
								</a>";
		}

		//If posts were loaded
		if($count > $limit)
			$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else 
			$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more messages to load!</p>";

		return $return_string;
	}

	//Retrieves number of unread messages and displays num beside the message icon in nav bar
	public function getUnreadNumber() {
		$userLoggedIn = $this->user_obj->getUsername();
		//Querys how many messages are not viewed by viewed = no
		$stmt = $this->con->prepare("SELECT * FROM messages WHERE viewed='no' AND user_to= ?");
        $stmt->bind_param("s", $userLoggedIn);
        $stmt->execute();
        $result = $stmt->get_result();
		$query = mysqli_num_rows($result);
		
		//returns number of rows of unread messages to be displayed
		return $query;
	}

}

?>