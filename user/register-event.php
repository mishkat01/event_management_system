<?php
session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please login to register for an event.";
        header('Location: ../index.php');
        exit;
    }

    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    // Validate event ID
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        $_SESSION['error'] = "Event not found.";
        header('Location: dashboard.php');
        exit;
    }

    // Check if max capacity is reached
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $current_registrations = $stmt->fetchColumn();

    if ($current_registrations >= $event['max_capacity']) {
        $_SESSION['error'] = "Event is fully booked.";
        header('Location: dashboard.php');
        exit;
    }

    // Check if the user is already registered for this event
    $stmt = $pdo->prepare("SELECT * FROM registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    $existing_registration = $stmt->fetch();

    if ($existing_registration) {
        $_SESSION['error'] = "You have already registered for this event.";
        header('Location: dashboard.php');
        exit;
    }

    // Register the user
    $stmt = $pdo->prepare("INSERT INTO registrations (event_id, user_id) VALUES (?, ?)");
    $stmt->execute([$event_id, $user_id]);

    $_SESSION['success'] = "You have successfully registered for the event.";
    header('Location: dashboard.php');
    exit;
}
