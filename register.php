<?php
    /****IT IS IMPORTANT THE ORDER OF THE REQUIRE PAGES OF WHICH ONE RUNS FIRST EG CONFIG BECAUSE ITS THE CONNECTION PAGE */
    require 'config/config.php'; // Loadings the database connection code in the config.php
    require 'includes/form_handlers/register_handler.php'; //Access register handler page that contains all php code for the register page, This just cleans up the code
    require 'includes/form_handlers/login_handler.php'; //Access login handler page
?>
<html>
<head>
    <title>Welcome to LEARNfrome</title>
    <link rel="stylesheet" type="text/css" href="assets/css/register_style.css">   <!-- CSS style link-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery Link -->
    <script src="assets/js/register.js"></script>   <!-- register javascript code link -->
</head>
<body>
    <?php
    //If you press register button but you have details errors this code prevents login section to show again when the button is pressed
    if(isset($_POST['register_button'])) {
        echo '
            <script>
                $(document).ready(function () {
                    $("#first").hide();
                    $("#second").show();
                });
            </script>
        ';
    }
    ?>

    <div class="wrapper"> <!-- This div "wrapper" will contain the entire page   -->
        <div class="login_box">
            <div class="login_header"> <!-- Header class for login page -->
            <!-- <img src="assets/css/iqmelogo.PNG" ALT="some text" WIDTH=70 HEIGHT=70> -->
                <h1><span>LEARN</span>frome!</h1>
                Login or sign up below
            </div>

            <div id="first">
                <!-- Login form -->
                <!-- "action" is stating the page its being sent to, which is register.php -->
                <form action="register.php" method="POST">
                    <input type="email" name="log_email" placeholder="Email Address" value="<?php
                    if(isset($_SESSION['reg_email'])) {
                        echo $_SESSION['reg_email'];
                    }
                    ?>" required>
                    <br>
                    <input type="password" name="log_password" placeholder="password">
                    <br>
                    <?php if(in_array("Email or password was incorrect<br>", $error_array)) echo "Email or password was incorrect<br>"?> <!--Email or password error message for login -->
                    <input type="submit" name="login_button" value="Login">
                    <br>
                    <a href="#" id="signup" class="signup">Need an account? Register Now.</a>    <!-- Sign in link -->
                </form>
            </div>

            <div id="second">
                <!-- Register Input Fields -->
                <form action="register.php" method="POST">
                    <!-- session variable stores the details and prints them to keep details saved when user recieves and error -->
                    <input type="text" name="reg_fname" placeholder="First Name" value="<?php
                    if(isset($_SESSION['reg_fname'])) {
                        echo $_SESSION['reg_fname'];
                    }
                    ?>" required>
                    <br>
                    <?php
                    //Prints out error message!
                    if(in_array("First name must be between 2 and 25 characters long<br>", $error_array)) echo "<font color=red>First name must be between 2 and 25 characters long<br>";  ?>    <!-- Using in_array, pushes error message into array var and then echo error message -->

                    <!-- LAST NAME SAVE & ERROR MESSAGE -->
                    <input type="text" name="reg_lname" placeholder="Last Name" value="<?php
                    if(isset($_SESSION['reg_lname'])) {
                        echo $_SESSION['reg_lname'];
                    }
                    ?>" required>
                    <br>
                    <?php if(in_array("Last name must be between 2 and 25 characters long<br>", $error_array)) echo "<font color=red>Last name must be between 2 and 25 characters long<br>";  ?>

                    <!-- EMAL SAVE & ERROR MESSAGE -->
                    <input type="email" name="reg_email" placeholder="Email" value="<?php
                    if(isset($_SESSION['reg_email'])) {
                        echo $_SESSION['reg_email'];
                    }
                    ?>" required>
                    <br>

                    <input type="email" name="reg_email2" placeholder="Confirm Email" value="<?php
                    if(isset($_SESSION['reg_email2'])) {
                        echo $_SESSION['reg_email2'];
                    }
                    ?>" required>
                    <br> <!-- All email error messages -->
                    <?php if(in_array("Email already in use<br>", $error_array)) echo "<font color=red>Email already in use<br>";      //Using in_array, pushes error message into array var and then echo error message
                        else if(in_array("Invalid format<br>", $error_array)) echo "<font color=red>Invalid format<br>";
                        else if(in_array("Emails do not match!<br>", $error_array)) echo "<font color=red> Emails do not match!<br>";  ?>

                    <!-- Password Error Messages -->
                    <input type="password" name="reg_password" placeholder="Password" required>
                    <br>
                    <input type="password" name="reg_password2" placeholder="Confirm Password" required>
                    <br>
                    <?php if(in_array("Please enter matching passwords<br>", $error_array)) echo "<font color=red>Please enter matching passwords<br>";      //Using in_array, pushes error message into array var and then echo error message
                        else if(in_array("Password can only contain letters and numbers<br>", $error_array)) echo "<font color=red>Password can only contain letters and numbers<br>";
                        else if(in_array("Your password must be between 5 and 30 characters<br>", $error_array)) echo "<font color=red>Your password must be between 5 and 30 characters<br>";  ?>


                    <input type="submit" name="register_button" value="Register">
                    <br>

                    <?php if(in_array("<span style='color: #14C800;'>You're account has been created!</span><br>", $error_array)) echo "<span style='color: #14C800;'>You're account has been created!</span><br>"; ?>
                    <a href="#" id="signin" class="signin">Have an account? Sign in here.</a>    <!-- Signup link -->
                </form>
            </div>
        </div>
    </div>
</body>
</html>
