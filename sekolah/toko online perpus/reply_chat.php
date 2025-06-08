<?php
session_start();
include 'db/config.php';

// Pastikan admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Error: Anda harus login sebagai admin untuk membalas pesan.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pesan = trim($_POST['pesan']);
    $pengirim = "admin"; // Nama pengirim otomatis "admin"

    if (!empty($pesan)) {
        // Simpan balasan ke database
        $stmt = $conn->prepare("INSERT INTO chat (pengirim, isi) VALUES (?, ?)");
        $stmt->bind_param("ss", $pengirim, $pesan);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: cs.php"); // Redirect kembali ke halaman chat
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
