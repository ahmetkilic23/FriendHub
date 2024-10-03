<?php


require "db.php";

var_dump($_POST);
extract($_POST);
$sql="SELECT user_id FROM posts WHERE id=?";
$stmt= $db->prepare($sql);
$stmt->execute([$post_id]);

$rs = $stmt->fetch();
$id= intval($rs["user_id"]);

var_dump($id);


$sql2="INSERT INTO `likes` (user_id,post_id) VALUES (?,?)  ";
$stmt2= $db->prepare($sql2);
$stmt2->execute([$id,$post_id]);


//butonun textini değiştirmek için kod
// Yeni metni belirleyin
$newText = "LIKED";

// Sonuçları JSON formatında döndürün
$response = [
  'newText' => $newText,
  'sqlResult' => $sqlResult
];

header('Content-Type: application/json');
echo json_encode($response);

?>