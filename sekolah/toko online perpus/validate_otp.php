<?php
// Menghubungkan ke database menggunakan PDO
$host = 'localhost';      // Ganti dengan host database Anda (misalnya localhost)
$db   = 'library_db';  // Ganti dengan nama database Anda
$user = 'root';       // Ganti dengan username database Anda
$pass = '';       // Ganti dengan password database Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set opsi untuk menangani kesalahan
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}


// Import library PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Koneksi database
require_once 'db/config.php';

// Fungsi untuk mengirim OTP ke email
function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ff29042028@gmail.com'; // Ganti dengan email Anda
        $mail->Password = 'tugh eaqv zyuf ikhv';   // Ganti dengan app password Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Pengaturan pengirim dan penerima
        $mail->setFrom('ffrdf3136@gmail.com', 'ffrdf3136'); // Sesuaikan dengan Username
        $mail->addAddress($email);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = 'Kode OTP Anda';
        $mail->Body = "Kode OTP Anda adalah <b>$otp</b>. Kode ini berlaku selama 5 menit.";

        // Kirim email
        $mail->send();
    } catch (Exception $e) {
        throw new Exception("Gagal mengirim email: " . $mail->ErrorInfo);
    }
}

// CSRF Protection
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF tidak valid.");
    }

    // Ambil data dari form
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $otp_input = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_STRING);

    if ($email && !$otp_input) { // Mengirim OTP
        $stmt = $pdo->prepare("SELECT id FROM pengguna WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $otp = rand(100000, 999999);
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

            $stmt = $pdo->prepare("UPDATE pengguna SET otp = :otp, otp_expiry = :otp_expiry WHERE email = :email");
            $stmt->execute(['otp' => $otp, 'otp_expiry' => $otp_expiry, 'email' => $email]);

            try {
                sendOTP($email, $otp);
                $message = "OTP berhasil dikirim ke email Anda.";
            } catch (Exception $e) {
                $message = $e->getMessage();
            }
        } else {
            $message = "Email tidak ditemukan!";
        }
    } elseif ($email && $otp_input) { // Validasi OTP
        $stmt = $pdo->prepare("SELECT otp, otp_expiry FROM pengguna WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && $user['otp'] === $otp_input && strtotime($user['otp_expiry']) > time()) {
            $stmt = $pdo->prepare("UPDATE pengguna SET otp = NULL, otp_expiry = NULL WHERE email = :email");
            $stmt->execute(['email' => $email]);

            $_SESSION['success'] = "Verifikasi berhasil!";
            header("Location: index.php");
            exit;
        } else {
            $message = "OTP salah atau sudah kadaluwarsa!";
        }
    } else {
        $message = "Data tidak lengkap!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(120deg, #8e44ad, #3498db);
        }

        .verify-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .verify-form {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 14px;
            color: #666;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #3498db;
        }

        .btn {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .message {
            text-align: center;
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-form">
            <h2>Verify OTP</h2>
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="otp">OTP (Jika sudah diterima)</label>
                    <input type="text" name="otp" id="otp">
                </div>
                <button type="submit" class="btn">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
