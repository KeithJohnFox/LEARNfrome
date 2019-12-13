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
    $(function(){

    	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    	var inProgress = false;

    	loadPosts(); //Load first posts

        $(window).scroll(function() {
        	var bottomElement = $(".status_post").last();
        	var noMorePosts = $('.posts_area').find('.noMorePosts').val();

            // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
            if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                loadPosts();
            }
        });

        function loadPosts() {
            if(inProgress) { //If it is already in the process of loading some posts, then it just returns
    			return;
    		}

    		inProgress = true;
    		$('#loading').show();

    		var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

    		$.ajax({
    			url: "includes/handlers/ajax_load_posts.php",
    			type: "POST",
    			data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
    			cache:false,

    			success: function(response) {
    				$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
    				$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
    				$('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage

    				$('#loading').hide();
    				$(".posts_area").append(response);

    				inProgress = false;
    			}
    		});
        }

        //Check if the element is in view
        function isElementInView (el) {
            var rect = el.getBoundingClientRect();

            return (
                rect.left >= 0 &&
                rect.top >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        }
    });
  </script>


    </div>
</body>
</html>
