<?php
session_start();
require __DIR__ . '/../config/db.php';

if (!isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}

$org_id = new MongoDB\BSON\ObjectId($_SESSION['org_id']);

$org = $db->organizations->findOne(['_id' => $org_id]);

// Pull every user in this org flagged as eboard, sorted so display order is consistent
$eboard_cursor = $db->users->find(
    [
        'org_id' => $org_id, 
        'role' => 'eboard'
        ],
    [
        'sort' => ['eboard_position' => 1]
        ]
);
$eboard_members = iterator_to_array($eboard_cursor);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Club: About Us</title>
    <link href="../style.css" rel="stylesheet">
</head>

<body>

<?php include '../header.html'; ?>

<!-- General information about organization, including eboard members, meeting times for general body
mission statement,  -->
<section id="about_us">
    <h1>About <em><?= htmlspecialchars($org->org_name ?? 'Our Org') ?></em></h1>

    <p id="mission_statement">
        <?= htmlspecialchars($org->mission_statement ?? 'Mission statement coming soon.') ?>
    </p>

    <div id="eboard_bios">
        <hr>
        <h2>Meet Our E-board!</h2>

        <div class="eboard_grid">
            <?php if (empty($eboard_members)): ?>
                <p>No e-board members listed yet.</p>
            <?php else: ?>
                <?php foreach ($eboard_members as $member): ?>
                    <div class="eboard_card">
                        <img src="<?= htmlspecialchars($member->profile_pic ?? '../media/blue_star.png') ?>"
                             alt="<?= htmlspecialchars($member->eboard_position ?? 'E-board Member') ?>"
                             class="eboard_profile_pic">
                        <p class="eboard_role">
                            <?= htmlspecialchars($member->eboard_position ?? 'Member') ?>:
                            <?= htmlspecialchars($member->fname . ' ' . $member->lname) ?>
                        </p>
                        <blockquote class="eboard_quote">
                            <?= htmlspecialchars($member->bio ?? '') ?>
                        </blockquote>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
</html>