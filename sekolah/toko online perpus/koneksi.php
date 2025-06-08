<?php
$host = "localhost"; // Sesuaikan dengan konfigurasi server Anda
$user = "root"; // Default username XAMPP
$password = ""; // Default password kosong di XAMPP
$database = "library_db"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
