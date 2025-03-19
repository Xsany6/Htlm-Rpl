<?php
session_start();
require 'db/config.php'; // File koneksi database

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Pastikan PHPMailer telah diinstal

$conn = new mysqli("localhost", "root", "", "library_db"); // Sesuaikan dengan database Anda

// Kirim OTP ke email pengguna
if (isset($_POST['send_otp'])) {
    $email = trim($_POST['email']);
    if (empty($email)) {
        echo "Email tidak boleh kosong.";
        exit;
    }
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ff29042028@gmail.com'; // Sesuaikan
            $mail->Password = 'ijgl ywsb lbzt mndw'; // Password aplikasi email
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('your-email@gmail.com', 'Your Website');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "Your OTP code is: $otp";
            $mail->send();

            // Update kode OTP dan waktu kadaluarsa di database
            $update_stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE email = ?");
            $update_stmt->bind_param("is", $otp, $email);
            $update_stmt->execute();
            $_SESSION['otp_sent'] = true;
            echo "OTP telah dikirim ke email Anda.";
        } catch (Exception $e) {
            echo "Gagal mengirim OTP. Kesalahan: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email tidak ditemukan.";
    }
    $stmt->close();
}

// Verifikasi OTP
if (isset($_POST['verify_otp'])) {
    if (!isset($_SESSION['email'])) {
        echo "Sesi kedaluwarsa. Silakan minta OTP lagi.";
        exit;
    }
    $email = $_SESSION['email'];
    $otp = trim($_POST['otp']);
    if (empty($otp)) {
        echo "OTP tidak boleh kosong.";
        exit;
    }
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND otp_code = ? AND otp_expiry > NOW()");
    $stmt->bind_param("si", $email, $otp);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['verified'] = true;
        $_SESSION['otp_verified'] = true;
        echo "OTP terverifikasi. Anda bisa mengganti kata sandi.";
    } else {
        echo "OTP tidak valid atau telah kedaluwarsa.";
    }
    $stmt->close();
}

// Reset Password
if (isset($_POST['reset_password'])) {
    if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
        echo "Permintaan tidak sah.";
        exit;
    }
    $email = $_SESSION['email'];
    $new_password = password_hash(trim($_POST['new_password']), PASSWORD_DEFAULT);
    if (empty($_POST['new_password'])) {
        echo "Kata sandi tidak boleh kosong.";
        exit;
    }
    $stmt = $conn->prepare("UPDATE users SET password = ?, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);
    $stmt->execute();

    // Hapus sesi setelah reset password berhasil
    session_destroy();
    
    // Redirect ke halaman login
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: url('images.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .container-box {
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: auto;
        margin-top: 100px;
        
        /* Animasi awal */
        opacity: 0;
        transform: translateY(50px);
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    </style>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Tambahkan animasi setelah halaman selesai dimuat
        document.querySelector(".container-box").style.opacity = "1";
        document.querySelector(".container-box").style.transform = "translateY(0)";
    });
</script>

</head>
<body>
    <div class="container-box text-center">
        <h3>Reset Password</h3>
        
        <!-- Notifikasi -->
        <?php if (!empty($message)): ?>
            <div><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="send_otp">Kirim OTP</button>
        </form>
        <?php if (isset($_SESSION['otp_sent'])): ?>
        <form method="post" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Masukkan OTP:</label>
                <input type="text" class="form-control" name="otp" value="<?php echo isset($_POST['otp']) ? $_POST['otp'] : ''; ?>" required>
            </div>
            <button type="submit" class="btn btn-success" name="verify_otp">Verifikasi OTP</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Popup Modal Reset Password -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Kata Sandi Baru:</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="reset_password">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".container-box").style.opacity = "1";
        document.querySelector(".container-box").style.transform = "translateY(0)";
        
        let showOtpForm = <?php echo isset($_SESSION['otp_sent']) ? 'true' : 'false'; ?>;
        if (showOtpForm) {
            document.querySelector(".otp-form").classList.remove("hidden");
        }

        let showModal = <?php echo isset($_SESSION['otp_verified']) ? 'true' : 'false'; ?>;
        if (showModal) {
            let modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            modal.show();
        }
    });
    </script>
</body>
</html>
