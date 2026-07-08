<!DOCTYPE html>
<html lang = "en">

<head>
    <meta charset = "UTF-8">
    <title>Your Club Goes Here</title>
</head>

<body>

<?php include '../header.html' ?>

    <!-- General information about organization, including eboard members, meeting times for general body
    mission statement,  -->
    <section id = "about_us">
        <h1> About <em> Our Org</em></h1>
        <br>
        <div id = "eboard_bios">
            <hr>
            <h2> Meet Our E-board!</h2>
            <img src ="../media/blue_star.png" alt = "President" class = "eboard_profile_pic">
            <p class ="eboard_role">$role: $firstName $lastName</p>
            <blockquote class = "eboard_quote">$bio</blockquote>

            <img src ="../media/blue_star.png" alt = "Vice President" class = "eboard_profile_pic">
            <p class ="eboard_role">$role: $firstName $lastName</p>
            <blockquote class = "eboard_quote">$bio</blockquote>

            <img src ="../media/blue_star.png" alt = "Treasurer" class = "eboard_profile_pic">
            <p class ="eboard_role">$role: $firstName $lastName</p>
            <blockquote class = "eboard_quote">$bio</blockquote>

            <img src ="../media/blue_star.png" alt = "Communications" class = "eboard_profile_pic">
            <p class ="eboard_role">$role: $firstName $lastName</p>
            <blockquote class = "eboard_quote">$bio</blockquote>
        </div>
    </section>



</body>
</html>