<?php
include("includes/header.php");

if(isset($_GET['profile_username'])) {
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "select * from users where username='$username'");
	$user_array= mysqli_fetch_array($user_details_query);

	$num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}
?>

<div class="profile_left">
	<img height = "100px" src="<?php echo $user_array['profile_pic']; ?>">

	<div class="profile_info">
		<p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
		<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
		<p><?php echo "Friends: " . $num_friends ?></p>

	</div>
	</div>

<div class="main_column column">
	
	<div class="posts_area"> </div>

</div>

<script> 
	
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	var profileUsername = '<?php echo $username; ?>' ;

	$(document).ready(function() { //ready fucntion is javascript/jquery. while isset is just php

		$('#loading').show();

		//original ajax request
		$.ajax({
			url: "includes/handlers/ajax_load_profile_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername, //this becomes request object of ajax.php and calls loadspostsfriends

			success: function(data) {
				$('#loading').hide();
				$('.posts_area').html(data);
			}

		});

		$(window).scroll(function() {

			var height = $('.posts_area').height(); //div cointaining posts
			var scroll_top = $(this).scrollTop(); //wherver the scroll top is
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();
			//were getting page and nomoreposts from post.php , if nomore is true then below code will not be run

			if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false'){
				$('#loading').show();
				alert("hello");


			var ajaxReq = $.ajax({
			url: "includes/handlers/ajax_load_profile_posts.php",
			type: "POST",
			data: "page=" + page +"&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
			cache: false,

			success: function(response) {
				$('.posts_area').find('.nextPage').remove(); //removes current next page
				$('.posts_area').find('.noMorePosts').remove(); //removes current next nextPage

				$('#loading').hide();
				$('.posts_area').append(response);
			}

		});

			} //end if 

			return false;


		});//end window scroll function


	});


</script>


</div>
</body> 
</html>