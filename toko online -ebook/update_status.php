<?php
require 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];

    // Perbarui status menjadi "Sukses" setelah pembayaran berhasil
    $stmt = $conn->prepare("UPDATE order_history SET status = 'Sukses' WHERE order_id = ?");
    $stmt->bind_param("s", $orderId);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
}
?>
