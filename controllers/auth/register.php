<?php
require '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate required fields
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        die("All fields are required!");
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if username or email already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        die("Username or email already taken!");
    }

    // Insert the new user into the database
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    if ($stmt->execute([$username, $email, $hashed_password])) {
        // Fetch the user ID for the newly registered user
        $user_id = $pdo->lastInsertId();

        // Store user information in session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;

        // Redirect to the protected page (index.php)
        header('Location: ../../index.php');
        exit();
    } else {
        echo "An error occurred!";
    }
}
?>
