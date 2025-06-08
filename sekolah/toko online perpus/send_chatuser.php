<?php
session_start();
include 'db/config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die("Error: Anda harus login terlebih dahulu.");
}

// Pastikan sesi user_id tersedia
if (!isset($_SESSION['user_id'])) {
    die("Error: ID pengguna tidak ditemukan.");
}

$user_id = $_SESSION['user_id'];

// Ambil username dari tabel users berdasarkan user_id
$query = $conn->prepare("SELECT username FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$query->bind_result($username);
$query->fetch();
$query->close();

// Pastikan username ditemukan
if (!$username) {
    die("Error: Username tidak ditemukan di database.");
}

// Proses pengiriman pesan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pesan = trim($_POST['pesan']); // Hilangkan spasi berlebih

    if (!empty($pesan)) {
        // Simpan pesan ke database
        $stmt = $conn->prepare("INSERT INTO chat (pengirim, isi) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $pesan);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: csuser.php"); // Redirect kembali ke halaman chat
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Pesan tidak boleh kosong.";
    }
}

$conn->close();
?>
