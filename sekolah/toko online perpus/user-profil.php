<?php
session_start();
$conn = new mysqli("localhost", "root", "", "library_db");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Silakan login terlebih dahulu.");
}

$user_id = $_SESSION['user_id'];
$message = "";

// Ambil data user dari database
$sql = "SELECT username, email, phone, profile_pic, gender, birth_date, otp_code FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$email = $user['email'] ?: '-';
$otp_code = $user['otp_code'];
$verified = empty($otp_code);

// Kirim OTP jika tombol ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_otp'])) {
    if (!empty($user['email'])) {
        $otp = rand(100000, 999999);
        $update_sql = "UPDATE users SET otp_code = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $otp, $user_id);
        $update_stmt->execute();

        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';
        require 'PHPMailer/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ff29042028@gmail.com';
            $mail->Password = 'ijgl ywsb lbzt mndw';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'Library System');
            $mail->addAddress($user['email']);
            $mail->Subject = 'Kode Verifikasi Email';
            $mail->Body = "Kode OTP Anda: $otp";

            if ($mail->send()) {
                $message = "OTP telah dikirim ke email Anda.";
            } else {
                $message = "Gagal mengirim OTP.";
            }
        } catch (Exception $e) {
            $message = "Error: " . $mail->ErrorInfo;
        }
    } else {
        $message = "Email tidak ditemukan. Harap isi email terlebih dahulu.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .navbar {
        transition: all 0.3s ease;
        background-color: #343a40 !important;
    }

    .navbar-brand,
    .nav-link {
        color: #ffffff !important;
    }

    .navbar.shrink {
        padding-top: 5px;
        padding-bottom: 5px;
        background-color: #212529 !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .container.profile-container {
        max-width: 600px;
        background: whitesmoke;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
        margin-bottom: 80px;
        /* Memberi jarak dari footer */
    }

    .profile-pic {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: block;
        margin: auto;
        object-fit: cover;
    }

    body {
        background-color: #1e1e1e;
        color: #f1f1f1;
    }

    .table th {
        width: 40%;
    }

    footer {
        background: white;
        padding: 20px;
        padding-top: 20px;
    }

    .footer-container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .footer-section {
        width: 15%;
        color: black;
    }

    .footer-section h4 {
        font-size: 26px;
        margin-bottom: 5px;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li {
        margin-bottom: 5px;
        color: black;
        display: flex;
    }

    .payment-methods img {
        width: 50px;
        margin-right: 5px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Xstore</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav d-flex flex-row gap-3 mb-0 list-unstyled">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart"></i> Cart
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container profile-container">
        <h2 class="text-center">Profil Saya</h2>
        <hr>

        <div class="text-center">
            <img src="<?= htmlspecialchars($user['profile_pic'] ?: 'https://via.placeholder.com/120') ?>"
                alt="Foto Profil" class="profile-pic" id="previewImage">
            <input type="file" name="profile_pic" class="form-control mt-2" id="fileInput">
        </div>

        <table class="table mt-3">
            <tr>
                <th>Username</th>
                <td><?= htmlspecialchars($user['username']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td>
                    <?= htmlspecialchars($email) ?>
                    <?php if (!$verified): ?>
                    <a href="verify-otp.php" class="btn btn-warning btn-sm">Verifikasi Email</a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Nomor Telepon</th>
                <td><?= htmlspecialchars($user['phone'] ?: '-') ?></td>
            </tr>
            <tr>
                <th>Jenis Kelamin</th>
                <td><?= htmlspecialchars($user['gender'] ?: '-') ?></td>
            </tr>
            <tr>
                <th>Tanggal Lahir</th>
                <td><?= htmlspecialchars($user['birth_date'] ?: '-') ?></td>
            </tr>
        </table>

        <div class="text-center">
            <a href="profiluser.php" class="btn btn-primary">Edit Profil</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#fileInput").change(function() {
            let reader = new FileReader();
            reader.onload = function(e) {
                $("#previewImage").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('shrink');
        } else {
            navbar.classList.remove('shrink');
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h4>Layanan Pelanggan</h4>
            <ul>
                <li>Bantuan</li>
                <li>Metode Pembayaran</li>
                <li>ShopeePay</li>
                <li>Koin Shopee</li>
                <li>Lacak Pesanan Pembeli</li>
                <li>Lacak Pengiriman Penjual</li>
                <li>Gratis Ongkir</li>
                <li>Pengembalian Barang & Dana</li>
                <li>Garansi Shopee</li>
                <li>Hubungi Kami</li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Jelajahi Xstore</h4>
            <ul>
                <li>Tentang Kami</li>
                <li>Karir</li>
                <li>Kebijakan Shopee</li>
                <li>Kebijakan Privasi</li>
                <li>Blog</li>
                <li>Shopee Mall</li>
                <li>Seller Centre</li>
                <li>Flash Sale</li>
                <li>Kontak Media</li>
                <li>Shopee Affiliate</li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Pembayaran</h4>
            <div class="payment-methods">
                <img src="images  payout/images.png" alt="BCA">
                <img src="images  payout/images.jpg" alt="BNI">
                <img src="images  payout/images (1).png" alt="BRI">
                <img src="images  payout/images (2).png" alt="Visa">
            </div>
        </div>
        <div class="footer-section">
            <h4>Ikuti Kami</h4>
            <ul>
                <li>Facebook</li>
                <li>Instagram</li>
                <li>Twitter</li>
                <li>LinkedIn</li>
            </ul>
        </div>
    </div>
</footer>

</html>