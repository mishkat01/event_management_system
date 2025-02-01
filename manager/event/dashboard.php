<?php
session_start();

// Check if the user is a manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header('Location: ../../auth/login.php');
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

// Pagination and filtering logic
$limit = 2; // Display 2 events per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch filter and sort parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';

// SQL query for fetching events based on filters and sorting
$sql = "SELECT * FROM events WHERE user_id = ? AND name LIKE ? ORDER BY $sort $order LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$filter_like = "%" . $filter . "%";
$stmt->bind_param("ssii", $user_id, $filter_like, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total event count for pagination
$stmt_total = $conn->prepare("SELECT COUNT(*) AS total FROM events WHERE user_id = ? AND name LIKE ?");
$stmt_total->bind_param("ss", $user_id, $filter_like);
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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">Event Dashboard</h1>
            <a href="../../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
        <a href="create_event.php" class="btn btn-info">Create Event</a><br><br>

        <!-- Filter and Sort options -->
        <form method="get" action="dashboard.php" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="filter" class="form-control" placeholder="Search by name" value="<?= htmlspecialchars($filter) ?>">
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-control">
                        <option value="created_at" <?= $sort == 'created_at' ? 'selected' : '' ?>>Sort by Date</option>
                        <option value="name" <?= $sort == 'name' ? 'selected' : '' ?>>Sort by Name</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="order" class="form-control">
                        <option value="asc" <?= $order == 'asc' ? 'selected' : '' ?>>Ascending</option>
                        <option value="desc" <?= $order == 'desc' ? 'selected' : '' ?>>Descending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">Apply</button>
                </div>
            </div>
        </form>

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

        <!-- Pagination Links -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&filter=<?= urlencode($filter) ?>&sort=<?= $sort ?>&order=<?= $order ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&filter=<?= urlencode($filter) ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&filter=<?= urlencode($filter) ?>&sort=<?= $sort ?>&order=<?= $order ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</body>
<!-- Bootstrap JS -->
<script src="../../js/bootstrap.bundle.min.js"></script>

</html>

<?php $conn->close(); ?>