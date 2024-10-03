<?php

require "./db.php";

session_start();

if (isset($_POST['senderId'])) {
	extract($_POST);



	try {
		$sql2 = "INSERT INTO notification (to_user_id,from_user_id,message) values (?,?,?)";
		$stmt = $db->prepare($sql2);
		$stmt->execute([$userId, $senderId, "wants to follow you"]);
		echo "<p> Request send to {$userId} !</p>";
		echo "<p> You are redirected to home page</p>";
	} catch (PDOException $e) {
	}
}

if (isset($_POST["searchUser"])) {
	$search = $_POST["searchUser"];
	$inputs = explode(" ", $search);
	$user = $_POST["user"];

	$userData = $_SESSION["user"];
	$userId = $userData["user_id"];
	$sql3 = "SELECT * from `user` WHERE user_name = '$inputs[0]' AND user_id != $userId";
	$rs = $db->query($sql3);
	if (isset($inputs[1])) {
		$sql = "SELECT * from `user` WHERE user_name = '$inputs[0]' AND surname='$inputs[1]' AND user_id != $userId";
		$rs = $db->query($sql);
	}



	if ($rs->rowCount() == 0) {
		$sql2 = "SELECT * from `user` WHERE email = '$search' AND user_id != $userId";
		$rs = $db->query($sql2);
	}

	$rows = $rs->fetchAll(PDO::FETCH_ASSOC);


	foreach ($rows as $row) {
?>
		<ul class='search-list'>
			<li><img class='search-pp' src='images/<?= $row["profilepic"]; ?>'></li>
			<li class='name'><?= $row["user_name"]; ?></li>
			<li><?= $row["surname"]; ?></li>
			<li> <span class=" fa-solid fa-plus" data-user=<?= $row["user_id"]   ?> data-sender=<?= $user ?>></span></li>
		</ul>


<?php
	}
	if ($rs->rowCount() == 0) {
		echo "<p>No user found!</p>";
	}
}

echo "<p><a href='homePage.php'> Go to home page</a></p>"

?>



<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="style.css">
	<title>Document</title>


	<script>
		$(document).ready(function() {
			$('.fa-plus').on('click', function() {
				var userid = $(this).data('user');
				var senderid = $(this).data('sender');
				var friend = $(this);

				console.log("user " + userid);
				console.log("sender " + senderid);

				$.ajax({
					url: 'search.php',
					type: 'post',
					data: {
						'userId': userid,
						'senderId': senderid
					},
					success: function(response) {
						console.log(friend);

						friend.removeClass('fa-plus');
						friend.removeClass('fa-solid');
						friend.addClass('fa-solid');
						friend.addClass('fa-check');
						$(".fa-check").off('click');
					}
				})

			});

		});
	</script>

</head>

<body>

</body>

</html>