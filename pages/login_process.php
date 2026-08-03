<?php

session_start();

include "../db.php";


$organization = $_POST['organization'];
$wid = $_POST['wid'];
$password = $_POST['password'];



$sql = "SELECT * FROM users 
        WHERE organization = ?
        AND wid = ?";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ss",
    $organization,
    $wid
);


$stmt->execute();


$result = $stmt->get_result();



if($result->num_rows == 1){

    $user = $result->fetch_assoc();


    if($password == $user['password']){


        $_SESSION['user'] = $user;


        header("Location: dashboard.php");
        exit();


    }

}


echo "Invalid login information";


?>