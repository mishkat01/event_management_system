<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $conn = new mysqli('localhost', 'root', '', 'event_management_system');
    $stmt = $conn->prepare("INSERT INTO users (username,email, password) VALUES (?, ?,?)");
    $stmt->bind_param('sss', $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: user/dashboard.php");
        exit;
    } else {
        $_SESSION['message'] = $conn->error;
        echo "<script>alert('" . addslashes($conn->error) . "');</script>";
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
    <title>Register </title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="bg-primary bg-gradient d-flex align-items-center justify-content-center vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow rounded">
                    <div class="card-body p-4">
                        <h2 class="text-center text-primary mb-4">Register</h2>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>
                            <a href="auth/login.php" class="btn btn-outline-secondary w-100 mt-2">Login</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>