<?php
session_start();

// Cek apakah pengguna sudah login, jika tidak redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Arahkan ke login jika belum login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Dashboard</h1>
    <p>Selamat datang, <?php echo $_SESSION['username']; ?>!</p>
    <a href="logout.php">Logout</a>
</body>
</html>
