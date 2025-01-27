<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied! Please login.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $max_capacity = $_POST['max_capacity'];

    $conn = new mysqli('localhost', 'root', '', 'event_management_system');
    $stmt = $conn->prepare("INSERT INTO events (name, description, max_capacity) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $name, $description, $max_capacity);

    if ($stmt->execute()) {
        echo "Event created successfully!";
        header("Location: ../event/dashboard.php");
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<form method="POST">
    Event Name: <input type="text" name="name" required><br>
    Description: <textarea name="description"></textarea><br>
    Max Capacity: <input type="number" name="max_capacity" required><br>
    <button type="submit">Create Event</button>
</form>