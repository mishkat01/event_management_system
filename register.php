<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        die("All fields are required!");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

  
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);


    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        die("Username or email already taken!");
    }

    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    if ($stmt->execute([$username, $email, $hashed_password])) {
        echo "Registration successful!";
    } else {
        echo "An error occurred!";
    }
}
?>
