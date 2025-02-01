<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied! Please login.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $max_capacity = $_POST['max_capacity'];
    $user_id = $_SESSION['user_id'];

    $conn = new mysqli('localhost', 'root', '', 'event_management_system');
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO events (name,user_id, description, max_capacity) VALUES (?,?, ?, ?)");
    // Bind parameters (s = string, i = integer)
    $stmt->bind_param("sisi", $name, $user_id, $description, $max_capacity);

    if ($stmt->execute()) {
        echo "Event created successfully!";
        header("Location: ../event/dashboard.php");
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" class="p-4 border rounded bg-white shadow-sm">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <h3 class="text-primary mb-3 text-center">Create New Event</h3>

                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Event Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter event name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter event description"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="max_capacity" class="form-label">Max Capacity</label>
                        <input type="number" id="max_capacity" name="max_capacity" class="form-control" placeholder="Enter maximum capacity" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Create Event</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>