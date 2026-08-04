<?php
session_start();
require __DIR__ . '/../config/db.php';

if (!isset($_SESSION['org_id']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$org_id = new MongoDB\BSON\ObjectId($_SESSION['org_id']);
$user_id = new MongoDB\BSON\ObjectId($_SESSION['user_id']);

$org = $db->organizations->findOne(['_id' => $org_id]);

$user = $db->users->findOne(['_id' => $user_id]);
$user_roles = $user ? $user->role->getArrayCopy() : [];
$is_eboard = !empty(array_intersect($user_roles, ['admin', 'eboard']));

// Only eboard/admin may edit their profile from this page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$is_eboard) {
        http_response_code(403);
        die('You do not have permission to edit a profile here.');
    }

    $updates = [
        'fname' => trim($_POST['fname'] ?? ''),
        'lname' => trim($_POST['lname'] ?? ''),
        'bio' => trim($_POST['bio'] ?? '')
    ];

    $db->users->updateOne(
        ['_id' => $user_id],
        ['$set' => $updates]
    );

    header('Location: aboutUs.php');
    exit;
}

// Pull every user in this org flagged as eboard, sorted so display order is consistent
$eboard_cursor = $db->users->find(
    ['org_id' => $org_id, 'role' => 'eboard'],
    ['sort' => ['eboard_position' => 1]]
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

<?php include '../header.php'; ?>

<!-- General information about organization, including eboard members, meeting times for general body
mission statement,  -->
<section id="about_us">
    <h1>About <em><?= htmlspecialchars($org->org_name ?? 'Our Org') ?></em></h1>

    <p id="mission_statement"><?= htmlspecialchars($org->mission_statement ?? 'Mission statement coming soon.') ?></p>

    <?php if ($is_eboard): ?>
    <hr>
    <div id="userProfile">
        <div id="profile">
            <div id="pic"><img id="profile_preview" src="<?= htmlspecialchars($user->profile_pic ?? '../media/blue_star.png') ?>"></div>
            <div id="profile_text">
                <b><?= htmlspecialchars($user->fname . ' ' . $user->lname) ?></b><br>
                <?= htmlspecialchars($user->school . ', ' . $user->year) ?><br>
                <?= htmlspecialchars($user->eboard_position ?? '') ?><br>
                <em><?= htmlspecialchars($user->bio ?? '') ?></em><br>
            </div>
        </div>
        <button type="button" id="editProfile">Edit Profile</button>
    </div>

    <div id="editModal" class="modal">
        <div class="modal_content">
            <span class="modal_close">&times;</span>
            <h2>Edit Profile</h2>

            <form method="POST" enctype="multipart/form-data">

                <label>First Name</label>
                <input type="text" name="fname" value="<?= htmlspecialchars($user->fname ?? '') ?>">

                <label>Last Name</label>
                <input type="text" name="lname" value="<?= htmlspecialchars($user->lname ?? '') ?>">

                <div id="bio_wrapper">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" maxlength="150" placeholder="Tell us about yourself..."><?= htmlspecialchars($user->bio ?? '') ?></textarea>
                    <span class="textarea_counter">0/150</span>
                </div>

                <label>Profile Picture</label>
                <input type="file" name="profile_pic">

                <button type="submit">Save Profile</button>

            </form>

        </div>
    </div>
    <?php endif; ?>

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
                            <?= htmlspecialchars($member->bio ?? 'No bio present.') ?>
                        </blockquote>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($is_eboard): ?>
<script>
    const editButton = document.getElementById("editProfile");
    const profileModal = document.getElementById("editModal");

    editButton.onclick = function () {
        profileModal.style.display = "block";
    };

    document.querySelectorAll('.modal_close').forEach(btn => {
        btn.addEventListener('click', () => {
            profileModal.style.display = "none";
        });
    });

    // close if clicking outside modal
    window.onclick = function (event) {
        if (event.target === profileModal) {
            profileModal.style.display = "none";
        }
    };

    // profile bio text counter
    document.addEventListener('DOMContentLoaded', () => {
        const bio = document.getElementById('bio');
        const counter = document.querySelector('.textarea_counter');
        const maxLength = 150;

        if (bio && counter) {
            bio.addEventListener('input', () => {
                counter.textContent = `${bio.value.length} / ${maxLength}`;
            });
        }
    });
</script>
<?php endif; ?>

</body>
</html>