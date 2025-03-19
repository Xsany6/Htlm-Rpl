<?php
session_start();
include 'db/config.php';

// Ambil data chat dari database
$result = $conn->query("SELECT * FROM chat ORDER BY waktu ASC");

$chats = [];
while ($row = $result->fetch_assoc()) {
    $chats[] = [
        'pengirim' => htmlspecialchars($row['pengirim']),
        'isi' => htmlspecialchars($row['isi']),
        'waktu' => $row['waktu']
    ];
}

echo json_encode($chats);
?>
