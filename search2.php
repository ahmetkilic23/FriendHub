<?php





$sql = "SELECT user_id,user_name from user where email = '$email'";
$rs= $db->query($sql);
$to_id=$rs->fetch(PDO::FETCH_ASSOC);


try{
$sql2 = "INSERT INTO notification (to_user_id,from_user_id,message) values (?,?,?)";
$stmt=$db->prepare($sql2);
$stmt->execute([$to_id["user_id"],$user,$request]);
 echo "<p> Request send to {$to_id["user_name"]} !</p>";
echo "<p> You are redirected to home page</p>";


}catch(PDOException $e ){


}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="refresh" content="100 url=http://localhost/256-project/256-Project/homePage.php">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>

<body>

</body>

</html>
