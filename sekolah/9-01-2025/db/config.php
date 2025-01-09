<?php
$host = "localhost";
$user = "root";
$password = ""; // Kosong jika menggunakan XAMPP default
$dbname = "adventure_game";

$conn = new mysqli($host, $user, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
