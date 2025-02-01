<?php
session_start();
require '../config/config.php';

// Fetch all events along with current registrations count
$stmt = $pdo->query("SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS registered_count FROM events e");
$events = $stmt->fetchAll();

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Events</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .event-card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h1 class="text-center text-primary mb-4">Available Events</h1>
                <div class="d-flex justify-content-end">
                    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>


        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-6">
                    <div class="card event-card">
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($event['name']) ?></h3>
                            <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                            <p><strong>Max Capacity:</strong> <?= $event['max_capacity'] ?></p>
                            <p><strong>Registered:</strong> <?= $event['registered_count'] ?> / <?= $event['max_capacity'] ?></p>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="register-event.php" method="POST" class="mt-3">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-block" <?= ($event['registered_count'] >= $event['max_capacity']) ? 'disabled' : '' ?>>
                                        <?= ($event['registered_count'] >= $event['max_capacity']) ? 'Full' : 'Register' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-warning btn-block">Login to Register</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>