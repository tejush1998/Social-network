<?php

if(isset($_POST['login_button'])) {

	$email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //sanitize email

	$_SESSION['log_email'] = $email; //Store email into session variable

	$password = md5($_POST['log_password']); //Get password 

	$check_database_query = mysqli_query($con, "select * from users where email='$email' and password = '$password'");

	$check_login_query = mysqli_num_rows($check_database_query);
	if($check_login_query == 1) {

		$row = mysqli_fetch_array($check_database_query);
		$username = $row['username']; //getting the username by fetching

		$user_closed_query = mysqli_query($con, "select * from users where email = '$email' and user_closed= 'yes'");
		if(mysqli_num_rows($user_closed_query) == 1) {
			$reopen_account = mysqli_query($con, "update users set user_closed='no' where email = '$email'");
		}

		$_SESSION['username'] = $username; //we'll check this frequently to see if user logged in

		header("Location:index.php");
		exit();
	}
	else {
		array_push($error_array, "Email or password incorrect<br>");
	}
}

?>