<?php
//adding includes, cuts down on duplicated code, instead i recall the header eg nav bar to pages needed
include("includes/header.php");    //includes just pastes in code from header.php file



if(isset($_POST['post'])) {  //If the post button is pressed
    $post = new Post($con, $userLoggedIn);  //Creating Instance of Class with the parameters
    $post->submitPost($_POST['post_text'], 'none');
}
?>
    <div class="user_details column">
        <!-- href will direct to the profile page because of the .htaccess file allowing username in url to link to their profile -->
        <a href="<?php echo $userLoggedIn; ?>"> <img src="<?php echo $user['profile_pic']; ?>" alt="User Profile Picture"></a>

        <div class="user_details_left_right">
            <a href="<?php echo $userLoggedIn; ?>">
            <?php
                echo $user['first_name'] . " " . $user['last_name'];
            ?>
            </a>
            <br>
            <?php echo "Posts: " . $user['num_posts']. "<br>";
                echo "Likes: " . $user['num_likes'];
            ?>
            </div>
    </div>

    <!-- top form box (To post statuses) -->
    <div class="main_column column">
        <form class="post_form" action="index.php" method="POST">
            <textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>   <!-- displays text box -->
            <input type="submit" name="post" id="post_botton" value="Post">
            <hr>    <!-- Puts long Line underneath element -->
        </form>



        <div class="posts_area">

        </div>
        <img id="#loading" src="assets/images/icons/loadposts.gif">

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
