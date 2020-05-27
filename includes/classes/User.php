<?php


class User {
    //Creating private variables for this class
    private $user;  //user variable
    private $con;   //connection variable

    //CONSTRUCTOR
    //This-> used to represent class variable in the function
    public function __construct($con, $user){   //Takes parameters connection and the user
        $this->con = $con;   //takes connection variable
        $stmt = $this->con->prepare("SELECT * FROM users WHERE username= ?");   //Gathers information from user and stores into variable
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $user_details_query = $stmt->get_result();
        $this->user = mysqli_fetch_array($user_details_query);   //Stores user information into an array called user
    }

    //Gets username from user logged in
    public function getUsername(){
        return $this->user['username'];
    }

    //This gets the number of posts the user made
    public function getNumPosts() {
        $username = $this->user['username'];    //gets username from user logged in 
        $stmt = $this->con->prepare("SELECT num_posts FROM users WHERE username= ?");   //query to select numposts column where username = username logged in
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $query = $stmt->get_result();

        $row = mysqli_fetch_array($query);   //stores numposts into array variable row
        return $row['num_posts'];   //returns number of posts
    }

    //Get first and last name function to user logged in
    public function getFirstAndLastName() {
        $username = $this->user['username'];
        $stmt = $this->con->prepare("SELECT first_name, last_name FROM users WHERE username= ?");   //Gets first and last name of user
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $query = $stmt->get_result();

        $row = mysqli_fetch_array($query);    //Stores first and last name in variable row
        return $row['first_name'] . " " . $row['last_name'];   //returns first and last name

    }

    //Get the number of friend requests to display as a notification above icon
    public function getNumberOfFriendRequests() {
        $username = $this->user['username']; // find user logged in in column
        $stmt = $this->con->prepare("SELECT * FROM friend_requests WHERE user_to= ?");   //query friend requests
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $query = $stmt->get_result();

        return mysqli_num_rows($query);
    }

    //Grab a profile pic from the associated username
    public function getProfilePic() {
        $username = $this->user['username'];
        $stmt = $this->con->prepare("SELECT profile_pic FROM users WHERE username= ?");   //Gets profile pic of that username
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $query = $stmt->get_result();

        $row = mysqli_fetch_array($query); //Stores profilepic in variable row
        return $row['profile_pic']; //returns profile pic
    }

    public function isClosed() {
        $username = $this->user['username'];
        $stmt = $this->con->prepare("SELECT user_closed FROM users WHERE username= ?");   //Here I store user_closed column into query from the user who posted it
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $query = $stmt->get_result();

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
        $stmt = $this->con->prepare("SELECT friend_array FROM users WHERE username= ?");   
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $query = $stmt->get_result();

        $row = mysqli_fetch_array($query);
        return $row['friend_array'];
    }

    //Checks if request was recieved
    public function didReceiveRequest($user_from) {
        $user_to = $this->user['username'];
        //variable created to make quert call to friend request table        
        $stmt = $this->con->prepare("SELECT * FROM friend_requests WHERE user_to= ? AND user_from= ?");   
        $stmt->bind_param("ss", $user_to, $user_from);
        $stmt->execute();
        $check_request_query = $stmt->get_result();

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
        $stmt = $this->con->prepare("SELECT * FROM friend_requests WHERE user_to= ? AND user_from= ?");   
        $stmt->bind_param("ss", $user_to, $user_from);
        $stmt->execute();
        $check_request_query = $stmt->get_result();

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
        $stmt = $this->con->prepare("SELECT friend_array  FROM users WHERE username= ?");   
        $stmt->bind_param("s", $user_remove);
        $stmt->execute();
        $setquery = $stmt->get_result();
        
        //Store query in an array
        $row = mysqli_fetch_array($setquery);
        
        //Store the friend array list into array friends username
        $array_friends_username = $row['friend_array'];
        
        //Here I use str_replace to find user we want to remove plus the comma and replace with empty string in the array
        $array_new_friend = str_replace($user_remove . ",", "", $this->user['friend_array']);
        
        //Update the table with the array to remove the friend
        $remove_friend = $this->con->prepare("UPDATE users SET friend_array= ? WHERE username= ?");   
        $remove_friend->bind_param("ss", $array_new_friend, $logged_in_user);
        $remove_friend->execute();
 
        //We do the same for the user table that has been deleted so both users have their names removed from their databases
        $array_new_friend = str_replace($this->user['username'] . ",", "", $array_friends_username);
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$array_new_friend' WHERE username='$user_remove");
    }
    
    public function sendRequest($user_to) {
		$user_from = $this->user['username'];
        $remove_friend = $this->con->prepare("INSERT INTO friend_requests VALUES(NULL, ?, ?)");   
        $remove_friend->bind_param("ss", $user_to, $user_from);
        $remove_friend->execute();
	}

    public function getMutualFriends($user_to_check) {
        $mutualFriends = 0;
        $user_array = $this->user['friend_array'];
        $user_array_explode = explode(",", $user_array);

        $stmt = $this->con->prepare("SELECT friend_array FROM users WHERE username= ?");   
        $stmt->bind_param("s", $user_to_check);
        $stmt->execute();
        $query = $stmt->get_result();

        $row = mysqli_fetch_array($query);
        $user_to_check_array = $row['friend_array'];
        $user_to_check_array_explode = explode(",", $user_to_check_array);

        foreach($user_array_explode as $i) {

            foreach($user_to_check_array_explode as $j) {

                if($i == $j && $i != "") {
                    $mutualFriends++;
                }
            }
        }
        return $mutualFriends;

    }

    public function isFollowedTo($userTo) {
        //Query to get all followers of user loggedIn
        $stmt = $this->con->prepare("SELECT * FROM followers WHERE userTo = ? AND userFrom = ?");
        $username = $this->getUsername();
        $stmt->bind_param("ss", $userTo, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $query = mysqli_num_rows($result);
        return $query > 0;
        
    }

    public function getFollowerCount() {
        //Query to get all followers of user loggedIn
        $stmt = $this->con->prepare("SELECT * FROM followers WHERE userTo = ?");
        $username = $this->getUsername();
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $query = mysqli_num_rows($result);
        return $query;

    }

    //FUNCTION PASSED TO FollowersContentProvider Class
    public function getFollowers() {
        $query = $this->con->prepare("SELECT userTo FROM followers WHERE userFrom=?");
        $username = $this->getUsername();
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();
        $subs = array();

        while($row = mysqli_fetch_assoc($result)) {
            $user = new User($this->con, $row["userTo"]);
            array_push($subs, $user);
        }
        return $subs;
    }
}
?>
