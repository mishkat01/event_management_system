<?php
session_start();
require 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Validate event ID
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        $_SESSION['error'] = "Event not found.";
        header('Location: index.php');
        exit;
    }

    // Check if max capacity is reached
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $current_registrations = $stmt->fetchColumn();

    if ($current_registrations >= $event['max_capacity']) {
        $_SESSION['error'] = "Event is fully booked.";
        header('Location: index.php');
        exit;
    }

    // Check if the user (email) is already registered for this event
    $stmt = $pdo->prepare("SELECT * FROM registrations WHERE event_id = ? AND email = ?");
    $stmt->execute([$event_id, $email]);
    $existing_registration = $stmt->fetch();

    if ($existing_registration) {
        $_SESSION['error'] = "You have already registered for this event.";
        header('Location: index.php');
        exit;
    }

    // Register the user
    $stmt = $pdo->prepare("INSERT INTO registrations (event_id, name, email) VALUES (?, ?, ?)");
    $stmt->execute([$event_id, $name, $email]);

    $_SESSION['success'] = "You have successfully registered for the event.";
    header('Location: index.php');
    exit;
}
