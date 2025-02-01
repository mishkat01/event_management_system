<?php
session_start();

// check if the form is submitted successfully
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'root', '', 'event_management_system');
    $stmt = $conn->prepare("SELECT id, password, role, status FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['status'] == 0) {
            echo "<script>alert('You are banned. Access denied.'); window.location.href='login.php';</script>";
            exit;
        }
        // Redirect users based on their roles
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: ../admin/manage-events.php");
                exit;
            } elseif ($row['role'] === 'user') {
                header("Location: ../user/dashboard.php");
                exit;
            } elseif ($row['role'] === 'manager') {
                header("Location: ../manager/event/dashboard.php");
                exit;
            } else {
                echo "<script>alert('Invalid User. Access denied.');</script>";
            }
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
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
    <title>Login</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body class="bg-primary bg-gradient d-flex align-items-center justify-content-center vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow rounded">
                    <div class="card-body p-4">
                        <h2 class="text-center text-primary mb-4">Login</h2>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Login</button>
                            <a href="../index.php" class="btn btn-outline-secondary w-100 mt-2">Register</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>