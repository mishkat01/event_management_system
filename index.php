<!DOCTYPE html>
<html>

<head>
    <title>Events</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Available Events</h1>

        <?php
        session_start();
        require 'config/config.php';

        // Fetch all events
        $stmt = $pdo->query("SELECT * FROM events");
        $events = $stmt->fetchAll();

        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>{$_SESSION['error']}</div>";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>{$_SESSION['success']}</div>";
            unset($_SESSION['success']);
        }
        ?>

        <ul class="list-group">
            <?php foreach ($events as $event): ?>
                <li class="list-group-item">
                    <h3><?= htmlspecialchars($event['name']) ?></h3>
                    <p><?= htmlspecialchars($event['description']) ?></p>
                    <p>Max Capacity: <?= $event['max_capacity'] ?></p>

                    <!-- Registration Form -->
                    <form action="register-event.php" method="POST">
                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                        <div class="form-group">
                            <label for="name">Your Name:</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Your Email:</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>

</html>