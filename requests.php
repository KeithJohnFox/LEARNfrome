<?php
include("includes/header.php");    //includes just pastes in code from header.php file

?>

<div class="main_column column" id="main_column">
    <h4>Friend Request</h4>
    <?php
    //variabble query stores user_to "names of friend requests" from database table friend_requests
    $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");

    //Check For any friend requests
    if(mysqli_num_rows($query) == 0)
        echo "You have no friend requests right now!";

    else{
        //Loop runs through friend request table and selects any row where user_to column has user loggedin's name
        //row variable stores all rows that has user_to as user logged in
        while($row = mysqli_fetch_array($query)) {
            //stores user from username into var
            $user_from = $row['user_from'];

            $user_from_obj = new User($con, $user_from);

            //Echo names of people who want to be your friend
            echo $user_from_obj->getFirstAndLastName() . " wants to be your friend!";

            $user_from_friend_array = $user_from_obj->getFriendArray();

            if(isset($_POST['accept_request' . $user_from ])) {
                $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
                $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$userLoggedIn'");

                $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "You have made a new friend!";
                header("Location: requests.php");
            }

            if(isset($_POST['ignore_request' . $user_from ])) {
                $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "You have ignored friend request!";
                header("Location: requests.php");
            }

            ?>
            <form action="requests.php" method="POST">
                
                <input type="submit" name="accept_request<?php echo $user_from; ?>" class="success" id="accept_button" value="Accept">
                <input type="submit" name="ignore_request<?php echo $user_from; ?>" class="warning" id="ignore_button" value="Ignore">
                
            </form>
            <?php
        }
    }
    ?>

   

</div>
