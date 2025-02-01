<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied! Please login.");
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'event_management_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in user ID
$user_id = $_SESSION['user_id'];

// Check if event ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$event_id = (int) $_GET['id'];

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Event not found or you don't have permission to edit it.");
}

$event = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $max_capacity = $_POST['max_capacity'];

    // Update event in database
    $stmt = $conn->prepare("UPDATE events SET name = ?, description = ?, max_capacity = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssiii", $name, $description, $max_capacity, $event_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event updated successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-primary">Edit Event</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Event Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($event['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" required><?= htmlspecialchars($event['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="max_capacity">Max Capacity</label>
                <input type="number" name="max_capacity" class="form-control" value="<?= htmlspecialchars($event['max_capacity']) ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update Event</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

<!-- Bootstrap JS -->
<script src="../../js/bootstrap.bundle.min.js"></script>

</html>