<?php

session_start() ;
	require "db.php" ;
	// check if the user authenticated before
	if( !validSession()) {
		header("Location: login.php?error") ; // redirect to login page
		exit ; 
	}


    $userData = $_SESSION["user"] ;
	$userId=$userData["user_id"];

echo "<h1NOTIFICATIONS</h1>";

$sql = "SELECT * from `notification` where to_user_id = $userId";

$rs= $db->query($sql);
if($rs->rowCount() == 0){

    echo "<p>No notifications here :)</p>";
} else{



$rows= $rs->fetchAll(PDO::FETCH_ASSOC) ;


foreach($rows as $row){
    
    $id=$row["from_user_id"];
    $sql2 = "SELECT * from `user` where user_id=$id";
    $rs2=$db->query($sql2);
    
    $rows2=$rs2->fetchAll(PDO::FETCH_ASSOC);

    echo "<table>";
    foreach($rows2 as $row2){
        echo "
        <tr>
            <td>$row2[user_name] $row2[surname]</td>
            <td>$row[message]</td>
            <td> <input type='button' value='Accept request'> <input type='button' value='Decline request'></td>
        </tr>
    ";
    }
    echo "</table>";
}

    

    }




?>






