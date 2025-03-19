<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$conn = new mysqli("localhost", "root", "", "library_db");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Koneksi database gagal."]));
}

$email = isset($_POST['email']) ? trim($_POST['email']) : "";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Format email tidak valid."]);
    exit;
}

// Buat OTP
$otp = rand(100000, 999999);
$otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Simpan OTP ke database
$sql = "UPDATE users SET email = ?, otp_code = ?, otp_expiry = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $email, $otp, $otp_expiry, $_SESSION['user_id']);
$stmt->execute();

// Kirim email dengan PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ff29042028@gmail.com';
    $mail->Password = 'ijgl ywsb lbzt mndw'; // Gunakan App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('your-email@gmail.com', 'Admin');
    $mail->addAddress($email);
    $mail->Subject = 'Verifikasi Email Anda';
    $mail->Body = "Kode OTP Anda: $otp\nGunakan kode ini untuk verifikasi email.";

    $mail->send();
    echo json_encode(["success" => true, "message" => "Kode OTP telah dikirim ke email Anda!"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Gagal mengirim email: {$mail->ErrorInfo}"]);
}
?>
