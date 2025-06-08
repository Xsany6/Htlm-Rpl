<?php
include 'db/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $messageId = $_POST['message_id'];
    $userId = $_SESSION['user_id'];

    $query = "UPDATE messages SET is_read = 1 WHERE id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $messageId, $userId);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
