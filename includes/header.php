<?php
require 'config/config.php';
include("includes/classes/User.php");    //Includes User Class to index page
include("includes/classes/Post.php");    //Includes Post Class to index page
include("includes/classes/Message.php");    //Includes Message Class to index page
include("includes/classes/Notification.php"); //Includes Notification class file
include("includes/classes/Video.php"); 

require_once("includes/classes/ButtonProvider.php");
require_once("includes/classes/VideoGrid.php");
require_once("includes/classes/VideoGridItem.php");
require_once("includes/classes/FollowerContentProvider.php");
require_once("includes/classes/NavigationMenuProvider.php");

//If this session variable is set make the userloggedIn variable username
//This stops users access the website without logging in
if (isset($_SESSION['username'])) {
    //Secure session by deleting the old session id
    session_regenerate_id(true);
    $userLoggedIn = $_SESSION['username'];
    //querys users information into user var for user name to display on nav bar in the nav section
    $user_details_query = mysqli_query($con , "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
    $userLoggedInObj = new User($con, $userLoggedIn);
}
else {
    // Username is not set, send them back to register page
    header("Location: register.php");
}

?>

<html>
<head>
    <title>LEARNfrome</title>

    <!-- CSS Links-->
    <script src="https://kit.fontawesome.com/88a64f9c03.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"> <!-- NOTE: Style css needs to be below bootstrap so we overide any bootstrap we dont want -->
    <link rel="stylesheet" type="text/css" href="assets/css/jquery.Jcrop.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <!-- JavaScript Links -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/learnfrome.js"></script>
    <script src="assets/js/jquery.jcrop.js"></script>
    <script src="assets/js/jcrop_bits.js"></script>
    <script src="assets/js/userActions.js"></script>


</head>
<body>

    <div class="top_bar">
        <div class="logo">
            <a href="index.php">
                <img href="index.php" border="0" alt="Learnfrome" src="assets/images/icons/learnfromeLogo2.png" >
            </a>
        </div>
<!--        SEARCH BAR ON NAV-->


        <div class="search">

        <form action="search.php" method="GET" name="search_form">
            <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">

            <div class="button_holder">
                <img src="assets/images/icons/magnifying_glass3.png">
            </div>

        </form>

        <div class="search_results">
        </div>

        <div class="search_results_footer_empty">
        </div>



        </div>

        <!-- All nav icons-->
        <nav>
            <?php
            //Instance of Message class and Number of messages connected to getUnreadNumber function
            //Unread Messages
            $messages = new Message($con, $userLoggedIn);
            $num_messages = $messages->getUnreadNumber();

            //Notifications unread
            $notifications = new Notification($con, $userLoggedIn);
            $num_notifications = $notifications->getUnreadNumber();

            //Friend Requests unseen
            $user_obj= new User($con, $userLoggedIn);
            $num_requests = $user_obj->getNumberOfFriendRequests();

            ?>

            
            <a href="tutorials.php" class="tutIcon">
                <img src="assets/images/icons/tutIcon.png" alt="tutorials">
            </a>  
            <a href="index.php" class="mainIcons">
                <img src="assets/images/icons/mainHome.png" alt="home">
            </a>      <!-- Displays Home icon to redirect to index page-->

            <a href="javascript:void(0);" class="messageIcon" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">     <!--Messages: DropDown javascript display all user conversations with friends-->
                <img src="assets/images/icons/messageIcon.png" alt="message">
                <?php
                if($num_messages > 0)
                    echo '<span class="notification_badge" id="unread_message">' . $num_messages . '</span>';
                ?>
            </a>

            <a href="javascript:void(0);" class="notificationIcon" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                <img src="assets/images/icons/notificationIcon.png" alt="notifications">   
                <?php
                if($num_notifications > 0)
                    echo '<span class="notification_badge" id="unread_notifications">' . $num_notifications . '</span>';
                ?>
            </a>   <!-- Shows notifications and how many are unseen -->
            <a href="requests.php" class="messageIcon">
                <img src="assets/images/icons/friendIcon.png" alt="Friend Request">
                <?php
                if($num_requests > 0)
                    echo '<span class="notification_badge" id="unread_requests">' . $num_requests . '</span>';
                ?>
            </a>   <!-- Friend Requests Icon -->
              
            <a href="uploadvideo.php" class="uploadIcon">
                <img src="assets/images/icons/uploadIcon.png" alt="upload">
            </a>

<!--            Settings Page-->
            <a href="settings.php" class="messageIcon">
                <img src="assets/images/icons/settingsIcon.png" alt="settings">
            </a>

            <a href="<?php echo $userLoggedIn; ?>" class="profileIcon">
                <?php
                echo "<img src='" . $user['profile_pic'] ."' class='small_profile_pic_nav'>";   //profile image
                ?>
            </a>
          

        </nav>

<!--        Dropdown Message div, when user clicks message icon on nav bar, a dropdown menu of recent messages show-->
        <div class="dropdown_data_window" style="height:0px; border:none;"></div>
        <input type="hidden" id="dropdown_data_type" value="">

<!--        This script allows new messages in drop down to be highlighted blue to indicate msg not read-->
<!--            Code allows infinite scrolling for message dropdown box-->
        <script>
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';

            $(document).ready(function() {

                $('.dropdown_data_window').scroll(function() {
                    var inner_height = $('.dropdown_data_window').innerHeight(); //Div containing data
                    var scroll_top = $('.dropdown_data_window').scrollTop();
                    var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
                    var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

                    if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

                        var pageName; //Holds name of page to send ajax request to
                        var type = $('#dropdown_data_type').val();


                        if(type == 'notification')
                            pageName = "ajax_load_notifications.php";
                        else if(type = 'message')
                            pageName = "ajax_load_messages.php"


                        var ajaxReq = $.ajax({
                            url: "includes/handlers/" + pageName,
                            type: "POST",
                            data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                            cache:false,

                            success: function(response) {
                                $('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage
                                $('.dropdown_data_window').find('.noMoreDropdownData').remove(); //Removes current .nextpage


                                $('.dropdown_data_window').append(response);
                            }
                        });

                    } //End if

                    return false;

                }); //End (window).scroll(function())
            });
        </script>
    </div>
    
  
