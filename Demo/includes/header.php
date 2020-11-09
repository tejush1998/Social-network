<?php
require 'config/config.php';

if(isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "select * from users where username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}
else {
	header("Location: register.php");

}
?>
<html>
<head>
	<title>ya</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
	</script>
	<script src="assets/js/bootstrap.js"></script>

	<!-- CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>

	<div class="left_bar">
		<div class ="logo">
			<a href="index.php">Swirlit</a>
		</div>

		<nav>
			<a href="<?php echo $userLoggedIn; ?>"><?php echo $user['first_name']; ?></a>
			<a href="#"><i class="fa fa-home fa-lg"></i> </a>
			<a href="#"><i class="fa fa-envelope fa-lg"></i> </a>
			<a href="#"><i class="fa fa-bell-o fa-lg"></i> </a>
			<a href="#"><i class="fa fa-users fa-lg"></i> </a>
			<a href="#"><i class="fa fa-cog fa-lg"></i> </a>
			<a href="includes/handlers/logout.php"><i class="fa fa-sign-out fa-lg"></i> </a>
			
		</nav>
  
</div>

<div class="wrapper">