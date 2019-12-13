<?php
//WHEN THE SIGNOUT BUTTON IS PRESSED THIS PAGE IS CALLED
//Lets us know we are using sessions and when this page is called it will destroy session
//Header will Location will bring us back to the register page
session_start();
session_destroy();
header("Location: ../../register.php")
?>