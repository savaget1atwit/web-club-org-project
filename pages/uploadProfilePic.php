<?php

session_start();

require __DIR__ . '/../config/db.php';

if(isset($_FILES['profile_pic'])){

    $file = $_FILES['profile_pic'];

    $filename = basename($file['name']);

    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    $newName = uniqid() . "." . $extension;

    $destination = "../media/profile_pics/" . $newName;


    if(move_uploaded_file($file['tmp_name'], $destination)){


        $user_id = new MongoDB\BSON\ObjectId($_SESSION['user_id']);


        $db->users->updateOne(
            [
                "_id" => $user_id
            ],
            [
                '$set' => [
                    "profile_pic" => $destination
                ]
            ]
        );

    }

}

header("Location: edit_profile.php");
exit();

?>