<!DOCTYPE html>
<html lang ="en">
    <head>
        <meta charset="utf-8">
        <link rel = "icon" type="images/icon" href="/media/favicon.ico">
        <title>Club: Dashboard</title>
    </head>

    <!-- this will serve as dashboard, 
     main page users after logging on, it will
      display announcements, events, attendance.. for now!
      always subject to change :) -->
      
<body class = 'dashboard'>

    <?php include '../header.html' ?>

 <header> 
    <!-- Mini nav board, scroll lower per each a href -->
    <h1>Student Organization Portal</h1>
    <nav>
    <a href="announcements.html">Announcements</a>
    <a href="profile.html">Profile</a>
    </nav>
</header>

<main>

    <h2>Welcome, $User</h2>
    <div id ='profile'>
        <div id = 'pic'><img src ="../media/blue_star.png"></div>
        <div id = 'profile_text'>
        <b>Fname Lname</b><br>
        School, Year<br>
        Eboard Position
        </div>
    </div>
    <section class="card">
        <h3>Upcoming Events</h3>
        <p>General Body Meeting - July 10</p>
    </section>

    <section class="card">
        <h3>Attendance</h3>
        <p>6 / 10 Meetings Attended </p>
    </section>

    <section class="Card">
        <h3>Attendance Points</h3>
        <p> 80 points</p>
    </section>

    <section class="card">
        <h3>Recent Announcement</h3>
        <p> The 7/16 General Body Meeting has been moved to 7/20 </p>
    </section>

</main>
</body>
</html>