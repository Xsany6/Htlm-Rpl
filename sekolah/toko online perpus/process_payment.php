<?php
session_start();
include 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethodId = filter_input(INPUT_POST, 'payment_method', FILTER_VALIDATE_INT);

    // Validasi metode pembayaran
    $stmt = $conn->prepare("SELECT * FROM payment_methods WHERE id = ?");
    $stmt->bind_param("i", $paymentMethodId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Metode pembayaran tidak valid.";
        exit();
    }

    $paymentMethod = $result->fetch_assoc();

    // Simpan data pesanan (implementasi tergantung kebutuhan)
    echo "Pesanan berhasil dibuat menggunakan metode pembayaran: " . htmlspecialchars($paymentMethod['method_name']);
    // Redirect ke halaman sukses atau lainnya
    exit();
}
