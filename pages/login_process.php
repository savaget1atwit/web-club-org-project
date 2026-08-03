<?php

session_start();

include "../db.php";

echo "PHP FILE REACHED<br><br>";

echo "<pre>";
print_r($_POST);
echo "</pre>";


$organization = $_POST['organization'] ?? '';
$wid = $_POST['wid'] ?? '';
$password = $_POST['password'] ?? '';


echo "Organization: " . $organization . "<br>";
echo "WID: " . $wid . "<br>";
echo "Password: " . $password . "<br><br>";


$sql = "SELECT * FROM users WHERE organization=? AND wid=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ss",
    $organization,
    $wid
);

$stmt->execute();

$result = $stmt->get_result();


echo "Users found: " . $result->num_rows . "<br><br>";


if ($result->num_rows == 1) {

    $user = $result->fetch_assoc();

    echo "Database user:<br>";

    echo "<pre>";
    print_r($user);
    echo "</pre>";


    if ($password == $user['password']) {

        echo "PASSWORD MATCH";

    } else {

        echo "PASSWORD DOES NOT MATCH";

    }

} else {

    echo "NO USER FOUND";

}

?>
