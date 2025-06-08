<?php
session_start();
include 'db/config.php'; // Pastikan ini sudah terhubung dengan database

header('Content-Type: application/json'); // Set response dalam format JSON

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => "Anda harus login untuk menghapus pesan."]);
        exit;
    }

    if (!isset($_POST['message_id'])) {
        echo json_encode(["success" => false, "error" => "ID pesan tidak ditemukan."]);
        exit;
    }

    $messageId = intval($_POST['message_id']);
    $userId = $_SESSION['user_id'];

    // Periksa apakah pesan benar-benar milik pengguna yang sedang login
    $checkQuery = "SELECT id FROM messages WHERE id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $messageId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Pesan tidak ditemukan atau Anda tidak memiliki izin."]);
        exit;
    }

    // Hapus pesan jika memang milik pengguna
    $deleteQuery = "DELETE FROM messages WHERE id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("ii", $messageId, $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Gagal menghapus pesan."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Metode tidak diizinkan."]);
}
?>
