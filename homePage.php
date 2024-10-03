<?php
session_start();
require "db.php";
// check if the user authenticated before
if (!validSession()) {
	header("Location: login.php?error"); // redirect to login page
	exit;
}

$userData = $_SESSION["user"];
$userId = $userData["user_id"];
//posting starts here

if (isset($_POST['check'])) {
	extract($_POST);
	$stmt = $db->prepare("INSERT INTO friends(follower_id, followed_id) VALUES (?,?)");
	$stmt->execute([$_POST['fid'], $userId]);
	$notif = getNotifications($userId);
	$stmt2 = $db->prepare("DELETE FROM notification WHERE id = ? ");
	$stmt2->execute([$notif[$_POST['row']]['id']]);
}

if (isset($_POST['uncheck'])) {
	extract($_POST);
var_dump($_POST);
	$notif = getNotifications($userId);
	$stmt = $db->prepare("DELETE FROM notification WHERE id = ? ");
	$stmt->execute([$notif[$_POST['row']]['id']]);
}


if (isset($_POST["postBtn"])) {
	extract($_POST);
	//var_dump($_FILES['file']);
	$targetDir = "images/";
	$fileName = basename($_FILES["file"]["name"]);
	$targetFilePath = $targetDir . $fileName;
	$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
	$allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf');

	if (!($_FILES['file']['name'] == "")) {
		if (in_array($fileType, $allowTypes)) {
			// Upload file to server
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
				// Check file size
				if ($_FILES["file"]["size"] < 500000) {
					// Insert image file name into database
					$sql = ("INSERT INTO posts (user_id,post_img, post_text) VALUES (?,?,?)");
					$stmt = $db->prepare($sql);

					$stmt->execute([$_SESSION["user"]["user_id"], $fileName, $textPost]);
					$msg = "Post uploaded successfully!";
				} else {
					echo "Sorry, your file is too large.";
				}
			}
		} else {
			echo "You cannot upload that file type";
		}
	} else {
		if (!empty($textPost)) {


			$sql = ("INSERT INTO posts (user_id, post_text) VALUES (?,?)");
			$stmt = $db->prepare($sql);
			$stmt->execute([$_SESSION["user"]["user_id"], $textPost]);
			$msg = "Post uploaded Successfully";
		} else {
			$msg = "Sorry, your post must include something.";
		}
	}
}




if (isset($_POST['comment'])) {
	extract($_POST);

	$posts = getPost($_SESSION["user"]["user_id"]);

	$stmt = $db->prepare("INSERT INTO comment (post_id,user_id,text) VALUES (?,?,?)");
	$stmt->execute([$postid, $userId, $comment]);


	foreach ($posts as $p) {

?>


<?php $comments = getComment($p['id']);

		// *****
		foreach ($comments as $c) {
			if ($c['text'] != "") {
				if ($c['post_id'] == $postid) {
		?>
<ul>
	<li> <img src='images/<?= $c['profilepic'] ?>'></li>
	<li><?= $c['user_name'] ?></li>
	<li><?= $c['text'] ?></li>
</ul>
<?php
				}
			}
		}


		?>

<?php

	}
	exit;
}

if (isset($_POST['liked'])) :
	extract($_POST);
	$postid = $_POST['postid'];

	$stmt = $db->query("SELECT * FROM posts WHERE id=$postid");

	$rs = $stmt->fetch();
	$n = $rs['like_count'];

	$st = $db->prepare("INSERT INTO likes (user_id, post_id) VALUES ($userId,$postid)");
	$st->execute();

	$db->query("UPDATE posts SET like_count= $n+1 WHERE id=$postid");
	echo $n + 1;
	exit;
endif;

if (isset($_POST['unliked'])) :
	extract($_POST);
	$postid = $_POST['postid'];

	$stmt = $db->query("SELECT * FROM posts WHERE id=$postid");

	$rs = $stmt->fetch();
	$n = $rs['like_count'];

	$st = $db->prepare("DELETE FROM likes WHERE user_id=$userId AND post_id=$postid");
	$st->execute();

	$db->query("UPDATE posts SET like_count= $n-1 WHERE id=$postid");
	echo $n - 1;
	exit;
endif;

if (isset($_POST['notification'])) :



	exit;
endif;


?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Home Page</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
		integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
		integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<!-- <link rel="stylesheet" href="style.css"> -->
	<link rel="stylesheet" type="text/css" href="style.css?<?php echo time(); ?>" /> <!-- deneme -->







</head>

<body>
	<?php
	if (isset($msg)) {
		echo "<p class='msg'>", $msg, "</p>";
	}
	?>

	<div class="menu-bar">
		<h1 class="logo"><span> FriendHub</span></h1>
		<ul>
			<form id="searchForm" action="search.php" method="post">

				<li class="search">
					<input type="search" name="searchUser" placeholder="Search for a friend">

				</li>
				<input type='hidden' name='user' value="<?php echo $userData["user_id"]; ?>">
				<li><button type="submit" class='searchBtn'><img src="images/search.svg"></button></li>
			</form>

			<!-- <li class="search">
				<form action="" method="POST">
					<input type="search" name="searchUser" placeholder="Search for a friend">
				
			</li>
			<span class="searchImg"><button type="submit"><img src="images/search.svg"></i></button></span>
			</form> -->

			<li class="profileImg"><img src="images/<?php echo ($_SESSION["user"]["profilepic"]) ?> " alt=""></li>
			<li>
				<p class="username"><?php echo $_SESSION["user"]["user_name"] . ' ' . $_SESSION["user"]["surname"] ?>
				</p>
				<div class="dropdown-menu">
					<ul>
						<img src="images/house.svg" alt="">
						<li><a href="profile.php">Profile</a></li>
						<img src="images/box-arrow-right.svg" alt="">
						<li><a href="logout.php">Logout</a></li>
					</ul>
				</div>
			</li>

			<li class="bell">
				<!-- ajax denedğim için bunu commente aldım <a href="notifications.php" id="attach_box"><img src="images/bell.svg" alt=""></a> -->
				<div id="nav">
					<div <?php $sql = "SELECT * from `notification` WHERE to_user_id = $userId";
							$rs = $db->query($sql);

							if ($rs->rowCount() == 0) {
								echo 'style="display:none"';
							} else {
								echo '';
							}

							?> class="notification"><?= $rs->rowCount() ?></div>
					<span class="dropdown-toggle bell fa fa-bell"></span>
				</div>
			</li>
		</ul>
	</div>
	<div class='timeline'>
		<div class="rightmenu">
			<p class='notHeader'>Nofitications</p>
			<?php
			$notif = getNotifications($userId);
			$row = 0;
			foreach ($notif as $n) {

			?>
			<ul>
				<li>
					<p><?= $n['user_name'] . ' ' . $n['surname'] ?></p>
				</li>
				<li>
					<p><?= $n['message'] ?></p>
				</li>
				<?php
					if ($n['message'] != "Removed you from followers") { ?>
				<li><span data-name=<?php echo $n['user_name'] ?> data-surname=<?php echo $n['surname'] ?>
						data-fid=<?php echo $n['user_id'] ?> data-row=<?php echo $row; ?>
						class="fa-solid fa-circle-check"></span></li>
				<li><span data-fid=<?php echo $n['user_id'] ?> data-row=<?php echo $row;?>
						class="fa-solid fa-circle-xmark"></span></li>
				<?php } else { ?>
				<li><span data-name=<?php echo $n['user_name'] ?> data-surname=<?php echo $n['surname'] ?>
						data-fid=<?php echo $n['user_id']  ?> data-row=<?php echo $row; ?>
						class="fa-solid fa-circle-check"></span></li>
				<li><span data-fid=<?php echo $n['user_id'] ?>></span></li>
				<?php	} ?>
			</ul>
			<?php

				$row++;
			}
			?>
		</div>
		<div class="post-area">
			<div class="share-area">
				<form action="" method="post" enctype="multipart/form-data">
					<textarea id="textarea" type="text" name="textPost" class="postText"
						placeholder="Share what you are thinking..."></textarea>
					<input type="file" name="file" class='fileUpload'>
					<button name="postBtn" class='postBtn'>Post</button>
				</form>
			</div>

			<?php

			$posts = getPost($_SESSION["user"]["user_id"]);
			$perPage = 10; //Number of photos to display per page
			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$start = ($page-1)*$perPage;
			$displayPhotos = array_slice($posts,$start,$perPage);
			?>
			<div class='nextPost'>
				<?php foreach ($displayPhotos as $p) {
				 ?>
				<div class='post_bar'>
					<div class='post-top'>
						<div class='friendProfileImg'><img src="images/<?= $p["profilepic"]; ?>"></div>
						<div class='friendName'>
							<p> <?= $p["user_name"] ?> <?= $p["surname"]; ?> </p>
						</div>
					</div>
					<div class='post-bottom'>

						<?php
								if (!empty($p['post_text']) && !empty($p['post_img'])) {
									echo "<div class='texts'><p>" . $p["post_text"] . "</p></div>";
									echo "<div class='images'><img src='images/" . $p["post_img"] . "'></div>";
								} else if (!empty($p['post_text']) && empty($p['post_img'])) {
									echo "<div class='texts'><p>" . $p["post_text"] . "</p></div>";
									echo "<div></div>";
								} else if (empty($p['post_text']) && !empty($p['post_img'])) {
									echo "<div></div>";
									echo "<div class='images'><img src='images/" . $p["post_img"] . "'></div>";
								}
								?>

					</div>
					<div class='like-comment-part'>
						<div class='like-part'>

							<div class='post'>
								<?php
										$sql = "SELECT * FROM likes WHERE user_id=$userId AND post_id=" . $p["id"] . "";
										$rs = $db->query($sql);

										if ($rs->rowCount() == 1) : ?>
								<span class="unlike fa-solid fa-heart" data-id="<?= $p['id']; ?>"></span>
								<span class="like hide fa-regular fa-heart" data-id="<?= $p['id']; ?>"></span>

								<?php else : ?>
								<span class='like fa-regular fa-heart' data-id="<?= $p['id']; ?>"></span>
								<span class='unlike hide fa-solid fa-heart' data-id="<?= $p['id']; ?>"></span>

								<?php endif ?>
								<span class="like_count"><?= $p['like_count']; ?> likes</span>


							</div>
						</div>
						<div class='comment'>
							<textarea id='comment' placeholder='Add a comment...'></textarea>
							<img data-uid="<?= $p['id']; ?>" src='images/chat.svg'>
						</div>

					</div>

					<div class="disp-com">
						<?php $comments = getComment($p['id']);

								// *****
								for ($i = 0; $i < sizeof($comments); $i++) {
									if ($comments[$i]['text'] != "") {
										if ($comments[$i]['post_id'] == $p['id']) {
								?>
						<ul>
							<li> <img src='images/<?= $comments[$i]['profilepic'] ?>'></li>
							<li><?= $comments[$i]['user_name'] ?></li>
							<li><?= $comments[$i]['text'] ?></li>
							<li><?= $comments[$i]['created_at'] ?></li>
						</ul>
						<?php
										}
									}
								}


								?>

					</div>

				</div>


				<?php } if(count($posts) > ($start -$perPage)){
					$nextPage = $page + 1;
					echo "<form action='homePage.php' method='get'>
              		<input type='hidden' name='page' value='$nextPage' />
              		<button type='submit'>Next</button>
         			 </form>";
				} if ($page > 1) {
					$prevPage = $page - 1;
					echo "<form action='homePage.php' method='get'>
              		<input type='hidden' name='page' value='$prevPage' />
              		<button type='submit'>Previous</button>
         			 </form>";
				}
				
				?>
			</div>

			<!-- echo "<form action='homePageNext' method='post'>";
				echo "<a href='homePageNext.php'>Next</a>";
				echo "</form>"; -->


			<script>
			$(document).ready(function() {
				$('.like').on('click', function() {

					var postid = $(this).data('id');
					var post = $(this);

					$.ajax({

						url: 'homePage.php',
						type: 'post',
						data: {
							'liked': 1,
							'postid': postid
						},

						success: function(e) {
							post.parent().find('span.like_count').text(e + " likes");
							post.addClass('hide');
							post.siblings().removeClass('hide');
						}



					})

				});


				$('.unlike').on('click', function() {

					var postid = $(this).data('id');
					var post = $(this);

					$.ajax({

						url: 'homePage.php',
						type: 'post',
						data: {
							'unliked': 1,
							'postid': postid
						},

						success: function(e) {
							post.parent().find('span.like_count').text(e + " likes");
							post.addClass('hide');
							post.siblings().removeClass('hide');
						}



					})

				});


				$('.comment img').on('click', function() {
					var com = $(this).parent().find('#comment');
					var dispcom = $(this).parent().parent().parent().find('.disp-com');


					if (com.val() == "") {

						com.attr('placeholder', 'Can not be empty!');

						//PLACEHOLDER COLOR ı DEĞİŞTİR!!!


					} else {
						var comment = com.val();
						var postid = $(this).data('uid');


						$.ajax({
							url: 'homePage.php',
							type: 'post',
							data: {
								'comment': comment,
								'postid': postid,
							},
							success: function(e) {
								com.val("");
								com.attr('placeholder', 'Add a comment...');

								dispcom.html(e);
							},
							error: function(e) {


							}



						})
					}

				})


				$('.fa-circle-check').on('click', function() {
					var iconCheck = $(this)
					var fid = $(this).data('fid')
					var fname = $(this).data('name')
					var fsurname = $(this).data('surname')
					var row = $(this).data('row')
					$.ajax({
						url: 'homePage.php',
						type: 'post',
						data: {
							'fid': fid,
							'check': 1,
							'row': row
						},
						success: function(e) {

							iconCheck.parent().parent().text(' Done!')
						},
						error: function(e) {}

					})


				})


				$('.fa-circle-xmark').on('click', function() {
					var fid = $(this).data('fid')
					var iconCheck = $(this)
					var row = $(this).data('row')
					console.log(fid)
					$.ajax({
						url: 'homePage.php',
						type: 'post',
						data: {
							'fid': fid,
							'uncheck': 1,
							'row': row

						},
						success: function(e) {
							iconCheck.parent().parent().text('Done!')
						},
						error: function(e) {}

					})


				})




			});
			</script>




		</div>

	</div>


</body>

</html>
