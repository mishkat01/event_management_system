<?php
session_start();
require '../config/config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get the event ID from the request (ensure it's provided)
if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    die('Event ID is required.');
}

$event_id = (int)$_GET['event_id'];

// Fetch the event details for validation
$stmt = $pdo->prepare("SELECT name FROM events WHERE id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die('Event not found.');
}

// Generate CSV for registrations
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="registrations_list_event_' . $event_id . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Registration ID', 'User ID', 'User Name', 'Email', 'Phone', 'Registered At']);

// Fetch registrations with user details using the foreign key
$stmt = $pdo->prepare("
    SELECT registrations.id AS registration_id, 
           users.id AS user_id, 
           users.username AS user_name, 
           users.email, 
           registrations.created_at
    FROM registrations
    INNER JOIN users ON registrations.user_id = users.id
    WHERE registrations.event_id = :event_id
");
$stmt->execute(['event_id' => $event_id]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
