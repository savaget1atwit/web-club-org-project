<?php
session_start();
require __DIR__ . '/../config/db.php';

// Kick out anyone who isn't logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = new MongoDB\BSON\ObjectId($_SESSION['user_id']);
$org_id = new MongoDB\BSON\ObjectId($_SESSION['org_id']);

// Pull the logged-in user's profile, scoped to their org for safety
$user = $db->users->findOne([
    '_id' => $user_id,
    'org_id' => $org_id
]);

// If session data doesn't match a real user, force re-login instead of showing broken data
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Count attendance records for this user within their org
$meetings_attended = $db->attendance_records->countDocuments([
    'org_id' => $org_id,
    'user_id' => $user_id,
    'attended' => true
]);

$meetings_total = $db->attendance_records->countDocuments([
    'org_id' => $org_id,
    'user_id' => $user_id
]);

// Grab the org's next upcoming event
$upcoming_event = $db->events->findOne(
    [
        'org_id' => $org_id,
        'start' => ['$gte' => new MongoDB\BSON\UTCDateTime()]
    ],
    ['sort' => ['start' => 1]]
);

// Grab the most recent announcement for this org
$announcement = $db->announcements->findOne(
    ['org_id' => $org_id],
    ['sort' => ['posted_at' => -1]]
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="images/icon" href="/media/favicon.ico">
    <title>Club: Dashboard</title>
    <link href="../style.css" rel="stylesheet">
</head>

<!-- this will serve as dashboard,
 main page users after logging on, it will
  display announcements, events, attendance.. for now!
  always subject to change :) -->

<body class="dashboard">

<?php include '../header.html'; ?>

<header>
    <!-- Mini nav board, scroll lower per each a href -->
    <h1>Student Organization Portal</h1>
    <nav>
        <a href="announcements.html">Announcements</a>
        <a href="profile.html">Profile</a>
        <a href="logout.php">Log Out</a>
    </nav>
</header>

<main>

    <h2>Welcome, <?= htmlspecialchars($user->fname) ?></h2>
    <div id="profile">
        <div id="pic"><img src="<?= htmlspecialchars($user->profile_pic) ?>"></div>
        <div id="profile_text">
            <b><?= htmlspecialchars($user->fname . ' ' . $user->lname) ?></b><br>
            <?= htmlspecialchars($user->school . ', ' . $user->year) ?><br>
            <?= htmlspecialchars($user->eboard_position ?? 'General Body') ?>
        </div>
    </div>

    <section class="card">
        <h3>Upcoming Events</h3>
        <?php if ($upcoming_event): ?>
            <p><?= htmlspecialchars($upcoming_event->title) ?> -
                <?= htmlspecialchars($upcoming_event->start->toDateTime()->format('F j')) ?></p>
        <?php else: ?>
            <p>No upcoming events scheduled.</p>
        <?php endif; ?>
    </section>

    <section class="card">
        <h3>Attendance</h3>
        <p><?= $meetings_attended ?> / <?= $meetings_total ?> Meetings Attended</p>
    </section>

    <section class="card">
        <h3>Attendance Points</h3>
        <p><?= $user->attendance_points ?? 0 ?> points</p>
    </section>

    <section class="card">
        <h3>Recent Announcement</h3>
        <?php if ($announcement): ?>
            <p><?= htmlspecialchars($announcement->body) ?></p>
        <?php else: ?>
            <p>No announcements yet.</p>
        <?php endif; ?>
    </section>

</main>
</body>
</html>