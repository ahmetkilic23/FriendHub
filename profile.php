<?php
	session_start();
	require "db.php" ;
	$userData=$_SESSION["user"];

	$userId=$userData["user_id"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
		integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="style.css">
	<title><?php echo $_SESSION["user"]["user_name"]." ".$_SESSION["user"]["surname"]?></title>
</head>

<body>
	<div class="menu-bar">
		<h1 class="logo"><span>FriendHub</span></h1>
		<ul>
			<li class="profileImg"><img src="images/<?php echo( $_SESSION["user"]["profilepic"])?> " alt=""></li>
			<li>
				<p class="username"><?php echo $_SESSION["user"]["user_name"]. ' '.$_SESSION["user"]["surname"]?> </p>
			</li>
			<li class="bell">
				<div <?php $sql = "SELECT * from `notification` WHERE to_user_id = $userId";
					$rs = $db->query($sql);

					if ($rs->rowCount() == 0) {
						echo 'style="display:none"';
					} else {
						echo '';
					}

					?> class="notification"><?= $rs->rowCount()?></div> <span class="bell fa fa-bell"></span>
			<li class="home"><a href="homePage.php"><img src="images/house.svg" alt=""></a></li>
			<li class="logout"><a href="logout.php"><img src="images/box-arrow-right.svg" alt=""></a></li>
		</ul>
	</div>
	<div class="profile-timeline">
		<div class="profile-left">
			<div class="friends">
				<h3>Your Followers</h3>
				<?php
				$friends = getFriends($_SESSION["user"]["user_id"]);
				foreach($friends as $f){
					echo "<form action='delete.php' method='post'>";
					echo "<ul id='friendList'>";
					echo "<li class='friendProfileImg2'><img src='images/".$f["profilepic"]."'><div class='friendName'><p>".$f["user_name"]." ".$f["surname"]."</p></div></li>";
					echo "<li class= 'removeBtn'><input type='submit' value='REMOVE'> </li>";
					echo "<input type='hidden' name='friend_id' value=".$f["friend_id"].">";
					echo "<input type='hidden' name='friend2_id' value='".$userId."'>";
					echo "</ul>";
					echo "</form>";
				}
			?>
			</div>
		</div>
		<div class="profile-right">
			<h3>Your Posts</h3>
			<?php
			$yourPosts = getYourPost($_SESSION["user"]["user_id"]);
			if(empty($yourPosts)){
				echo "<p class='noPost'>You do not have any post yet!</p>";
			}else{
			foreach($yourPosts as $p){
				echo "<div class='post_bar'>";
				echo "<div class='post-top'>";
				echo "<div class='friendProfileImg'><img src='images/".$p["profilepic"]."'></div>";
				echo "<div class='friendName'><p>".$p["user_name"]." ".$p["surname"]."</p></div>";
				echo "<form action='delete.php' method='POST'>";
				echo "<input type='hidden' name='delete_id' value='".$p["id"].".'>";
				echo "<input type='hidden' name='post_id' value='".$p["profilepic"].".'>";
				echo "<input type='submit' name='deleteBtn' value='Delete' class='deleteBtn'>";
				echo "</form>";
				echo "</div>";
				echo "<div class='post-bottom'>";
				if(!empty($p['post_text']) && !empty($p['post_img'])){
					echo "<div class='texts'><p>".$p["post_text"]."</p></div>";
					echo "<div class='images'><img src='images/".$p["post_img"]."'></div>";
				}else if(!empty($p['post_text']) && empty($p['post_img'])){
					echo "<div class='texts'><p>".$p["post_text"]."</p></div>";
					echo "<div></div>";
				}else if(empty($p['post_text']) && !empty($p['post_img'])){
					echo "<div></div>";
					echo "<div class='images'><img src='images/".$p["post_img"]."'></div>";
				}
				echo "</div>";
				
				echo "</div>";
			}
		}
		?>
		</div>
	</div>

</body>

</html>
