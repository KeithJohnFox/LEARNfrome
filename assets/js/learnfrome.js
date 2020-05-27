$(document).ready(function() {

	//JS for Search box on nav bar
	$('#search_text_input').focus(function() {
		//If screen large enough eg not on mobile make the search bar wider
		if(window.matchMedia( "(min-width: 250px)" ).matches) {
			//animates box wider by 250px, 500 is speed how fast moves (mili secs)
			$(this).animate({width: '300px'}, 700);
		}
	});

	//Code allows user to click search icon to submit the search
	$('.button_holder').on('click', function() {
		//Submit the input value in the form called (name = search_form) in html on header.php page
		document.search_form.submit();
	});

	//Button for profile post
	$('#submit_profile_post').click(function(){

		$.ajax({
			type: "POST",
			url: "includes/handlers/ajax_submit_profile_post.php",
			data: $('form.profile_post').serialize(),
			success: function(msg) {
				$("#post_form").modal('hide');
				location.reload();
			},
			error: function() {
				alert('Failure');
			}
		});

	});
});

	//Sends request to ajax friend search page with the values, when it returns it sets data to the .results div
function getUsers(value, user){
	$.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".results").html(data);
	})
}
//Used to display dropdown messages when user clicks on message icon in nav bar
function getDropdownData(user, type) {

	if($(".dropdown_data_window").css("height") == "0px") {

		var pageName;

		if(type == 'notification') {
			pageName = "ajax_load_notifications.php";
			$("span").remove("#unread_notification");
		}
		else if (type == 'message') {
			pageName = "ajax_load_messages.php";
			$("span").remove("#unread_message");
		}

		var ajaxreq = $.ajax({
			url: "includes/handlers/" + pageName,
			type: "POST",
			data: "page=1&userLoggedIn=" + user,
			cache: false,

			success: function(response) {
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding" : "0px", "height": "280px", "border" : "1px solid #DADADA"});
				$("#dropdown_data_type").val(type);
			}

		});

	}
	else {
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height": "0px", "border" : "none"});
	}

}

//Code returns search results in dropdown div under search box
function getLiveSearchUsers(value, user) {

	//post means send data to ajaz_search.php page with 2 parameters vlaue (search input) and userloggedIn (username of user), whats returned is inseted into function(data)
	$.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn: user}, function(data) {
		//Accessing class search results footer empty on header.php page
		if($(".search_results_footer_empty")[0]) {
			//This code essentially says if results is empty originally then show the results if there are any
			$(".search_results_footer_empty").toggleClass("search_results_footer");
			//if search is empty then show results is empty if there are no results to show
			$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
		}
		//take data retrieved from search and add them to drop down menu
		$('.search_results').html(data);
		//this shows a link if you click it will take user to search page
		$('.search_results_footer').html("<a href='search.php?q=" + value + "'>See All Results</a>");

		//If search couldnt find any results then remove all elements (meaning a dropdown menu doesnt appear with no results
		if(data == "") {
			$('.search_results_footer').html("");
			$('.search_results_footer').toggleClass("search_results_footer_empty");
			$('.search_results_footer').toggleClass("search_results_footer");
		}

	});

}
//If you click anywhere outside a dropdown menu it will close the dropdown div
$(document).click(function(e){
	//Removes html attributes when you click away from dropdown
	if(e.target.class != "search_results" && e.target.id != "search_text_input") {
		//removes all html data
		$(".search_results").html("");
		//removes result of footer
		$('.search_results_footer').html("");
		$('.search_results_footer').toggleClass("search_results_footer_empty");
		$('.search_results_footer').toggleClass("search_results_footer");
	}
	//Removes css attributes when you click away from dropdown
	if(e.target.className != "dropdown_data_window") {

		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height" : "0px"});
	}


});

