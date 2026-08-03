<?php

    session_start();

    require __DIR__ . '/../config/db.php';


    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $user_id = new MongoDB\BSON\ObjectId($_SESSION['user_id']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $updates = [
        "fname" => $_POST['fname'],
        "lname" => $_POST['lname'],
        "bio" => $_POST['bio']
    ];


    $db->users->updateOne(
        [
            "_id" => $user_id
        ],
        [
            '$set' => $updates
        ]
    );


    header("Location: userPref.php");
    exit;
}

    $user = $db->users->findOne([
        '_id' => $user_id
    ]);
?>


<!DOCTYPE html>    
    <html lang = 'en'>

<head>
    <meta charset = 'UTF-8'>
    <link rel = 'icon' type='images/icon' href='/media/favicon.ico'>
    <title>Your Club Goes Here</title>
</head>


<body>

<?php include '../header.html' ?>

<section id = 'edit_profile'>
    <h2>Edit Profile</h2>
        <div id ='profile'>
            <div id = 'pic'><img id='profile_preview' src='<?= htmlspecialchars($user->profile_pic ?? '../media/blue_star.png') ?>' width='150'></div>
            <div id = 'profile_text'>
                <b><?= htmlspecialchars($user->fname . ' ' . $user->lname) ?> </b><br>
                <?= htmlspecialchars($user->school . ', ' . $user->year) ?><br>
                <?= htmlspecialchars($user->eboard_position) ?>    
            </div>
            

        </div>

        <hr>
        
        <div id = 'bio_section'>
            <label for = 'biography'><h2>Bio</h2></label>
            <div id = 'bio_wrapper'>
                <?= htmlspecialchars($user->bio) ?><br>

            </div>
        </div>

        <br>
        <button type = 'button' id = 'editProfile'>Edit Profile</button>
</section>

<div id="editModal" class="modal">

    <div class="modal_content">

        <span id="close_modal">&times;</span>

        <h2>Edit Profile</h2>


        <form method="POST" enctype="multipart/form-data">

            <label>First Name</label>
            <input type="text"name="fname" value="<?= htmlspecialchars($user->fname ?? '') ?>">


            <label>Last Name</label>
            <input type="text" name="lname" value="<?= htmlspecialchars($user->lname ?? '') ?>">


            <label>Bio</label>
            <textarea id = 'bio' name = 'bio' maxlength = '150' placeholder = 'Tell us about yourself...'><?= htmlspecialchars($user->bio ?? '') ?></textarea>
            <span id = 'bio_counter'> 0/150 </span>

            <label>Profile Picture</label>
            <input type="file"name="profile_pic">


            <button type="submit">Save Profile</button>

        </form>

    </div>

</div>
<section id = 'account_info'>
    <h2>Account Information</h2>
    
</section>

<section id = 'notification'>
    <nav id = 'notif_bar'>
        <ul>
            <p>Push notifications</p>
            <p>Email notifications</p>
        </ul> 
   </nav>
</section>

    <script>

    const editButton = document.getElementById("editProfile");

    const profileModal = document.getElementById("editModal");

    const closeButton = document.getElementById("close_modal");



    editButton.onclick = function(){
        profileModal.style.display = "block";
    }
    
    closeButton.onclick = function(){
        profileModal.style.display = "none";
    }

    // close if clicking outside modal
    window.onclick = function(event){

        if(event.target == modal){
            profileModal.style.display = "none";
        }

    }

    //user pref bio text counter
    document.addEventListener('DOMContentLoaded', () =>{
        const bio = document.getElementById('bio');
        const counter = document.getElementById('bio_counter');
        const maxLength = 150;

        bio.addEventListener('input', () => {
            counter.textContent = `${bio.value.length} / ${maxLength}`;
        });
    });
    </script>

</body> 
</html>