<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];

$query = "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND is_read = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update messages"]);
}
$stmt->close();
$conn->close();
?>
