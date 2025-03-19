<?php
session_start();
require_once 'db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $email;
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['message'] = "Username atau password salah!";
        header("Location: login.php");
        exit;
    }
}
?>
