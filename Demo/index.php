<?php
include("includes/header.php"); 
$num_friends = (substr_count($user['friend_array'], ",")) - 1;

//userloggedin is userid, user is full details about user, both these come from header file

if(isset($_POST['post'])) {
	$post = new Post ($con, $userLoggedIn);
	$post->submitPost($_POST['post_text'], 'none');
}
?>
<div class="user_details column">
	<a href="<?php echo $userLoggedIn; ?>"> <img width="100" height="100" src="<?php echo $user['profile_pic']; ?>"> </a>
		<div class="user_details_left_right">
			<a href="<?php echo $userLoggedIn; ?>">

			<?php echo $user['first_name'] . " " . $user['last_name'];

		?>
		</a>
		<br>
		<?php echo "Posts: " . $user['num_posts']. "<br>" ; 
		
		?>


		</div>
</div>


<div class="main_column column">
	<form class="post_form" action="index.php" method="POST">
		<textarea name ="post_text" id="post_text" placeholder= "Got something to say?"></textarea>
		<input type="submit" name="post" id="post_button" value="Post"> 
		<hr>

	</form>


	<div class="posts_area"></div>

</div>

<script> //basically ajax is sending to post and post is sending back if available more, then ajax scrolls then post again till posts sends no more available

//AJAX Works through Javascript
	
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() { //ready fucntion is javascript/jquery. while isset is just php

		$('#loading').show();

		//original ajax request
		$.ajax({
			url: "includes/handlers/ajax_load_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn, //this becomes request object of ajax.php and calls loadspostsfriends

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
			url: "includes/handlers/ajax_load_posts.php",
			type: "POST",
			data: "page=" + page +"&userLoggedIn=" + userLoggedIn,
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