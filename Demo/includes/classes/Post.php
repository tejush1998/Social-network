<?php
class Post {
	private  $user_obj;
	private  $con;

	public function __construct($con, $user) {
		$this->con=$con;
		$this->user_obj  = new User($con, $user);

	}

	public function submitPost($body, $user_to) {
		
		$body = strip_tags($body);
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces to make sure theres text inside that post

		if($check_empty != "") {

			$date_added = date("Y-m-d H:i:s");
			//get username
			$added_by = $this->user_obj->getUsername();
			//why not using session variables, itll get complex?

			//if user is on own profile, user_to is 'none'

			//like writing on someones timeline or own timeline
			if($user_to == $added_by ) {
				$user_to = "none";

			}

			//insert post

		$query = mysqli_query($this->con, "insert into posts values('','$body','$added_by','$user_to','$date_added', 'no', 'no', '0')");

		$returned_id = mysqli_insert_id($this->con);

		//insert notification

		//update post count for user
		$num_posts = $this->user_obj->getNumPosts();
		$num_posts++;
		$update_query = mysqli_query($this->con, "update users set num_posts='$num_posts' where username='$added_by'");



		}
	}

	public function loadPostsFriends($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start =0;
		else
			$start = ($page - 1) * $limit;


		$str = ""; //string to return
		$data_query = mysqli_query($this->con, "select * from posts where deleted='no' order by id desc");

		if(mysqli_num_rows($data_query) > 0) {

			$num_iterations = 0; //num of results checked not necessarily posted
			$count=1;

		while($row = mysqli_fetch_array($data_query)) {
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
			$date_time = $row['date_added'];

			//prepare user_to string so it can be included even if not posted to a user
			if($row['user_to'] == "none") {
				$user_to="";
			}
			else {
				$user_to_obj = new User($this->con, $row['user_to']);
				$user_to_name = $user_to_obj->getFirstAndLastName();
				$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
			}

			//check if user posted has acc closed

			$added_by_obj = new User($this->con, $added_by);
			if($added_by_obj->isClosed()) {
				continue;
			}

			$user_logged_obj = new User($this->con, $userLoggedIn);
			if($user_logged_obj->isFriend($added_by)){

			if($num_iterations++ < $start)
				continue; //send to top again

			//once 10 posts have been loaded, break
			if($count > $limit) {
				break;
			}
			else {
				$count++;
			}

			if($userLoggedIn == $added_by) 
				$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
			else 
				$delete_button = "";

			$user_details_query = mysqli_query($this->con, "select first_name, last_name, profile_pic from users where username='$added_by'");
			$user_row = mysqli_fetch_array($user_details_query);
			$first_name = $user_row['first_name'];
			$last_name = $user_row['last_name'];
			$profile_pic = $user_row['profile_pic'];


			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_time);
			$end_date = new DateTime($date_time_now);
			$interval = $start_date->diff($end_date);
			if($interval->y >=1) {
				if($interval == 1)
					$time_message = $interval ->y . " year ago";
				else
					$time_message = $interval ->y . " years ago";
			}
			else if ($interval-> m >=1) {
				
				if($interval->d ==0) {
					$days = " ago"; 
				}
				else if($interval->d ==1 ) {
					$days = $interval->d . " days ago";
				}
				else  {
						$days = $interval->d . " days ago";
				}

				if($interval->m ==1) {
					$time_message = $interval->m . " month". $days;
				}

				else {
					$time_message = $interval->m . " months". $days;
				}
			}

			else if($interval->d >= 1) {

				if($interval->d ==1 ) {
					$time_message = "Yesterday";
				}
				else  {
						$time_message = $interval->d . " days ago";
				}

			}
			else if($interval->h >= 1) {

				 if($interval->h ==1 ) {
					$time_message = $interval->h . " hour ago";
				}
				else  {
						$time_message = $interval->h . " hours ago";
				}

			}
			else if($interval->i >= 1) {

				 if($interval->i ==1 ) {
					$time_message = $interval->i . " minute ago";
				}
				else  {
						$time_message = $interval->i . " minutes ago";
				}

			}
			else {

				 if($interval->s < 30) {
					$time_message = "Just now";
				}
				else  {
						$time_message = $interval->s . " seconds ago";
				}

			}

			$str .= "<div class='status_post'>
					<div class='post_profile_pic'>
					<img src='$profile_pic' width='50'>
					</div>

					<div class='posted_by' style='color:#ACACAC;'>
					<a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
					$delete_button
					</div>
					<div id='post_body'>$body<br></div>


					</div>
					<hr>";
				}

				?>
				<script>
					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Confirm", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
							

						});
					});

				</script>
				<?php

		} //end while

		if($count > $limit)
			$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'><input type = 'hidden' class='noMorePosts' value='false'>";
		else 
			$str .= "<input type = 'hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		//if less than 10 posts to see then html gets appended with false, ajax is getting all its info from this html, show it stops looping
	}

		echo $str; //final output
	}

	public function loadProfilePosts($data, $limit) {

		$page = $data['page'];
		$profileUser  = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start =0;
		else
			$start = ($page - 1) * $limit;


		$str = ""; //string to return
		$data_query = mysqli_query($this->con, "select * from posts where deleted='no' and ((added_by='$profileUser' and user_to='none') or user_to='$profileUser') order by id desc");

		if(mysqli_num_rows($data_query) > 0) {

			$num_iterations = 0; //num of results checked not necessarily posted
			$count=1;

		while($row = mysqli_fetch_array($data_query)) {
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
			$date_time = $row['date_added'];


			if($num_iterations++ < $start)
				continue; //send to top again

			//once 10 posts have been loaded, break
			if($count > $limit) {
				break;
			}
			else {
				$count++;
			}

			if($userLoggedIn == $added_by) 
				$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
			else 
				$delete_button = "";

			$user_details_query = mysqli_query($this->con, "select first_name, last_name, profile_pic from users where username='$added_by'");
			$user_row = mysqli_fetch_array($user_details_query);
			$first_name = $user_row['first_name'];
			$last_name = $user_row['last_name'];
			$profile_pic = $user_row['profile_pic'];


			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_time);
			$end_date = new DateTime($date_time_now);
			$interval = $start_date->diff($end_date);
			if($interval->y >=1) {
				if($interval == 1)
					$time_message = $interval ->y . " year ago";
				else
					$time_message = $interval ->y . " years ago";
			}
			else if ($interval-> m >=1) {
				
				if($interval->d ==0) {
					$days = " ago"; 
				}
				else if($interval->d ==1 ) {
					$days = $interval->d . " days ago";
				}
				else  {
						$days = $interval->d . " days ago";
				}

				if($interval->m ==1) {
					$time_message = $interval->m . " month". $days;
				}

				else {
					$time_message = $interval->m . " months". $days;
				}
			}

			else if($interval->d >= 1) {

				if($interval->d ==1 ) {
					$time_message = "Yesterday";
				}
				else  {
						$time_message = $interval->d . " days ago";
				}

			}
			else if($interval->h >= 1) {

				 if($interval->h ==1 ) {
					$time_message = $interval->h . " hour ago";
				}
				else  {
						$time_message = $interval->h . " hours ago";
				}

			}
			else if($interval->i >= 1) {

				 if($interval->i ==1 ) {
					$time_message = $interval->i . " minute ago";
				}
				else  {
						$time_message = $interval->i . " minutes ago";
				}

			}
			else {

				 if($interval->s < 30) {
					$time_message = "Just now";
				}
				else  {
						$time_message = $interval->s . " seconds ago";
				}

			}

			$str .= "<div class='status_post'>
					<div class='post_profile_pic'>
					<img src='$profile_pic' width='50'>
					</div>

					<div class='posted_by' style='color:#ACACAC;'>
					<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
					$delete_button
					</div>
					<div id='post_body'>$body<br></div>


					</div>
					<hr>";

		?>
				<script>
					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Confirm", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
							

						});
					});

				</script>
				<?php
			}

		if($count > $limit)
			$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'><input type = 'hidden' class='noMorePosts' value='false'>";
		else 
			$str .= "<input type = 'hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		//if less than 10 posts to see then html gets appended with false, ajax is getting all its info from this html, show it stops looping
	}

		echo $str; //final output
	}

}


?>