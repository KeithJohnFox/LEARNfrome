<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php"); //settings handler import
?>

<div class="settingsWrap">
	<div class="settingsContainer">
		<h4 style="font-family: Nunito_SemiBold; font-size: 22px;"> Account Settings</h4>
		<?php
		echo "<img src='" . $user['profile_pic'] ."' class='small_profile_pic'>";   //profile image
		?>
		<br>
		<div class="uploadLink">
			
			<a style="font-family: Nunito_SemiBold; font-size: 17px;" href="upload.php">
				<img src='assets/images/icons/uploadLink.png'>
				Upload a new profile picture
			</a> <br><br><br>
		</div>



	<!--    Gather user logged in details (firstname, lastname, email)-->
		<?php
		$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
		$row = mysqli_fetch_array($user_data_query);

		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$email = $row['email'];
		?>

	<!--    Display input boxes to change first, last and email details-->

		

		<form id="userDetailsBox" action="settings.php" method="POST">
			First Name: <input type="text" name="first_name" value="<?php echo $first_name; ?>" id="settings_input"><br>
			Last Name: <input type="text" name="last_name" value="<?php echo $last_name; ?>" id="settings_input"><br>
			Email: <input type="text" name="email" value="<?php echo $email; ?>" id="settings_input"><br>

	<!--        Message var from "handler settings.php" output either details updated or invalid details or in use-->
			<?php echo $message; ?>

			<input type="submit" name="update_details" id="save_details" value="Update Details" class="info settings_submit"><br>
		</form>

		<h4 style="margin-top: 30px; font-family: Nunito_SemiBold;">Change Password</h4>
		<form id="passBox" action="settings.php" method="POST">
			Old Password: <input type="password" name="old_password" id="settings_input"><br>
			New Password: <input type="password" name="new_password_1" id="settings_input"><br>
			New Password Again: <input type="password" name="new_password_2" id="settings_input"><br>

	<!--        password handling message output-->
			<?php echo $password_message; ?>

			<input type="submit" name="update_password" id="save_details" value="Update Password" class="info settings_submit"><br>
			<br>
			<div class="logoutButton" >
				<img src='assets/images/icons/logoutIcon.png' href="includes/handlers/logout.php">
				<a href="includes/handlers/logout.php">Logout</a>  
			</div>  
		</form>

		<div class="closeAccount">
		<!--    This Deactivates your account till you login next -->
			<h4>Deactivate your Account</h4>
			<form action="settings.php" method="POST">
				<input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
			</form>
		</div>

	</div>
</div>