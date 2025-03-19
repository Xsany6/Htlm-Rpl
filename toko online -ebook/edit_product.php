<?php
session_start();
require_once 'db/config.php';

$response = ['success' => false];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $name = $_POST['name'] ?? null;
    $price = $_POST['price'] ?? null;
    $stok = $_POST['stok'] ?? null;

    if ($product_id > 0 && $name && $price && $stok) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stok = ? WHERE id = ?");
        $stmt->bind_param("sdii", $name, $price, $stok, $product_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
        }

        $stmt->close();
    }
}

$conn->close();
echo json_encode($response);
?>
