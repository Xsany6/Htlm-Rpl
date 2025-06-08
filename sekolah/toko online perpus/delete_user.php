<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Periksa apakah permintaan dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    // Cegah penghapusan admin utama
    $check_role = $conn->query("SELECT role FROM users WHERE id = $userId")->fetch_assoc();
    if ($check_role && $check_role['role'] === 'admin') {
        $_SESSION['error'] = "Admin cannot be deleted!";
    } else {
        $conn->query("DELETE FROM users WHERE id = $userId");
        $_SESSION['success'] = "User deleted successfully!";
    }
}

header("Location: users.php");
exit();
?>
