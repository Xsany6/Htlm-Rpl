<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: reting-produk.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

$insertQuery = "INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);

if ($stmt->execute()) {
    echo "<script>alert('Ulasan berhasil dikirim!'); window.location.href='reting-produk.php';</script>";
} else {
    echo "<script>alert('Gagal mengirim ulasan. Coba lagi!');</script>";
}
?>
