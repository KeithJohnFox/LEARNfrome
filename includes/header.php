<?php
require 'config/config.php';
include("includes/classes/User.php");    //Includes User Class to index page
include("includes/classes/Post.php");    //Includes Post Class to index page
include("includes/classes/Message.php");    //Includes Message Class to index page

//If this session variable is set make the userloggedIn variable username
//This stops users access the website without logging in
if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    //querys users information into user var for user name to display on nav bar in the nav section
    $user_details_query = mysqli_query($con , "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
}
else {
    // Username is not set, send them back to register page
    header("Location: register.php");
}
?>

<html>
<head>
    <title>LEARNfrome</title>

    <!-- JavaScript Links -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery Link -->
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/learnfrome.js"></script>
    

    <!-- CSS Links-->
    <script src="https://kit.fontawesome.com/88a64f9c03.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">    <!-- NOTE: Style css needs to be below bootstrap so we overide any bootstrap we dont want -->
</head>
<body>

    <div class="top_bar">
        <div class="logo">
            <a href="index.php">LEARNfrome</a>
        </div>

        <nav>
            <a href="#"><?php echo $user['first_name']; ?></a>  <!-- displays username through user variable and selecting first name-->
            <a href="index.php"><i class="fas fa-home fa-lg"></i></a>
            <a href="messages.php"><i class="fas fa-envelope fa-lg"></i></a>
            <a href="#"><i class="far fa-bell fa-lg"></i></a>
            <a href="requests.php"><i class="fas fa-users fa-lg"></i></i></a>
            <a href="#"><i class="fas fa-user-cog fa-lg"></i></a>
            <a href="includes/handlers/logout.php"><i class="fas fa-sign-out-alt fa-lg"></i></a>    <!-- When logout icon is pressed href links to logout handler file to execute logout -->
        </nav>
    </div>

    <div class="wrapper">
