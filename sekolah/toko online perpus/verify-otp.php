<?php
session_start();
$conn = new mysqli("localhost", "root", "", "library_db");

if (!isset($_SESSION['user_id'])) {
    die("Silakan login terlebih dahulu.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT otp_code FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['otp_code'] == $otp) {
        $sql = "UPDATE users SET otp_code = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        echo "Email berhasil diverifikasi!";
    } else {
        echo "Kode OTP salah.";
    }
}
?>
<form method="post">
    <input type="text" name="otp" placeholder="Masukkan OTP">
    <button type="submit">Verifikasi</button>
</form>
