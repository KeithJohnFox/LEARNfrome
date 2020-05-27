<?php  
if(isset($_POST['update_details'])) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];

	// INPUT SANITIZATION
	$first_name = strip_tags($first_name);  //removes any html tags
	$first_name = mysqli_real_escape_string($con, $first_name); 
	
	$last_name = strip_tags($last_name);  //removes any html tags
	$last_name = mysqli_real_escape_string($con, $last_name); 
	
	$email = filter_var($email, FILTER_VALIDATE_EMAIL);

	// $email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");

	$stmt = $con->prepare("SELECT * FROM users WHERE email= ?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$email_check = $stmt->get_result();

	$row = mysqli_fetch_array($email_check);
	$matched_user = $row['username'];

	if($matched_user == "" || $matched_user == $userLoggedIn) {
		$message = "<div style ='color: green;'>Details updated!<br><br></div>";

		$query = $con->prepare("UPDATE users SET first_name= ?, last_name= ?, email= ? WHERE username= ?");
		$query->bind_param("ssss", $first_name, $last_name, $email, $userLoggedIn);
		$query->execute();
	}
	else 
		$message = "<div style ='color: red;'>That email is already in use!<br><br></div>";
}
else 
	$message = "";


//**************************************************

if(isset($_POST['update_password'])) {

	//SANITIZATION
	$old_password = strip_tags($_POST['old_password']);
	$old_password = preg_replace('/[^\p{L}\p{N}\s]/u', '', $old_password); //Replace these symbols with nothing
	$old_password = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $old_password); //Replace javascript tags and symbols with nothing
	
	$new_password_1 = strip_tags($_POST['new_password_1']);
	$new_password_1 = preg_replace('/[^\p{L}\p{N}\s]/u', '', $new_password_1); //Replace these symbols with nothing
	$new_password_1 = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $new_password_1); //Replace javascript tags and symbols with nothing

	$new_password_2 = strip_tags($_POST['new_password_2']);
	$new_password_2 = preg_replace('/[^\p{L}\p{N}\s]/u', '', $new_password_2); //Replace these symbols with nothing
	$new_password_2 = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $new_password_2); //Replace javascript tags and symbols with nothing

	// $password_query = mysqli_query($con, "SELECT password FROM users WHERE username='$userLoggedIn'");

	$stmt = $con->prepare("SELECT password FROM users WHERE username= ?");
	$stmt->bind_param("s", $userLoggedIn);
	$stmt->execute();
	$password_query = $stmt->get_result();
	$row = mysqli_fetch_array($password_query);

	$db_password = $row['password'];

	$old_password = hash("sha512", $old_password); //hashing with sha512 is a secuirty encryption


	if($old_password == $db_password) {

		if($new_password_1 == $new_password_2) {

			if(strlen($new_password_1) <= 4) {
				$password_message = "Sorry, your password must be greater than 4 characters<br><br>";
			}	
			else {
				$new_pass = hash("sha512", $new_password_1);
				// $password_query = mysqli_query($con, "UPDATE users SET password='$new_pass' WHERE username='$userLoggedIn'");
				$password_query = $con->prepare("UPDATE users SET password= ? WHERE username= ?");
				$password_query->bind_param("ss", $new_pass, $userLoggedIn);
				$password_query->execute();
				$password_message = "Your Password has been changed!<br><br>";
			}


		}
		//display error message
		else {
			$password_message = "New passwords do not match, try again!<br><br>";
		}

	}
	else {
			$password_message = "Old password is incorrect, try again! <br><br>";
	}

}
else {
	$password_message = "";
}

//Closed account handler
if(isset($_POST['close_account'])) {
	header("Location: close_account.php");
}


?>