<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipientEmail = $_POST['recipient_email'];

    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        echo "Email tidak valid.";
        exit();
    }

    // Path file yang akan dikirim
    $filePath = "ebook/Buku online -20241106T131308Z-001/Buku online/105. Hidup Damai Tanpa Berpikir Berlebihan.pdf";  // Ganti dengan path yang benar
    $fileName = basename($filePath);

    // Inisialisasi PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Pengaturan SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'blecktiger8@gmail.com';  // Ganti dengan alamat email Gmail
        $mail->Password = 'fahri.3210A';     // Ganti dengan App Password jika 2FA aktif
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Gunakan STARTTLS
        $mail->Port = 587;  // Gunakan port 587 untuk TLS


        // Pengaturan Pengirim dan Penerima
        $mail->setFrom('blecktiger8@gmail.com', 'Xsany6');
        $mail->addAddress($recipientEmail);  // Alamat email tujuan

        // Menambahkan lampiran
        $mail->addAttachment($filePath, $fileName);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = 'Bukti Pembayaran dan File Produk';
        $mail->Body    = 'Berikut adalah bukti pembayaran dan file produk yang Anda pesan.';

        // Mengirimkan email
        $mail->send();
        echo 'File telah dikirim ke email Anda.';
    } catch (Exception $e) {
        echo "Terjadi kesalahan saat mengirim email. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
