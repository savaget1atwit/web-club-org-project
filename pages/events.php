<?php
session_start();
require __DIR__ . '/../config/db.php';

if (!isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = new MongoDB\BSON\ObjectId($_SESSION['user_id']);
$org_id = new MongoDB\BSON\ObjectId($_SESSION['org_id']);

$events = $db->events->find(['org_id' => $org_id]);

?>

$org = $db->organizations->findOne(['_id' => $org_id]);

<!DOCTYPE html>    
    <html lang = "en">

<head>
    <meta charset = "UTF-8">
    <link rel = "icon" type="images/icon" href="/media/favicon.ico">
    <title>club: Events</title>
</head>


<!-- Events Page-->
 <!-- Features include: exploring by tagged events, clicking on events for details and rsvp, and option to join mailing list. -->

<body>

<?php include '../header.html' ?>


    <h1>Events Page!</h1>
    <p>Let's get into it! These are the previous, current, and upcoming events from $organization! 
        Click on any event to see details, rsvp or blah!
    </p>
    <hr>

    <section id='tag1'>
        <h2>Explore by Tag!</h2><br>
        <h3>Tag 1: Career Building</h3><br>

            <div class = 'scroll_container'>
                <?php if(empty($events)): ?>


                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
        </div>
    </section>

    <section id= 'tag2'>
        <h3>Tag 2: How To's</h3><br>
            <div class = 'scroll_container'>
                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
                <div class = 'card'>
                    <h2>Event 1</h2>
                    <p> More text about the event</p>
                </div>
        </div>  
        <br><hr>
    </section>

    <section id = 'extra_tags'>
        <h3>Other tags:</h3>

        <div class = 'tag'>
            <p>tag 1</p>
        </div>
        <div class = 'tag'>
            <p>tag 1</p>
        </div>
        <div class = 'tag'>
            <p>tag 1</p>
        </div>
        <br> <hr>
    </section>


    <!-- Grid presentation with poster and bottom bar with general details, clickable. -->
    <h2>Explore All</h2>

    <div class = 'wrapper'>
        <?php if (empty($events)): ?>
            <p>No events listed yet</p>
                
        <?php else: ?>
            <?php foreach ($events as $event): ?>
        <div class = 'box'>
            <img src ="../media/blue_star.png">
            <h2><?= htmlspecialchars($event->event_name) ?></h2>
            <p><?= htmlspecialchars($event->event_details ?: 'TBD') ?></p>
            <p><?= htmlspecialchars($event->date_scheduled ?: 'TBD') ?></p>
            <p><?= htmlspecialchars($event->location ?: 'TBD') ?></p>

            <p>detail detail detail</p>
        </div>
    <?php endforeach; ?>

<?php endif; ?>
    </div>

</body>
</html>