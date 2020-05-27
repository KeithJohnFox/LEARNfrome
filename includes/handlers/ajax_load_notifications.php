<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Notification.php");

//limit of notifications loaded on page = 7
$limit = 7;

//Notification instance class
$notification = new Notification($con, $_REQUEST['userLoggedIn']);
//Output notifications from function
echo $notification->getNotifications($_REQUEST, $limit);

?>