<?php
class User {
    //Creating private variables for this class
    private $user;  //user variable
    private $con;   //connection variable

    //CONSTRUCTOR
    //This-> used to represent class variable in the function
    public function __construct($con, $user){   //Takes parameters connection and the user
        $this->con = $con;   //takes connection variable
        $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");    //Gathers information from user and stores into variable
        $this->user = mysqli_fetch_array($user_details_query);   //Stores user information into an array called user
    }

    //Gets username from user logged in
    public function getUsername(){
        return $this->user['username'];
    }

    //This gets the number of posts the user made
    public function getNumPosts() {
        $username = $this->user['username'];    //gets username from user logged in
        $query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE username='$username'");   //query to select numposts column where username = username logged in
        $row = mysqli_fetch_array($query);   //stores numposts into array variable row
        return $row['num_posts'];   //returns number of posts
    }

    //Get first and last name function to user logged in
    public function getFirstAndLastName() {
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE username='$username'");    //Gets first and last name of user
        $row = mysqli_fetch_array($query);    //Stores first and last name in variable row
        return $row['first_name'] . " " . $row['last_name'];   //returns first and last name

    }

    //Grab a profile pic from the associated username
    public function getProfilePic() {
        $username = $this->user['username'];
        //Gets profile pic of that username
        $query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE username='$username'"); //Gets first and last name of user
        $row = mysqli_fetch_array($query); //Stores profilepic in variable row
        return $row['profile_pic']; //returns profile pic
    }

    public function isClosed() {
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT user_closed FROM users WHERE username='$username'");  //Here I store user_closed column into query from the user who posted it
        $row = mysqli_fetch_array($query);  //store query result in array row var

        //If column in database contains yes under user_closed we return true otherwise false
        if($row['user_closed'] == 'yes')
            return true;
        else
            return false;
    }

    //Checks if a user is your friend
    public function isFriend($username_to_check) {
        $usernameComma = "," . $username_to_check . ","; //Setups variable that stores in your database

        //Haystack needle check that friend in database table equals to your username, if so passes true or false if not
        if((strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username'])) {
            return true;
        }
        else {
          return false;
        }
    }

    //gets friend array for friend requests page
    public function getFriendArray(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$username'");
        $row = mysqli_fetch_array($query);
        return $row['friend_array'];
    }

    //Checks if request was recieved
    public function didReceiveRequest($user_from) {
        $user_to = $this->user['username'];
        //variable created to make quert call to friend request table
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
        //If the row in friend request table is higher then 0 then friend request is true
        if(mysqli_num_rows($check_request_query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
    
    //Check a friend request has been sent to user 
    public function didSendRequest($user_to) {
		$user_from = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

    //Remove a friend 
    public function removeFriend($user_remove) {
        //places user logged in into var logged in user
        $logged_in_user = $this->user['username'];
        
        //Select friend array row from username you want to remove
        $setquery = mysqli_query($this->con, "SELECT friend_array  FROM users WHERE username='$user_remove'");
        
        //Store query in an array
        $row = mysqli_fetch_array($setquery);
        
        //Store the friend array list into array friends username
        $array_friends_username = $row['friend_array'];
        
        //Here I use str_replace to find user we want to remove plus the comma and replace with empty string in the array
        $array_new_friend = str_replace($user_remove . ",", "", $this->user['friend_array']);
        
        //Update the table with the array to remove the friend
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$array_new_friend' WHERE username='$logged_in_user'");
        
        //We do the same for the user table that has been deleted so both users have their names removed from their databases
        $array_new_friend = str_replace($this->user['username'] . ",", "", $array_friends_username);
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$array_new_friend' WHERE username='$user_remove");
    }
    
    public function sendRequest($user_to) {
		$user_from = $this->user['username'];
		$query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES('', '$user_to', '$user_from')");
	}


}





?>
