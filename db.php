<?php

$dsn = "mysql:host=localhost;dbname=friendhub;charset=utf8mb4;port=3306";
$user = "root";
$pass = "";

try {
	$db = new PDO($dsn, $user, $pass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
	echo $ex->getMessage();
	echo "<p>Error occured try later.</p>";
	exit;
}

function checkUser($email, $pass)
{
	global $db;
	$user = getUser($email);
	if ($user) {
		return password_verify($pass, $user["password"]);
	}
	return false;
}

function validSession()
{
	return isset($_SESSION["user"]);
}

function getUser($email)
{
	global $db;
	$stmt = $db->prepare("SELECT * FROM user WHERE email=?");
	$stmt->execute([$email]);
	return $stmt->fetch();
}

function getFriendPost(){
	global $db;
	$stmt = $db->prepare("SELECT followed_id FROM friends JOIN posts ON posts.user_id = friends.follower_id WHERE posts.user_id = friends.follower_id");
	$stmt->execute();
	var_dump( $stmt->fetchAll());
}
function getPost($id)
{

	global $db;
	$stmt = $db->prepare("SELECT * FROM posts 
	JOIN user ON posts.user_id = user.user_id 
	JOIN friends ON posts.user_id = friends.followed_id WHERE posts.user_id IN (SELECT followed_id FROM friends JOIN posts ON posts.user_id = friends.follower_id WHERE friends.follower_id = ?) AND friends.follower_id = ? order by posts.id DESC ");
	$stmt->execute([$id,$id]);
	return $stmt->fetchAll();
}	
function getYourPost($id)
{
	global $db;
	$stmt = $db->prepare("SELECT * FROM posts JOIN user ON posts.user_id = user.user_id WHERE posts.user_id=? AND user.user_id = posts.user_id ORDER BY id DESC");
	$stmt->execute([$id]);
	return $stmt->fetchAll();
}

function getFriends($id)
{
	global $db;
	$stmt = $db->prepare("SELECT user_name,surname,profilepic,friend_id FROM user join friends on user.user_id=friends.follower_id WHERE friends.followed_id=? ");
	$stmt->execute([$id]);
	return $stmt->fetchAll();
}
//function getFriendImg($id) {
//	global $db ;
//	$stmt = $db->prepare("SELECT profilepic, user_name,surname FROM user JOIN posts ON user.user_id = posts.user_id WHERE user.user_id!=? AND user.user_id = posts.user_id") ;
//	$stmt->execute([$id]);
//	return $stmt->fetchAll() ;
//}

function getNotifications($id)
{
	global $db;
	$stmt = $db->prepare("SELECT * FROM notification JOIN user ON from_user_id = user.user_id WHERE to_user_id =? ");
	$stmt->execute([$id]);
	return $stmt->fetchAll();
	// AND from_user_id = user_id
	
}


function deletePost($post_id)
{
	global $db;
	$stmt = $db->prepare("DELETE FROM posts WHERE id=?");
	$stmt->execute([$post_id]);
}
function deleteFriend($fr_id)
{
	global $db;
	$stmt = $db->prepare("DELETE FROM friends WHERE friend_id=?");
	$stmt->execute([$fr_id]);
}



function getComment($post_id)
{
	global $db;
	$stmt = $db->prepare("SELECT * FROM comment join posts on comment.post_id=posts.id join user on comment.user_id=user.user_id WHERE comment.post_id=?");
	$stmt->execute([$post_id]);
	return $stmt->fetchAll();
}
