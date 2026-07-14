<!DOCTYPE html>    
    <html lang = "en">

<head>
    <meta charset = "UTF-8">
    <link rel = "icon" type="images/icon" href="/media/favicon.ico">
    <title>Your Club Goes Here</title>
</head>


<body>

<?php include '../header.html' ?>

<section id = 'edit_profile'>
    <h2>Edit Profile</h2>
    <div id ='profile'>
        <div id = 'pic'><img src ="../media/blue_star.png"></div>
        <div id = 'profile_text'>
        <b>Fname Lname</b><br>
        School, Year<br>
        Eboard Position
        </div>
        <div><button id = 'edit_picture' type = 'button'>Edit Picture</button></div>
    </div>

    <hr>
    
    <div id = 'bio_section'>
        <label for = 'biography'><b>Bio</b></label><br><br>
        <div id = 'bio_wrapper'>
            <textarea id = 'bio' maxlength = '150' placeholder = 'Tell us about yourself...'></textarea>
            <span id = 'bio_counter'> 0/150 </span>
        </div>
    </div>

    <br>
    <button type = 'button' id = 'save_profile'>Save Profile</button>
</section>

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
</body> 
</html>