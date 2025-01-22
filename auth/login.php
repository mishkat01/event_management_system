<?php
require '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        die("Username and password are required!");
    }

    $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;

        header('Location: ../event/read.php');
        exit();
    } else {
        echo "Invalid username or password!";
    }
}
?>
