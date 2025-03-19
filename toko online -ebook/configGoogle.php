<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('572532228729-un5n6nq380h55qluuhelpmkrangu2u9c.apps.googleusercontent.com'); // Ganti dengan Client ID dari Google
$client->setClientSecret('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'); // Ganti dengan Client Secret
$client->setRedirectUri('http://localhost/projek-perpus/login.php'); // Ganti sesuai URL proyek
$client->addScope("email");
$client->addScope("profile");

session_start();

$conn = new mysqli("localhost", "root", "", "nama_database"); // Ganti sesuai database
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
