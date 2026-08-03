<?php
session_start();
require __DIR__ . '/../config/db.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $org_code = trim($_POST['org'] ?? '');
    $wID = trim($_POST['wID'] ?? '');
    $password = $_POST['password'] ?? '';

    $org = $db->organizations->findOne(['org_code' => $org_code]);

    if (!$org) {
        $error = "Invalid organization";
    } else {
        $user = $db->users->findOne([
            'org_id' => $org->_id,
            'wID' => $wID
        ]);

        if ($user && password_verify($password, $user->password_hash)) {
            $_SESSION['user_id'] = (string) $user->_id;
            $_SESSION['org_id'] = (string) $org->_id;
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid ID or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../style.css" rel="stylesheet">
    <script src="../main.js" defer></script>
</head>

<!--Login portal will serve as authentication and managing perms and who can view what!
password hashing using bcrypt algorithm-->
<body>

<header>
    <h1>Student Organization Management Portal</h1>
</header>

<main>
    <section id="club_container">
        <form id="org_login" method = "post">
            <h1>Please log in to access your org's information...</h1>
            
            <!-- autocomplete possibly after certain amount of characters -->
            <label for = "org">Affiliated Organization:</label><br>
            <input type = text id = "org" required><br><br>

            <!-- general body able type in org and press 'just view as general body' -->
            <label for ="wID">WIT ID:</label><br>
            <input type = "text" id = "wID" placeholder = "W000123456" pattern = "W\d{8}" required><br><br>

            <label for ="password">Password:</label><br>
            <input type = "password" id = "password" minLength = "4" required><br><br>

            <button type ="button" onclick="auth()">Submit</button>
         </form>
    </section>
</main>

</body>
</html>
