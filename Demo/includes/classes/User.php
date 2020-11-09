<?php
class User {
	private  $user;
	private  $con;

	public function __construct($con, $user) {
		$this->con=$con;
		$user_details_query = mysqli_query($con, "select * from users where username='$user'");
		$this->user = mysqli_fetch_array($user_details_query);

	}

	public function getUsername() {
		return $this->user['username'];
	}

	public function getNumPosts() {

		$username = $this->user['username'];
		$query = mysqli_query($this->con, "select num_posts from users where username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['num_posts'];
	}

	public function getFirstAndLastName() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "select first_name, last_name from users where username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['first_name'] . " ". $row['last_name'] ;
	}

	public function isClosed() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "select user_closed from users where username='$username'");
		$row = mysqli_fetch_array($query);

		if($row['user_closed'] == 'yes')
			return true;
		else
			return false;
	}
}


?>