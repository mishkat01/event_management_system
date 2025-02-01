<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $conn = new mysqli('localhost', 'root', '', 'event_management_system');
    $stmt = $conn->prepare("INSERT INTO users (username,email, password) VALUES (?, ?,?)");
    $stmt->bind_param('sss', $username, $email, $password);

    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location: user/dashboard.php");
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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Document</title>
</head>

<body>
    <form method="POST" class="w-50 mx-auto mt-5 p-4 border rounded shadow-sm">
        <h2 class="text-center mb-4">Register</h2>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-success btn-block">Register</button>
        <a href="auth/login.php" class="btn btn-secondary btn-block mt-2">Login</a>
    </form>

</body>
<!-- Bootstrap JS -->
<script src="js/bootstrap.bundle.min.js"></script>

</html>