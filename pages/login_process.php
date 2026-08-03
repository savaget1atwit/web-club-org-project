<?php

session_start();

include "../db.php";

echo "<h2>Debugging Login</h2>";

echo "<pre>";
print_r($_POST);
echo "</pre>";


$organization = $_POST['organization'] ?? '';
$wid = $_POST['wid'] ?? '';
$password = $_POST['password'] ?? '';


echo "Organization entered: " . $organization . "<br>";
echo "WID entered: " . $wid . "<br>";
echo "Password entered: " . $password . "<br><br>";


$sql = "SELECT * FROM users WHERE organization = ? AND wid = ?";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ss",
    $organization,
    $wid
);

$stmt->execute();


$result = $stmt->get_result();


echo "Number of users found: " . $result->num_rows . "<br><br>";


if ($result->num_rows == 1) {

    $user = $result->fetch_assoc();

    echo "User found:<br>";

    echo "<pre>";
    print_r($user);
    echo "</pre>";


    if ($password == $user['password']) {

        echo "PASSWORD MATCHED";

    } else {

        echo "PASSWORD DOES NOT MATCH";

    }

} else {

    echo "NO USER FOUND";

}

?>
