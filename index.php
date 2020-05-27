<?php
//adding includes, cuts down on duplicated code, instead i recall the header eg nav bar to pages needed
include("includes/header.php");    //includes just pastes in code from header.php file
require_once("includes/Classes/ProfileData.php"); 


if(isset($_POST['post'])){ //If the post button is pressed

    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $errorMessage = "";

    //if image is not empty
    if($imageName != "") {
        $targetDir = "assets/images/posts/";
        $imageName = $targetDir . uniqid() . basename($imageName);
        $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

        //If the file size it too big
        if($_FILES['fileToUpload']['size'] > 10000000) {
            $errorMessage = "Sorry your file is too large";
            $uploadOk = 0;
        }

        //If the file type is not jpeg or png then dont accept file, output error message
        if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
            $errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
            $uploadOk = 0;
        }

        //If upload is validated then run this code to upload file
        if($uploadOk) {
            if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
                //image uploaded okay
            }
            else {
                //image did not upload
                $uploadOk = 0;
            }
        }
    }

    if($uploadOk) {
        $post = new Post($con, $userLoggedIn); //Creating Instance of Class with the parameters
        $post->submitPost($_POST['post_text'], 'none', $imageName);
    }
    else {
        echo "<div style='text-align:center;' class='alert alert-danger'>
				$errorMessage
			</div>";
    }
}

$profileData = new ProfileData($con, $userLoggedIn);
$followerCount = $profileData->getFollowerCount();
$num_friends = substr_count($user['friend_array'], ",") - 1;

?>
<div class="wrapper">
    <div class="sec">
        <div id="userBox" class="user_details column">
            
            <a href="<?php echo $userLoggedIn; ?>"> <img src="<?php echo $user['profile_pic']; ?>" alt="User Profile Picture"></a>

            <div class="user_details_left_right">
                <a href="<?php echo $userLoggedIn; ?>">
                <?php
                    echo $user['first_name'] . " " . $user['last_name'];
                ?>
                </a>
                <br>
                <?php echo "Followers: " . $followerCount . "<br>" .
                        "Friends: " . $num_friends . "<br>" . 
                        "Posts: " . $user['num_posts']. "<br>" .
                        "Likes: " . $user['num_likes'] . "<br>";
                ?>
                </div>
        </div>
        
        <div class="newbox">
            <div id="navsideboxes" class="quicknavBox">
            <h4 style="font-family: Nunito_Bold">Quick Navigation</h4>
                <div class='navigationHomeItem'>
                    <div class='quickButtons'>
                        <div class='quicknavButton'>
                        <a href="<?php echo $userLoggedIn; ?>" class="profile">
                            <?php
                            echo "<img src='" . $user['profile_pic'] ."' class='small_profile_pic_nav'>";   //profile image
                            ?>
                            <span>Profile</span>
                        </a>    
                        </div>
                        <div class='quicknavButton'>
                            <a href='tutorials.php'>
                                <img src='assets/images/icons/tutIcon.png'>
                                <span>Tutorials</span>
                            </a>
                        </div>
                        <div class='quicknavButton'>
                            <a href='trending.php'>
                                <img src='assets/images/icons/trendIcon.png'>
                                <span>Trending Tutorials</span>
                            </a>
                        </div>
                        <div class='quicknavButton'>
                            <a href='followers.php'>
                                <img src='assets/images/icons/followersIcon.png'>
                                <span>Follower's Tutorials</span>
                            </a>
                        </div>
                        <div class='quicknavButton'>
                            <a href='likedTutorials.php'>
                                <img src='assets/images/icons/likedIcon.png'>
                                <span>Liked Tutorials</span>
                            </a>
                        </div>
                        <div class='quicknavButton'>
                            <a href='tutorials.php'>
                                <img src='assets/images/icons/settingsIcon.png'>
                                <span>Settings</span>
                            </a>
                        </div>
                        <div class='quicknavButton'>
                            <a href='includes/handlers/logout.php'>
                                <img src='assets/images/icons/logoutIcon.png'>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
                </div>
        </div>
        <div class="secondnewbox">
            <div id="navsideboxes" class="trendBox">
                <h4 style="font-family: Nunito_Bold">Trending Words</h4>
                <div class="trendswords">
                    <?php
                    //Retrieve top 9 trending words
                    $query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");
                    //loop through each word from query to be displayed
                    foreach ($query as $row) {

                        $word = $row['title'];
                        $word_dot = strlen($word) >= 14 ? "..." : "";

                        $trimmed_word = str_split($word, 14);
                        $trimmed_word = $trimmed_word[0];

                        echo "<div style'padding: 1px'>";
                        echo $trimmed_word . $word_dot;
                        echo "<br></div><br>";
                    }
                    ?>
                </div>
            </div>
        </div> 

        <div class="thirdnewbox">
            <div id="navsideboxes" class="followerBox">
                <h4 style="font-family: Nunito_Bold">Following Users</h4>
                <?php
                    $navigationProvider = new NavigationMenuProvider($con, $userLoggedInObj);
                    echo $navigationProvider->Create();
                ?>
            </div>
        </div>
        
    </div>

    <div class="newsfeedContainer">
        <div class="mainNewsfeed">
                <!-- top form box (To post statuses) -->
            <div class="column"> 
                <form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="fileToUpload" id="fileToUpload"> 
                    <textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>   
                    <input type="submit" name="post" id="post_botton" value="Post">
                    <hr>    
                </form>

                <div class="posts_area">

                </div>
                <img id="#loading" src="assets/images/icons/loadposts.gif">

            </div> 
        </div>
    </div>

<!-- Infinite Scoll function -->
<!-- Loads all posts to limit, with loading gif -->
<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(document).ready(function() {

        $('#loading').show();

        //Original ajax request for loading first posts
        $.ajax({
            url: "includes/handlers/ajax_load_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn,
            cache:false,

            success: function(data) {
                $('#loading').hide();
                $('.posts_area').html(data);
            }
        });

        $(window).scroll(function() {
            var height = $('.posts_area').height(); //Div containing posts
            var scroll_top = $(this).scrollTop();
            var page = $('.posts_area').find('.nextPage').val();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();

            if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                $('#loading').show();

                var ajaxReq = $.ajax({
                    url: "includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                    cache:false,

                    success: function(response) {
                        $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                        $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
                        $('#loading').hide();
                        $('.posts_area').append(response);
                    }
                });

            } //End if

            return false;

        }); //End (window).scroll(function())


    });

</script>


    </div>
</body>
</html>
