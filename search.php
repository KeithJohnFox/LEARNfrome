<?php
//includes header nav bar
include("includes/header.php");

if(isset($_GET['q'])) {
    $query = $_GET['q'];
    $query = strip_tags($query);  //removes any html tags
	$query = mysqli_real_escape_string($con, $query); //escapes special characters in a string for use in an SQL query
}
else {
    $query = "";
}

//Type var = username or name
if(isset($_GET['type'])) {
    $type = $_GET['type'];
}
else {
    $type = "name";
}
?>

<!--HTML code-->
<div class="messageContainer">
    <div class="result_column column" id="main_column">

    <?php
    //Echo message you have to write in the input search box to make a search
    if($query == "")
        echo "You must enter something in the search box.";
    else {



        //If the query contains an underscore, then assume user is searching for usernames
        if($type == "username")
            $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
        //If there are two words, assume they are first and last names respectively
        else {

            $names = explode(" ", $query);
            //if there is 3 words in search for name
            if(count($names) == 3)
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");
            //If query has two words in search
            else if(count($names) == 2)
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
        //One word in search for name
            else
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");
        }

        //Check if any results are found
        if(mysqli_num_rows($usersReturnedQuery) == 0)
            //if no results output this
            echo "We can't find anyone with a " . $type . " like: " .$query;
        else
            //results are found
            echo mysqli_num_rows($usersReturnedQuery) . " results found: <br> <br>";


        echo "<p id='grey'>Try searching for:</p>";
        echo "<a href='search.php?q=" . $query ."&type=name'>Names</a>, <a href='search.php?q=" . $query ."&type=username'>Usernames</a><br><br><hr id='search_hr'>";

        while($row = mysqli_fetch_array($usersReturnedQuery)) {
            $user_obj = new User($con, $user['username']);

            $button = "";
            $mutual_friends = "";

            if($user['username'] != $row['username']) {

                //Generate button depending on friendship status to add friend request or not
                if($user_obj->isFriend($row['username']))
                    //remove friend button  (red)
                    $button = "<input type='submit' name='" . $row['username'] . "' class='danger' value='Remove Friend'>";
                else if($user_obj->didReceiveRequest($row['username']))
                    //Respond to accept friend request button (orange)
                    $button = "<input type='submit' name='" . $row['username'] . "' class='warning' value='Respond to request'>";
                else if($user_obj->didSendRequest($row['username']))
                    //Friend request has been sent button (grey)
                    $button = "<input type='submit' class='default' value='Request Sent'>";
                else
                    //Add friend button (green)
                    $button = "<input type='submit' name='" . $row['username'] . "' class='success' value='Add Friend'>";

                $mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";


                //Button forms
                if(isset($_POST[$row['username']])) {

                    if($user_obj->isFriend($row['username'])) {
                        $user_obj->removeFriend($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }
                    else if($user_obj->didReceiveRequest($row['username'])) {
                        header("Location: requests.php");
                    }
                    else if($user_obj->didSendRequest($row['username'])) {

                    }
                    else {
                        $user_obj->sendRequest($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }

                }



            }
            //HTML for search page to display username, fname and lname, and profile image
            echo "<div class='search_result'>
                    <div class='searchPageFriendButtons'>
                        <form action='' method='POST'>
                            " . $button . "
                            <br>
                        </form>
                    </div>

                
                    <div class='result_profile_pic'>
                        <a href='" . $row['username'] ."'><img src='". $row['profile_pic'] ."' style='height: 100px;'></a>
                    </div>
                        
                        <a href='" . $row['username'] ."'> " . $row['first_name'] . " " . $row['last_name'] . "
                        <p id='grey'> " . $row['username'] ."</p>
                        </a>
                        <br>
                        " . $mutual_friends ."<br>

                </div>
                <hr id='search_hr'>";

        } //End of while loop
    }


    ?>
    </div>
</div>
