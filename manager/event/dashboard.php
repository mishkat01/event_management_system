<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied! Please login.");
}

// Check if the user is a manager
if ($_SESSION['role'] !== 'manager') {
    header('Location: ../auth/login.php');
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'event_management_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in user ID
$user_id = $_SESSION['user_id'];

// Handle event deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// Pagination logic
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch only events created by the logged-in user
$stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ? LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total event count for pagination
$stmt_total = $conn->prepare("SELECT COUNT(*) AS total FROM events WHERE user_id = ?");
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$total_events = $stmt_total->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_events / $limit);

$stmt->close();
$stmt_total->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">Event Dashboard</h1>
            <a href="../../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
        <a href="create_event.php" class="btn btn-info">Create Event</a><br><br>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">My Events</h2>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($event = $result->fetch_assoc()): ?>
                        <div class="mb-4">
                            <h3 class="text-secondary"><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <p><strong>Max Capacity:</strong> <?= htmlspecialchars($event['max_capacity']) ?></p>
                            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="dashboard.php?delete_id=<?= $event['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            <a href="download_report.php" class="btn btn-success">Download Report</a><br><br>
                            <hr>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">No events found.</p>
                <?php endif; ?>
            </div>
        </div>

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="dashboard.php?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>

<?php $conn->close(); ?>