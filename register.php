<?php
if (!empty($_POST)) {


	require "db.php";
	extract($_POST);



	//Saniting
	$sanitized_name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$sanitized_surname = filter_var($surname, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
	$sanitized_pass = filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	//default if no photo selected
	try {

		if (empty($_FILES["profilePic"]["name"])) {

			$targetDir = "images/";
			$fileName = basename("default.jpg");
			$targetFilePath = $targetDir . $fileName;
			$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
		}


		// Allow certain file formats
		$allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf');
		$hash_password = password_hash($sanitized_pass, PASSWORD_BCRYPT);



// insert new user to database
		$sql = ("insert into user (user_name, surname, email,password,bdate,profilepic) values (?,?,?,?,?,?)");
		$stmt = $db->prepare($sql);

		$emailCheck = $db->prepare("SELECT * FROM user WHERE email = ?");
		$emailCheck->execute([$email]);

		if (!(empty($name) || empty($surname) || empty($email) || empty($pass) || empty($bdate))) {

			if ($emailCheck->rowCount() == 0) {
				$stmt->execute([$name, $surname, $email, $hash_password, $bdate, $fileName]);


				header("Location: login.php");
			} else {
				$authError = true;
			}
		}
	} catch (PDOException $ex) {
		header("Location: register.php");
		exit;
	}
}

//Error messages
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (!preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $pass)) {
		$error_pass_match = "At least 8 chars, 1 uppercase letter, 1 lowercase letter, 1 number and 1 special char";
	}

	if (empty($_POST['name'])) {
		$name_error = "Please enter your name";
	}

	if (empty($_POST['surname'])) {
		$surname_error = "Please enter your surname";
	}

	if (empty($_POST['email'])) {
		$email_error = "Please enter your email";
	}

	if (empty($_POST['pass'])) {
		$pass_error = "Please enter your password";
	}

	if (empty($_POST['bdate'])) {
		$bdate_error = "Please enter your birth date";
	}
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<link rel="stylesheet" href="./style.css">
	<title>Document</title>
</head>

<body>
	<div class="main">
		<div class="box right1">
			<h1 class="register">Register to <span> FriendHub</span></h1>
			<form action="" method="POST" enctype="multipart/form-data">
				<div class="inpbox">
					<label for="profilePic">Profile Picture</label>
					<div class="row">
						<div class="small-12 medium-2 large-2 columns">
							<div class="circle">
								<img class="profile-pic" src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg">

							</div>
							<div class="p-image">
								<i class="fa fa-camera upload-button"></i>
								<input class="file-upload" name="profilePic" type="file" accept="image/*" />
							</div>
						</div>
					</div>
				</div>
				<div class="inpbox">
					<label for="name">Name</label> <br>
					<input type="text" placeholder="Enter Your Name" name="name" value=<?= isset($name) ? $sanitized_name : "" ?>>
					<?php if (isset($name_error)) echo "<p class='inpError'>$name_error</p>" ?>
				</div>
				<div class="inpbox">
					<label for="surname">Surname</label> <br>
					<input type="text" placeholder="Enter Your Surname" name="surname" value=<?= isset($surname) ? $sanitized_surname : "" ?>>
					<?php if (isset($surname_error)) echo "<p class='inpError'> $surname_error</p>" ?>
				</div>
				<div class="inpbox">
					<label for="email">Email</label> <br>
					<input type="email" placeholder="Enter Your Email" name="email" value=<?= isset($email) ? $sanitized_email : "" ?>>
					<?php if (isset($email_error)) echo "<p class='inpError'> $email_error</p>" ?>
				</div>
				<div class="inpbox">
					<label for="pass">Password</label> <br>
					<input type="password" placeholder="Enter Your Password" name="pass" value=<?= isset($pass) ? $sanitized_pass : "" ?>>
					<?php if (isset($pass_error)) echo "<p class='inpError'> $pass_error</p>" ?>
					<span style="font-size: 12px; color:red;"><?php if (isset($error_pass_match)) echo $error_pass_match; ?></span>
				</div>
				<div class="inpbox">
					<label for="bdate">Birth Date</label> <br>
					<input type="date" placeholder="Enter Your Birth Date" name="bdate">
					<?php if (isset($bdate_error)) echo "<p class='inpError'> $bdate_error</p>" ?>
				</div>

				<button type="submit" class="btn btn-success" name="registerBtn">Register</button>
			</form>
			<?php if (isset($authError)) {
				echo "<p class='error'>This email is already used</p>";
			} ?>
			<script>
				$(document).ready(function() {


					var readURL = function(input) {
						if (input.files && input.files[0]) {
							var reader = new FileReader();

							reader.onload = function(e) {
								$('.profile-pic').attr('src', e.target.result);
							}

							reader.readAsDataURL(input.files[0]);
						}
					}


					$(".file-upload").on('change', function() {
						readURL(this);
					});

					$(".upload-button").on('click', function() {
						$(".file-upload").click();
					});
				});
			</script>
		</div>

	</div>
</body>

</html>