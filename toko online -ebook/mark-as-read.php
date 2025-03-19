<?php
require 'db/config.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Tandai pesan sebagai terbaca
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
