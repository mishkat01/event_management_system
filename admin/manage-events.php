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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h1 class="text-center text-primary mb-4">Admin - Manage Events</h1>
                <div class="d-flex justify-content-end">
                    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>

        <!-- Add Event Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Add New Event</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Event Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter event name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter event description" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="max_capacity" class="form-label">Max Capacity</label>
                        <input type="number" name="max_capacity" id="max_capacity" class="form-control" placeholder="Enter maximum capacity" required>
                    </div>
                    <button type="submit" name="add_event" class="btn btn-primary w-100">Add Event</button>
                </form>
            </div>
        </div>

        <!-- Event List -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h2 class="mb-0">All Events</h2>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
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
                                    <a href="manage-events.php?delete_event_id=<?= $event['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Download Report -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h2 class="mb-0">Download Event Report</h2>
            </div>
            <div class="card-body">
                <form action="download-report.php" method="GET">
                    <div class="form-group mb-3">
                        <label for="event_id" class="form-label">Select Event</label>
                        <select name="event_id" id="event_id" class="form-control" required>
                            <option value="">Select an event</option>
                            <?php foreach ($events as $event): ?>
                                <option value="<?= $event['id'] ?>"><?= htmlspecialchars($event['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Download Report</button>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>