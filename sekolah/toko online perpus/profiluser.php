<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Pastikan PHPMailer terinstal

$conn = new mysqli("localhost", "root", "", "library_db");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Silakan login terlebih dahulu.");
}

$user_id = $_SESSION['user_id'];
$message = "";

// **Ambil data user**
$sql = "SELECT username, email, phone, profile_pic, gender, birth_date, otp_code FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $profile_pic = $user['profile_pic'];

    // **Perbarui email hanya jika ada perubahan**
    $email = isset($_POST['email']) ? trim($_POST['email']) : $user['email'];
    if ($email !== $user['email']) {
        $otp = rand(100000, 999999);
        $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Simpan OTP di database
        $update_email_sql = "UPDATE users SET email = ?, otp_code = ?, otp_expiry = ? WHERE id = ?";
        $update_email_stmt = $conn->prepare($update_email_sql);
        $update_email_stmt->bind_param("sssi", $email, $otp, $otp_expiry, $user_id);
        $update_email_stmt->execute();

        // **Kirim OTP ke email baru**
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ff29042028@gmail.com';
            $mail->Password = 'ijgl ywsb lbzt mndw'; // Gunakan App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'Library System');
            $mail->addAddress($email);
            $mail->Subject = 'Kode Verifikasi Email';
            $mail->Body = "Kode OTP Anda: $otp\nGunakan kode ini untuk verifikasi email.";

            if ($mail->send()) {
                $message = "Kode OTP telah dikirim ke email baru Anda.";
            } else {
                $message = "Gagal mengirim OTP.";
            }
        } catch (Exception $e) {
            $message = "Error: " . $mail->ErrorInfo;
        }
    }

    // **Upload Foto Profil**
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "foto-user/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
            $profile_pic = $target_file;
        } else {
            $message = "Format gambar tidak valid (hanya JPG, JPEG, PNG, GIF).";
        }
    }

    // **Update Data Profil**
    if (empty($message)) {
        $sql = "UPDATE users SET username = ?, phone = ?, profile_pic = ?, gender = ?, birth_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $username, $phone, $profile_pic, $gender, $birth_date, $user_id);

        if ($stmt->execute()) {
            header("Location: user-profil.php");
            exit();
        } else {
            $message = "Terjadi kesalahan saat memperbarui profil.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #1e1e1e;
        /* Warna latar belakang gelap */
        color: #f1f1f1;
        /* Warna teks putih */
    }

    .container {
        max-width: 600px;
        background: whitesmoke;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.1);
        margin-top: 50px;
        margin-bottom: 50px;
        /* Tambahkan ini untuk memberi jarak ke footer */
        color: #1e1e1e;
    }


    .profile-pic {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ff5722;
        display: block;
        margin: 0 auto;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center">Profil Saya</h2>
        <div id="message" class="alert text-center" style="display: none;"></div>

        <form id="profileForm" method="POST" enctype="multipart/form-data">
            <div class="mb-3 text-center">
                <img src="<?= !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'https://via.placeholder.com/120' ?>"
                    alt="Foto Profil" id="previewImage" class="profile-pic">
            </div>
            <div class="mb-3">
                <label class="form-label">Nama:</label>
                <input type="text" name="username" class="form-control"
                    value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nomor HP:</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Email:</label>
                <?php if (!empty($user['email'])): ?>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                <?php else: ?>
                <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda"
                    required>
                <button type="button" id="sendOtp" class="btn btn-warning mt-2">Kirim OTP</button>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Email:</label>
                <?php if (!empty($user['email'])): ?>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                <?php else: ?>
                <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda"
                    required>
                <button type="button" id="sendOtp" class="btn btn-warning mt-2">Kirim OTP</button>
                <?php endif; ?>
            </div>

            <div id="otpSection" class="mb-3" style="display: none;">
                <label class="form-label">Masukkan OTP:</label>
                <input type="text" id="otpCode" name="otp" class="form-control" placeholder="Masukkan kode OTP">
                <button type="button" id="verifyOtp" class="btn btn-success mt-2">Verifikasi OTP</button>
            </div>

            <div id="message" class="alert text-center" style="display: none;"></div>


            <div class="mb-3">
                <label class="form-label">Jenis Kelamin:</label>
                <div>
                    <input type="radio" name="gender" value="Laki-laki"
                        <?= $user['gender'] == 'Laki-laki' ? 'checked' : '' ?>> Laki-laki
                    <input type="radio" name="gender" value="Perempuan"
                        <?= $user['gender'] == 'Perempuan' ? 'checked' : '' ?>> Perempuan
                    <input type="radio" name="gender" value="Lainnya"
                        <?= $user['gender'] == 'Lainnya' ? 'checked' : '' ?>> Lainnya
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Lahir:</label>
                <input type="date" name="birth_date" class="form-control"
                    value="<?= htmlspecialchars($user['birth_date']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Foto Profil:</label>
                <input type="file" name="profile_pic" class="form-control" id="fileInput">
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Preview gambar sebelum upload
        $("#fileInput").change(function() {
            let reader = new FileReader();
            reader.onload = function(e) {
                $("#previewImage").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });

        // Kirim OTP
        $("#sendOtp").click(function() {
            let email = $("#email").val();
            if (email === "") {
                alert("Masukkan email terlebih dahulu!");
                return;
            }

            $.ajax({
                url: "send_otp.php",
                type: "POST",
                data: {
                    email: email
                },
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $("#otpSection").show();
                        $("#message").removeClass("alert-danger").addClass("alert-success")
                            .text(data.message).show();
                    } else {
                        $("#message").removeClass("alert-success").addClass("alert-danger")
                            .text(data.message).show();
                    }
                }
            });
        });

        // Verifikasi OTP
        $("#verifyOtp").click(function() {
            let email = $("#email").val();
            let otp = $("#otpCode").val();

            if (otp === "") {
                alert("Masukkan kode OTP!");
                return;
            }

            $.ajax({
                url: "verify_otp.php",
                type: "POST",
                data: {
                    email: email,
                    otp: otp
                },
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $("#message").removeClass("alert-danger").addClass("alert-success")
                            .text(data.message).show();
                        $("#email").prop("disabled", true);
                        $("#otpSection").hide();
                    } else {
                        $("#message").removeClass("alert-success").addClass("alert-danger")
                            .text(data.message).show();
                    }
                }
            });
        });
    });
    </script>
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
<style>
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
</style>

</html>