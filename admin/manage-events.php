<?php
session_start();
require '../config/config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Fetch events
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll();

// Add a new event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $max_capacity = $_POST['max_capacity'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO events (name, description, max_capacity, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $max_capacity, $created_by]);

    header('Location: manage-events.php');
    exit;
}

// Delete an event
if (isset($_GET['delete_event_id'])) {
    $event_id = $_GET['delete_event_id'];

    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$event_id]);

    header('Location: manage-events.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Events</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Admin - Manage Events</h1>
        <a href="../logout.php" class="btn btn-danger">Logout</a>

        <!-- Add Event Form -->
        <h2>Add New Event</h2>
        <form method="POST">
            <div class="form-group">
                <label>Event Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label>Max Capacity</label>
                <input type="number" name="max_capacity" class="form-control" required>
            </div>
            <button type="submit" name="add_event" class="btn btn-primary">Add Event</button>
        </form>

        <!-- Event List -->
        <h2>All Events</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Max Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['name']) ?></td>
                        <td><?= htmlspecialchars($event['description']) ?></td>
                        <td><?= htmlspecialchars($event['max_capacity']) ?></td>
                        <td>
                            <a href="manage-events.php?delete_event_id=<?= $event['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Download Report -->
        <h2>Download Event Report</h2>
        <form action="download-report.php" method="GET">
            <div class="form-group">
                <label>Select Event</label>
                <select name="event_id" class="form-control" required>
                    <option value="">Select an event</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>"><?= htmlspecialchars($event['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-info">Download Report</button>
        </form>
    </div>
</body>

</html>