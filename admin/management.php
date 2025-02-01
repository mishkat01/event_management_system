<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'event_management_system');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle block/unblock request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $new_status = intval($_POST['status']);

    $update_query = "UPDATE users SET status = $new_status WHERE id = $user_id";
    if ($conn->query($update_query) === TRUE) {
        header("Location: management.php");
        exit();
    } else {
        $_SESSION['message'] = $conn->error;
        echo "<script>alert('" . addslashes($conn->error) . "');</script>";
    }
}

// Fetch managers from the database
$query = "SELECT id, username, email, status FROM users WHERE role = 'manager'";
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Error in SQL query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Management</h2>
        <a href="manage-events.php" class="btn btn-secondary mb-3">
            ← Back
        </a>
        <a href="create_manager.php" class="btn btn-primary mb-3">
            Create Manager
        </a>
        <!-- manager list -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <span class="badge <?= $row['status'] == 1 ? 'bg-success' : 'bg-danger' ?>">
                                <?= $row['status'] == 1 ? 'Active' : 'Blocked' ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="status" value="<?= $row['status'] == 1 ? 0 : 1 ?>">
                                <button type="submit" class="btn <?= $row['status'] == 1 ? 'btn-danger' : 'btn-success' ?>">
                                    <?= $row['status'] == 1 ? 'Block' : 'Unblock' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>


<!-- Bootstrap JS -->
<script src="../js/bootstrap.bundle.min.js"></script>

</html>