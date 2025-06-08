<?php
include 'db/config.php';

$query = "SELECT id, name, price, image FROM products LIMIT 5";
$result = $conn->query($query);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>
