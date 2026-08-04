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

$attendance_percent = ($meetings_total > 0) ? round(($meetings_attended / $meetings_total) * 100) : 0;

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
    ['sort' => ['posted_at' => -1]]);

$profilePic = !empty($user->profile_pic)
    ? $user->profile_pic
    : "../media/default-profile.png";
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

<?php include '../header.php'; ?>

<header>
    <!-- Mini nav board, scroll lower per each a href -->
    <h2>Welcome, <?= htmlspecialchars($user->fname) ?></h2>
    <nav>
        <a href="announcements.php">Announcements</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Log Out</a>
    </nav>
    <br>
</header>

<main>

    <div id="profile">
        <div id="pic">
            <?php
                $profilePic = !empty($user->profile_pic)? $user->profile_pic: '../media/default-profile.png';
            ?><img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture">
        </div>
        <div id="profile_text">
            <b><?= htmlspecialchars($user->fname . ' ' . $user->lname) ?></b><br>
            <?= htmlspecialchars($user->school . ', ' . $user->year) ?><br>
            <?= htmlspecialchars($user->eboard_position ?? 'General Body') ?>
        </div>
    </div>

<section class = "dashboard_wrapper">
    <div class="box">
        <h3>Upcoming Events</h3>
        <?php if ($upcoming_event): ?>
            <strong><?= htmlspecialchars($upcoming_event->title) ?></strong>
            <p><?= $upcoming_event->start->toDateTime()->format('F j, Y') ?></p>
        <?php else: ?>
            <p>No upcoming events scheduled.</p>
        <?php endif; ?>
        </div>

    <div class="box">
        <h3>Attendance</h3>

        <p><?= $meetings_attended ?> / <?= $meetings_total ?> Meetings Attended</p>
        <p><?= $attendance_percent ?>%</p>
        </div>

    <div class="box">
        <h3>Attendance Points</h3>
        <p><?= $user->attendance_points ?? 0 ?> points</p>
    </div>

    <div class="box announcement_card">
        <h3>Latest Announcement</h3>
        <?php if ($announcement): ?>
            <strong><?= htmlspecialchars($announcement->title) ?></strong>
            <p><?= nl2br(htmlspecialchars($announcement->body)) ?></p>
            <small>Posted:
                <?= $announcement->posted_at->toDateTime()->format('F j, Y') ?>
            </small>

        <?php else: ?>
            <p>No announcements yet.</p>
        <?php endif; ?>

    </div>
</section>
</main>
</body>
</html>
