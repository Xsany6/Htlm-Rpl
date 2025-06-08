<?php
include 'db/config.php';
$conn->query("DELETE FROM chat WHERE waktu < NOW() - INTERVAL 5 MINUTE");
?>
