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

// Generate CSV for attendees
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendees_list_event_' . $event_id . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Name', 'Email', 'Registered At']);

// Fetch attendees for the specific event
$stmt = $pdo->prepare("
    SELECT attendees.id, attendees.name, attendees.email, attendees.created_at 
    FROM attendees 
    WHERE attendees.event_id = :event_id
");
$stmt->execute(['event_id' => $event_id]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
