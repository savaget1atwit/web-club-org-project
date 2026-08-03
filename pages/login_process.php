<?php

session_start();

include "../db.php";


echo "<pre>";
print_r($_POST);
echo "</pre>";


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


echo "Rows found: " . $result->num_rows . "<br>";


if($result->num_rows == 1){

    $user = $result->fetch_assoc();

    echo "User found:<br>";
    print_r($user);

    echo "<br>Entered password: " . $password;
    echo "<br>Database password: " . $user['password'];


    if($password == $user['password']){

        echo "<br>Password matches!";

    } else {

        echo "<br>Password does not match";

    }

} else {

    echo "No user found";

}

?>
