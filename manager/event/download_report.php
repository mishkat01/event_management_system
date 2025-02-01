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

// Fetch registered users for the manager's events
$query = "
    SELECT users.id, users.username, users.email, events.name AS event_name
    FROM registrations
    JOIN users ON registrations.user_id = users.id
    JOIN events ON registrations.event_id = events.id
    WHERE events.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No registered users found.");
}

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=registered_users.csv');

// Open file output stream
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['User ID', 'Name', 'Email', 'Event Name']);

// Write data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Close database connection
$stmt->close();
$conn->close();
