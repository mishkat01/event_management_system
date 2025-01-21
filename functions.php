
<?php
require_once 'config.php'; // Include database connection

function getAllItems($conn) {
    $stmt = $conn->query("SELECT * FROM events");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>