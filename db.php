<?php

$host = "localhost";
$username = "root";
$password = "Ilovelearning234!";
$database = "student_org_portal";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional success message for testing
echo "Database connected successfully!";

?>