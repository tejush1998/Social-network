<?php
ob_start(); //Turns on output buffering
session_start(); //values of these variables will be stored

$timezone = date_default_timezone_set("Asia/Calcutta");

$con = mysqli_connect("localhost", "root", "", "social");
if(mysqli_connect_errno() ) {
	echo "Failed to connect:" . mysqli_connect_errno();
}
?>