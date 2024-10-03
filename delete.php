<?php

require "db.php";


if(isset($_POST['delete_id'])){
	$id = $_POST['delete_id'];
	$deleted_img = $_POST['post_id'];
	deletePost($id);
	unlink("images/".$deleted_img);
	$_SESSION['status']="Post deleted successfully";
	header("Location: profile.php");
}


if(isset($_POST['friend_id'])){
	extract($_POST);
	$stmt= $db->prepare("SELECT  * from friends where friend_id=? ");
	$stmt->execute([$friend_id]);
	$rs=$stmt->fetchAll();
	$stmt2= $db->prepare("INSERT INTO notification (to_user_id, from_user_id, message) VALUES (?,?,?) ");
	
	$follower=$rs[0]["follower_id"] ;
	$followed=$rs[0]["followed_id"];
	
	$stmt2->execute([$follower,$followed,"Removed you from followers"]);
	

	$id = $_POST['friend_id'];
	
	deleteFriend($id);


	header("Location: profile.php");
}



