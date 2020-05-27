<?php
//Includes config for connection and user class
include("../../config/config.php");
include("../../includes/classes/User.php");

//Query and userLoggedIn are to parameters we send here from learnfrome.js file in function getLiveSearchUsers(value, user)
//Make variables
$query = $_POST['query'];
$query = strip_tags($query);  //removes any html tags
$query = mysqli_real_escape_string($con, $query); //escapes special characters in a string for use in an SQL query
$userLoggedIn = $_POST['userLoggedIn'];

//Explode splits the search string into an array, Example if search string is Keith Fox then it would be [0] Keith, [1] Fox
//We use Explode to separate values in arrays so we can search first and second name seperately
$names = explode(" ", $query);

//If query contains an underscore, then we assume user is searching for a username
if(strpos($query, '_') !== false){
    // $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");

    $stmt = $con->prepare("SELECT * FROM users WHERE username LIKE CONCAT(?, '%')
                                        AND user_closed='no' LIMIT 8");
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $usersReturnedQuery = $stmt->get_result();
}

//If there are two words entered, then assume they are first and last names
else if(count($names) == 2){
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");
}
//If query has only one word, then search first names or last names
else {
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");
}
//If search has a result from database
if($query != ""){
    $mutual_friends = "";
    //Loop through
    while($row = mysqli_fetch_array($usersReturnedQuery)) {
        $user = new User($con, $userLoggedIn);
        //If result is not equal to user already logged in
        if($row['username'] != $userLoggedIn)
            //Display number of friends you have in common with that user
            $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
        //If user has found its username then dont display how many friends you have in common
        else
            $mutual_friends == "";

        //Display drop down menu div with results of usernames / names / profile images / mutual friends
        echo "<div class='resultDisplaySearch'>
				<a href='" . $row['username'] . "' style='color: #1485BD'>
					<div class='liveSearchProfilePic'>
						<img src='" . $row['profile_pic'] ."'>
					</div>
					<div class='liveSearchText'>
						" . $row['first_name'] . " " . $row['last_name'] . "
						<p>" . $row['username'] ."</p>
						<p id='grey'>" . $mutual_friends ."</p>
					</div>
				</a>
				</div>";

    }

}

?>

