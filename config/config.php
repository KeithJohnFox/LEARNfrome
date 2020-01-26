<?php
    ob_start(); //this turns on output bufering, passing php code at the end of the browser when it loads to speed the loading time
    session_start();    //Session start stores values of the variables inside the session variable
    $con = mysqli_connect("localhost", "root", "", "learnfrome");

    $timezone = date_default_timezone_set("Europe/Dublin"); //sets the default time to dublin

    // if statement is used if there is an error by using the errno command AND the . is append allowing you to join 2 strings
    if(mysqli_connect_errno())
    {
        echo "Failed to connect: " . mysqli_connect_errno();
    }
?>
