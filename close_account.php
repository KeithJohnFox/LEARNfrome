<?php
include("includes/header.php");

if(isset($_POST['cancel'])) {
	header("Location: settings.php");
}

//If user closes account then set account to yes in user_closed column
if(isset($_POST['close_account'])) {
	$close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
	session_destroy(); //Destroy session
	header("Location: register.php"); //send user to register page
}
?>

<div class="settingsWrap">
	<div class="closeContainer">
		<h4>Close Account</h4>
		Are you sure you want to close your account?<br><br>
		Closing your account will hide your profile and all your activity from other users on the platform.<br><br>
		You can re-open your account at any time by simply logging in again.<br><br>

		<form action="close_account.php" method="POST">
			<input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
			<input type="submit" name="cancel" id="update_details" value="Cancel" class="info settings_submit">
		</form>
	</div>
</div>