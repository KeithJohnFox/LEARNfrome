<?php
//This page holds php code for the register page.
//THIS CODE CONSISTS OF:  Variable used, all possible errors for registering details, error messages and randomizing profile for user


//I'm Declaring variables to prevent any errors
    $fname = "";        //First Name
    $lname = "";        //Last Name
    $em = "";           //Email
    $em2 = "";          //Email Confirm         
    $password = "";     //Password
    $password2 = "";    //Password Confirm
    $date = "";         //Date of Sign Up
    $error_array = array();  //Holds All Error Messages

    if(isset($_POST['register_button'])){
        
        //REGISTRATION FORM VALUES
        //First Name
        $fname = strip_tags($_POST['reg_fname']); //Remove html tags
        $fname = str_replace(' ', '', $fname);  //Removes any spaces
        $fname = ucfirst(strtolower($fname));   //converts all letters to lower case but capitalises the first letter 
        $_SESSION['reg_fname'] = $fname;        //Stores the first name into the session variable
        //Last Name
        $lname = strip_tags($_POST['reg_lname']); //Remove html tags
        $lname = str_replace(' ', '', $lname);  //Removes any spaces
        $lname = ucfirst(strtolower($lname));   //converts all letters to lower case but capitalises the first letter 
        $_SESSION['reg_lname'] = $lname;

        //Email
        $em = strip_tags($_POST['reg_email']); //Remove html tags
        $em = str_replace(' ', '', $em);  //Removes any spaces
        $em = ucfirst(strtolower($em));   //converts all letters to lower case but capitalises the first letter 
        $_SESSION['reg_email'] = $em;

        //Email Confirm
        $em2 = strip_tags($_POST['reg_email2']); //Remove html tags
        $em2 = str_replace(' ', '', $em2);  //Removes any spaces
        $em2 = ucfirst(strtolower($em2));   //converts all letters to lower case but capitalises the first letter 
        $_SESSION['reg_email2'] = $em2;

        //Password & Password Confirm
        $password = strip_tags($_POST['reg_password']); //Remove html tags
        $password2 = strip_tags($_POST['reg_password2']); //Remove html tags

        $date = date("Y-m-d"); //Gets the current date
        
        //VALIDATING EMAIL
        if($em == $em2) {
            //Here we check that the email is valid formating 
            if(filter_var($em, FILTER_VALIDATE_EMAIL)) {
                $em = filter_var($em, FILTER_VALIDATE_EMAIL);   //Here we set the email as being valid 
                
                //checking if email already exist
                $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

                //Counting the numbers of rows returned
                $num_rows = mysqli_num_rows($e_check);
                //If num rows returns value greater than 1 than that email already exists in database
                if($num_rows > 0) {
                    array_push($error_array, "Email already in use<br>");   //Error message stored in array, array_push stores string in array var
                }
            }
            else {
                array_push($error_array, "Invalid format<br>");
            }
        }
        else {
            array_push($error_array, "Emails do not match!<br>");
        }

        //USER SIGNUP GUIDELINES
        if(strlen($fname) > 25 || strlen($fname) < 2) {
            array_push($error_array, "First name must be between 2 and 25 characters long<br>");
        }

        if(strlen($lname) > 25 || strlen($lname) < 2) {
            array_push($error_array, "Last name must be between 2 and 25 characters long<br>");
        }

        if($password != $password2) {
            array_push($error_array, "Please enter matching passwords<br>");
        }
        else{
            if(preg_match('/[^A-Za-z0-9]/', $password)) {    //Password must contain letters & numbers
                array_push($error_array, "Password can only contain letters and numbers<br>");
            }
        }
        //Password must contain between 5 and 30 characters
        if(strlen($password > 30 || strlen($password) < 5)) {
            array_push($error_array, "Your password must be between 5 and 30 characters<br>");
        }
        //If there is no errors in the error array we encyrpt the password using md5 before sending password to the database
        if(empty($error_array)) {
            $password = md5($password); //md5 is a secuirty encryption

            //Here I am generating a username by simply combining first and last name together  
            $username = strtolower($fname . "_" . $lname); 
            //Here we query to check if the username has been already taken in the database
            $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username ='$username'");   //Note: you cannot use double quotes in double quotes, thats why i use single quotes after
            
            //If there is a username this code adds a number to the name and if the number has been taken it incrememnts ny 1 till the username doesnt exists 
            $i = 0;
            while(mysqli_num_rows($check_username_query) != 0) {    //If there is a username there
                $i++;   //increment
                $username = $username . "_" . $i;   //Add number to username
                $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'"); //checks new username if it exists again
            }

            //Assigning profile picture to account
            $rand = rand(1, 4); //rand short for random picks number between 1 - 4 for assign random default user picture

            //Sets profile pic variable to image path
            if($rand == 1)
                $profile_pic = "assets/images/profile_pictures/default_pictures/head_alizarin.png";

            else if($rand == 2)
                $profile_pic = "assets/images/profile_pictures/default_pictures/head_emerald.png";

            else if($rand == 3)
                $profile_pic = "assets/images/profile_pictures/default_pictures/head_pete_river.png";
                
            else if($rand == 4)
                $profile_pic = "assets/images/profile_pictures/default_pictures/head_wet_asphalt.png";    

            //Sending data to database
            $query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");
            array_push($error_array, "<span style='color: #14C800;'>You're account has been created!</span><br>");

            //Here I clear the session variables
            $_SESSION['reg_fname'] = "";
            $_SESSION['reg_lname'] = "";
            $_SESSION['reg_email'] = "";
            $_SESSION['reg_email2'] = "";
        }

    }
?>