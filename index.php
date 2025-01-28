<!DOCTYPE html>
<html>

<head>
    <title>Events</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        <h1 class="mb-4 text-center">Available Events</h1>

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

        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-6">
                    <div class="card event-card">
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($event['name']) ?></h3>
                            <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                            <p><strong>Max Capacity:</strong> <?= $event['max_capacity'] ?></p>

                            <!-- Registration Form -->
                            <form action="user/register-event.php" method="POST" class="mt-3">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <div class="form-group">
                                    <label for="name">Your Name:</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Your Email:</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Register</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>