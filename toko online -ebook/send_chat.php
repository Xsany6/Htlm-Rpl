<?php
session_start();
include 'db/config.php';

// Set header JSON agar response bisa dibaca oleh AJAX
header('Content-Type: application/json');

// Pastikan pengguna sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(["status" => "error", "message" => "Anda harus login terlebih dahulu."]);
    exit();
}

$pengirim = "";

// Jika admin login, gunakan langsung username "admin"
if ($_SESSION['username'] === "admin") {
    $pengirim = "admin";
} else {
    // Ambil username berdasarkan user_id untuk user biasa
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "ID pengguna tidak ditemukan."]);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $query->bind_result($username);
    $query->fetch();
    $query->close();

    if (!$username) {
        echo json_encode(["status" => "error", "message" => "Username tidak ditemukan."]);
        exit();
    }

    $pengirim = $username;
}

// Proses pengiriman pesan
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pesan = trim($_POST['pesan']); // Hilangkan spasi berlebih
    $pesan = htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8'); // Cegah XSS

    if (!empty($pesan)) {
        // Simpan pesan ke database
        $stmt = $conn->prepare("INSERT INTO chat (pengirim, isi, waktu) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $pengirim, $pesan);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Pesan berhasil dikirim."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal mengirim pesan: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Pesan tidak boleh kosong."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Metode tidak valid."]);
}

$conn->close();
?>
