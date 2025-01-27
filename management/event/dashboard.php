<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied! Please login.");
}

$conn = new mysqli('localhost', 'root', '', 'event_management_system');
$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$result = $conn->query("SELECT * FROM events LIMIT $limit OFFSET $offset");
while ($event = $result->fetch_assoc()) {
    echo "<h3>" . $event['name'] . "</h3>";
    echo "<p>" . $event['description'] . "</p>";
    echo "<p>Max Capacity: " . $event['max_capacity'] . "</p><hr>";
}

// Pagination
$total_events = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()['total'];
$total_pages = ceil($total_events / $limit);

for ($i = 1; $i <= $total_pages; $i++) {
    echo "<a href='dashboard.php?page=$i'>$i</a> ";
}

$conn->close();
