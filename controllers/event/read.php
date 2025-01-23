<?php
require_once '../../config/config.php'; // Include database connection
session_start();


if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not authenticated
    header('Location: login.html');
    exit();
}
function getAllItems($conn) {
    $stmt = $conn->query("SELECT * FROM events");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$items = getAllItems($pdo);
?>


