<?php 

if(isset($_POST['login_button'])) {
    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //FILTER SANATIZE makes sure the email is in the correct format
    
    $_SESSION['log_email'] = $email; //Store email into session to keep email in email field when page refereshes
    $password = md5($_POST['log_password']); //gets password from form and encrypts it with md5, then stores it into password variable

    //Query that compares if the password and email entered matches with the database
    $check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    $check_login_query = mysqli_num_rows($check_database_query); //If row exists check_login_query will store value '1' row, if details dont match variable will be empty

    if($check_login_query == 1) {
        //this allows us to access result returned from above query and store it into the row variable
        $row = mysqli_fetch_array($check_database_query);
        //using row variable we can now access other details of that user by specifying that row
        $username = $row['username'];

        //This is to set user closed to no if they login 
        $user_closed_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND user_closed='yes'"); // If user email, has yes under user_closed column
        if(mysqli_num_rows($user_closed_query) == 1) {
            $reopen_account = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE email='$email'"); // Then set user_closed to no when they login
        }

        //Session is now called username which stores the username of the user
        $_SESSION['username'] = $username;
        //header redirects the page to index.php
        //This only occurs when the username is not set to null 
        header("location: index.php");
        exit();
    }
    else {
        array_push($error_array, "Email or password was incorrect<br>");
    }
}

?>