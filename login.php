<?php
session_start();
require "db.php";

// auto login
if (validSession()) {
	header("Location: homePage.php");
	exit;
}

if (isset($_POST["loginBtn"])) {

	if (empty($_POST['email'])) {
		$email_error = "Please enter your email";
	}


	if (empty($_POST['pass'])) {
		$pass_error = "Please enter your password";
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
	<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope"
		viewBox="0 0 16 16">

</head>

<body>
	<?php
	// Authentication
	if (!empty($_POST)) {
		extract($_POST);
		if (checkUser($email, $pass)) {
			// the user is authenticated
			// Store data to use in other php files. 
			$_SESSION["user"] = getUser($email);
			header("Location: homePage.php"); // redirect to main page
			exit;
		}
		if (!empty($_POST['email']) && !empty($_POST['pass'])) {
			$authError = true;
		}
	}
	?>
	<div class="main">
		<div class="box left">
			<img src="images/login.png" alt="image"><br></br>
			<div class="explaination">
				<h1 class="welcome">Welcome to <span class="home-span">FriendHub</span></h1>
				<p>We are the best and biggest social network with 3 billion active users all around the world. Share
					your thoughts, posts and much more!</p>
				<a href="./register.php"><button type="button" class="btn btn-outline-success">Register
						Now!</button></a>
				<a href="about.html" class="btn btn-outline-success">About FriendHub</a>
			</div>

		</div>
		<div class="box right">
			<h1>Login to Your Account</h1>
			<form action="login.php" method="POST">
				<div class="inpbox">
					<i class="bi bi-mailbox2" style="font-size: 1.50rem; margin-right: 10px"></i>
					<label for="email">Email</label> <br>
					<input type="email" placeholder="Enter Your Email" name="email"
						value=<?= isset($email) ? $email : "" ?>>
					<?= (isset($email_error) ?  "<p class='inpError'> $email_error </p>" : "") ?>

				</div>

				<div class="inpbox">
					<i class="bi bi-key" style="font-size: 1.50rem; margin-right: 10px"></i>
					<label for="password">Password</label> <br>
					<input type="password" placeholder="********" name="pass" value=<?= isset($pass) ? $pass : "" ?>>
					<?php
					// Authentication Error Message
					if (isset($authError)) {
						echo "<p class='error'>Wrong email or password</p>";
					}

					// Direct access to main page error message
					if (isset($_GET["error"])) {
						echo "<p class='error'>You tried to access home page directly</p>";
					}

					?>
					<?php if (isset($pass_error)) echo "<p class='inpError'> $pass_error</p>" ?>
				</div>
				<button type="submit" class="btn btn-success" name="loginBtn">Login</button>
				<p>Don't you have an account? <a href="register.php"><span>Register Now!</span></a></p>
			</form>
		</div>
	</div>

</body>

</html>
