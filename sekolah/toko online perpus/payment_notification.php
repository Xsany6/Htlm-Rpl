<?php
require 'db/config.php';
require_once 'vendor/autoload.php'; // Pastikan composer sudah di-install

use Midtrans\Config;
use Midtrans\Transaction;

Config::$serverKey = 'SB-Mid-server-g6_POD2Koz8RALVP0AAMKkS3';
Config::$isProduction = false;
Config::$isSanitized = true;
Config::$is3ds = true;

// Debugging: Simpan log JSON yang diterima
$json = file_get_contents('php://input');
file_put_contents('log_midtrans_request.txt', $json . PHP_EOL, FILE_APPEND);

$notification = json_decode($json, true);

if (!$json) {
    error_log("Failed to receive JSON input");
    http_response_code(400);
    exit("Failed to receive JSON input");
}

if (!$notification) {
    error_log("Failed to decode JSON: " . $json);
    http_response_code(400);
    exit("Failed to decode JSON");
}

error_log("Received notification: " . print_r($notification, true));

// Pastikan order_id dan transaction_status ada
if (!isset($notification['order_id'], $notification['transaction_status'])) {
    error_log("Invalid notification received: " . print_r($notification, true));
    http_response_code(400);
    exit("Invalid notification");
}

$orderId = $notification['order_id'];
$transactionStatus = $notification['transaction_status'];

$statusUpdate = "";

if ($transactionStatus === "settlement" || $transactionStatus === "capture") {
    $statusUpdate = "Sukses";
} elseif ($transactionStatus === "pending") {
    $statusUpdate = "Menunggu Pembayaran";
} elseif ($transactionStatus === "expire") {
    $statusUpdate = "Kadaluarsa";
} elseif ($transactionStatus === "cancel" || $transactionStatus === "deny" || $transactionStatus === "failure") {
    $statusUpdate = "Gagal";
} else {
    $statusUpdate = "Unknown";
}

// Cek apakah order_id ada dalam database sebelum update
$stmt = $conn->prepare("SELECT id FROM order_history WHERE order_id = ?");
$stmt->bind_param("s", $orderId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $stmt = $conn->prepare("UPDATE order_history SET status = ? WHERE order_id = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $statusUpdate, $orderId);
        if (!$stmt->execute()) {
            error_log("Failed to update order: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Database statement error: " . $conn->error);
    }
} else {
    error_log("Order ID not found: " . $orderId);
}

// Kirim response ke Midtrans
http_response_code(200);
echo "OK";
?>
