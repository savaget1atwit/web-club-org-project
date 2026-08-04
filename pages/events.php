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

$users = iterator_to_array($db->users->find(['org_id' => $org_id]));
$current_user = $db->users->findOne(['_id' => $user_id]);
$user_roles = $current_user ? $current_user->role->getArrayCopy() : [];

$is_eboard = !empty(array_intersect($user_roles, ['admin', 'eboard']));

$events = iterator_to_array($db->events->find(['org_id' => $org_id]));

// Tag sort for events
$all_tags = $db->events->distinct('tag', ['org_id' => $org_id]);
sort($all_tags);

$events_by_tag = [];
foreach ($events as $event) {
    if (isset($event->tag)) {
        foreach ($event->tag as $tag) {
            $events_by_tag[$tag][] = $event;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only eboard/admin may create, edit, or delete events, regardless of what the client sends
    if (!$is_eboard) {
        http_response_code(403);
        die('You do not have permission to modify events.');
    }

    $action = $_POST['action'] ?? 'create';

    if ($action === 'delete') {
        $event_id = new MongoDB\BSON\ObjectId($_POST['event_id']);

        // Scope by org_id too, so no one can delete another org's event by guessing an ID
        $db->events->deleteOne([
            '_id' => $event_id,
            'org_id' => $org_id
        ]);

        header('Location: events.php');
        exit;
    }

    $event_name = trim($_POST['eventName'] ?? '');
    $lead_id = trim($_POST['eventLead'] ?? '');
    $date_scheduled = trim($_POST['dateScheduled'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $event_details = trim($_POST['eventDetails'] ?? '');
    $existing_tags = $_POST['existing_tags'] ?? [];
    $new_tags_raw = trim($_POST['new_tags'] ?? '');

    if ($event_name === '' || $lead_id === '') {
        die('Event name and lead are required.');
    }

    // Resolve the selected lead's ObjectId back to a display name
    $event_lead = '';
    foreach ($users as $u) {
        if ((string) $u->_id === $lead_id) {
            $event_lead = $u->fname . ' ' . $u->lname;
            break;
        }
    }

    // Merge checked tags with newly typed ones, normalize casing/whitespace, drop duplicates
    $new_tags = array_filter(array_map('trim', explode(',', strtolower($new_tags_raw))));
    $existing_tags = array_filter(array_map(fn($t) => strtolower(trim($t)), $existing_tags));
    $tags = array_values(array_unique(array_merge($existing_tags, $new_tags)));

    if ($action === 'edit') {
        $event_id = new MongoDB\BSON\ObjectId($_POST['event_id']);

        $db->events->updateOne(
            ['_id' => $event_id, 'org_id' => $org_id],
            ['$set' => [
                'event_name' => $event_name,
                'event_lead' => $event_lead,
                'date_scheduled' => $date_scheduled,
                'location' => $location,
                'event_details' => $event_details,
                'tag' => $tags
            ]]
        );
    } else {
        $db->events->insertOne([
            'org_id' => $org_id,
            'event_name' => $event_name,
            'event_lead' => $event_lead,
            'date_created' => date('n/j/Y'),
            'date_scheduled' => $date_scheduled,
            'location' => $location,
            'event_details' => $event_details,
            'tag' => $tags,
            'created_by' => $user_id
        ]);
    }

    header('Location: events.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="images/icon" href="/media/favicon.ico">
    <title>Club: Events</title>
    <link href="../style.css" rel="stylesheet">
</head>

<!-- Events Page-->
<!-- Features include: exploring by tagged events, clicking on events for details and rsvp, and option to join mailing list. -->

<body>

    <?php include '../header.php'; ?>

    <h1>Events Page!</h1>
    <p>Let's get into it! These are the previous, current, and upcoming events from
        <?= htmlspecialchars($org->org_name ?? 'your org') ?>!
        Click on any event to see details, rsvp or blah!
    </p>

    <?php if ($is_eboard): ?>
    <button type="button" id="addEvent">+ Add Event</button>

    <div id="addEventModal" class="modal" style="display:none;">
        <div class="modal_content">
            <span class="modal_close">&times;</span>
            <h2>Add Event</h2>

            <form method="POST">

                <label for="eventName">Event Name:</label>
                <input type="text" name="eventName" id="eventName" required>

                <label for="eventLead">Event Lead:</label>
                <select name="eventLead" id="eventLead" required>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= htmlspecialchars($u->_id) ?>"><?= htmlspecialchars($u->fname . ' ' . $u->lname) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="eventDetails">Details:</label>
                <div id="details_wrapper" style="position: relative;">
                    <textarea id="eventDetails" name="eventDetails" maxlength="150" placeholder="Add event information here..."></textarea>
                    <span class="textarea_counter" id="details_counter">0/150</span>
                </div>

                <label for="dateScheduled">Date Scheduled:</label>
                <input type="date" name="dateScheduled" id="dateScheduled" required>

                <label for="location">Location:</label>
                <input type="text" name="location" id="location" required>

                <label>Tags:</label><br>
                <div class="tag_checkboxes">
                    <?php if (empty($all_tags)): ?>
                        <p class="no_tags_msg">No existing tags</p>
                    <?php else: ?>
                        <?php foreach ($all_tags as $existing_tag): ?>
                            <label class="tag_checkbox_label">
                                <input type="checkbox" name="existing_tags[]" value="<?= htmlspecialchars($existing_tag) ?>"><?= htmlspecialchars(ucfirst($existing_tag)) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <label for="new_tags">Add new tag(s) (comma separated):</label>
                <input type="text" id="new_tags" name="new_tags" placeholder="fundraising, social">

                <button type="submit" class="createBtn">Create Event</button>

            </form>

        </div>

    </div>
    <?php endif; ?>
    <hr>

    <div id="viewEventModal" class="modal" style="display:none;">
        <div class="modal_content">
            <span class="modal_close">&times;</span>
            <h2 id="view_name"></h2>
            <p><strong>Lead:</strong> <span id="view_lead"></span></p>
            <p><strong>Date:</strong> <span id="view_date"></span></p>
            <p><strong>Location:</strong> <span id="view_location"></span></p>
            <p id="view_details"></p>
            <div id="view_tags" class="tag_checkboxes"></div>

            <?php if ($is_eboard): ?>
            <div class="modal_actions">
                <button type="button" id="openEditFromView">Edit</button>
                <form method="POST" onsubmit="return confirm('Delete this event? This cannot be undone.');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="event_id" id="view_delete_event_id">
                    <button type="submit" class="delete_btn">Delete Event</button>
                </form>
            <?php endif; ?>

                <button type = "button" id = "rsvpEvent">RSVP</button>
            </div>
        </div>
    </div>


    <!-- RSVP function -->
    <div id = "rsvpEventModal" class = "modal" style="display:none;">
        <div class="modal_content">
            <span class="modal_close">&times;</span>
            <h2>RSVP to </h2>
        </div>
    </div>

    <?php if ($is_eboard): ?>
    <div id="editEventModal" class="modal" style="display:none;">
        <div class="modal_content">
            <span class="modal_close">&times;</span>
            <h2>Edit Event</h2>

            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="event_id" id="edit_event_id">

                <label for="edit_eventName">Event Name:</label>
                <input type="text" name="eventName" id="edit_eventName" required>

                <label for="edit_eventLead">Event Lead:</label>
                <select name="eventLead" id="edit_eventLead" required>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= htmlspecialchars($u->_id) ?>"><?= htmlspecialchars($u->fname . ' ' . $u->lname) ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="edit_eventDetails">Details:</label>
                <div style="position: relative;">
                    <textarea id="edit_eventDetails" name="eventDetails" maxlength="150"></textarea>
                    <span class="textarea_counter" id="edit_details_counter">0/150</span>
                </div>

                <label for="edit_dateScheduled">Date Scheduled:</label>
                <input type="date" name="dateScheduled" id="edit_dateScheduled" required><br>

                <label for="edit_location">Location:</label>
                <input type="text" name="location" id="edit_location" required><br>

                <label>Tags:</label>
                <div class="tag_checkboxes" id="edit_tag_checkboxes">
                    <?php foreach ($all_tags as $existing_tag): ?>
                        <label class="tag_checkbox_label">
                            <input type="checkbox" name="existing_tags[]" value="<?= htmlspecialchars($existing_tag) ?>" class="edit_tag_checkbox"><?= htmlspecialchars(ucfirst($existing_tag)) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <label for="edit_new_tags">Add new tag(s) (comma separated):</label><br>
                <input type="text" id="edit_new_tags" name="new_tags">

                <button type="submit">Save Changes</button>
            </form>

            <form method="POST" onsubmit="return confirm('Delete this event? This cannot be undone.');" style="margin-top: 10px;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="event_id" id="delete_event_id">
                <button type="submit" class="delete_btn">Delete Event</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <h2>Explore by Tag!</h2>
    <?php if (empty($all_tags)): ?>
        <p>No tags have been added to events yet.</p>
    <?php else: ?>
        <?php foreach ($all_tags as $tag): ?>
            <section class="tag_section">
                <h3 class="tag_name"><?= htmlspecialchars(ucfirst($tag)) ?></h3>
                <div class="scroll_container">
                    <?php foreach ($events_by_tag[$tag] as $event): ?>
                        <div class="card">
                            <h2><?= htmlspecialchars($event->event_name) ?></h2>
                            <p><?= htmlspecialchars($event->event_details ?: 'Details coming soon...') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Grid presentation with poster and bottom bar with general details, clickable. -->
    <h2>Explore All</h2>

    <div class="wrapper">

        <?php if (empty($events)): ?>
            <p>No events have been created yet.</p>

        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <?php
                    $lead_id = '';
                    foreach ($users as $u) {
                        if ($u->fname . ' ' . $u->lname === $event->event_lead) {
                            $lead_id = (string) $u->_id;
                            break;
                        }
                    }
                ?>

                <div class="box box_clickable"
                    data-id="<?= htmlspecialchars($event->_id) ?>"
                    data-name="<?= htmlspecialchars($event->event_name) ?>"
                    data-details="<?= htmlspecialchars($event->event_details ?? '') ?>"
                    data-date="<?= htmlspecialchars($event->date_scheduled ?? '') ?>"
                    data-location="<?= htmlspecialchars($event->location ?? '') ?>"
                    data-lead-name="<?= htmlspecialchars($event->event_lead) ?>"
                    data-lead-id="<?= htmlspecialchars($lead_id) ?>"
                    data-tags="<?= htmlspecialchars(isset($event->tag) ? implode(',', $event->tag->getArrayCopy()) : '') ?>"
                >
                    <h2><?= htmlspecialchars($event->event_name) ?></h2>
                    <p><?= htmlspecialchars($event->event_details ?: '') ?></p>
                    <p><strong>Lead:</strong>
                    <?= htmlspecialchars($event->event_lead) ?></p>
                    <p>
                        <strong>Date:</strong>
                        <?= htmlspecialchars($event->date_scheduled ?: 'TBD') ?>
                    </p>
                    <p>
                        <strong>Location:</strong>
                        <?= htmlspecialchars($event->location ?: 'TBD') ?>
                    </p>

                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        const addEventBtn = document.getElementById("addEvent");
        const addModal = document.getElementById("addEventModal");

        if (addEventBtn) {
            addEventBtn.onclick = function () {
                addModal.style.display = "block";
            };
        }

        // One shared close handler for every modal - finds the nearest ancestor .modal and hides it
        document.querySelectorAll('.modal_close').forEach(btn => {
            btn.addEventListener('click', () => {
                const parentModal = btn.closest('.modal');
                if (parentModal) {
                    parentModal.style.display = 'none';
                }
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const details = document.getElementById('eventDetails');
            const counter = document.getElementById('details_counter');
            const maxLength = 150;

            if (details && counter) {
                details.addEventListener('input', () => {
                    counter.textContent = `${details.value.length} / ${maxLength}`;
                });
            }

            // View modal - available to everyone, populated from the clicked card's data attributes
            const viewModal = document.getElementById('viewEventModal');
            const openEditFromView = document.getElementById('openEditFromView');
            let activeCard = null;

            document.querySelectorAll('.box_clickable').forEach(card => {
                card.addEventListener('click', () => {
                    activeCard = card;

                    document.getElementById('view_name').textContent = card.dataset.name;
                    document.getElementById('view_lead').textContent = card.dataset.leadName;
                    document.getElementById('view_date').textContent = card.dataset.date || 'TBD';
                    document.getElementById('view_location').textContent = card.dataset.location || 'TBD';
                    document.getElementById('view_details').textContent = card.dataset.details;

                    const tagContainer = document.getElementById('view_tags');
                    tagContainer.innerHTML = '';
                    (card.dataset.tags || '').split(',').filter(Boolean).forEach(tag => {
                        const pill = document.createElement('span');
                        pill.className = 'tag_pill';
                        pill.textContent = tag;
                        tagContainer.appendChild(pill);
                    });

                    const deleteInput = document.getElementById('view_delete_event_id');
                    if (deleteInput) {
                        deleteInput.value = card.dataset.id;
                    }

                    viewModal.style.display = 'block';
                });
            });

            // Edit modal wiring - only present in the DOM for eboard/admin users
            const editModal = document.getElementById('editEventModal');
            const editDetails = document.getElementById('edit_eventDetails');
            const editCounter = document.getElementById('edit_details_counter');

            function populateEditModal(card) {
                document.getElementById('edit_event_id').value = card.dataset.id;
                document.getElementById('delete_event_id').value = card.dataset.id;
                document.getElementById('edit_eventName').value = card.dataset.name;
                document.getElementById('edit_eventDetails').value = card.dataset.details;
                document.getElementById('edit_dateScheduled').value = card.dataset.date;
                document.getElementById('edit_location').value = card.dataset.location;
                document.getElementById('edit_eventLead').value = card.dataset.leadId;

                if (editCounter) {
                    editCounter.textContent = `${card.dataset.details.length} / 150`;
                }

                const selectedTags = (card.dataset.tags || '').split(',').filter(Boolean);
                document.querySelectorAll('.edit_tag_checkbox').forEach(box => {
                    box.checked = selectedTags.includes(box.value);
                });
            }

            if (editModal && openEditFromView) {
                openEditFromView.addEventListener('click', () => {
                    if (activeCard) {
                        populateEditModal(activeCard);
                    }
                    viewModal.style.display = 'none';
                    editModal.style.display = 'block';
                });
            }

            if (editDetails && editCounter) {
                editDetails.addEventListener('input', () => {
                    editCounter.textContent = `${editDetails.value.length} / 150`;
                });
            }
        });
    </script>

</body>
</html>