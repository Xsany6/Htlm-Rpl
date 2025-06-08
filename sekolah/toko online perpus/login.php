<?php
session_start();
require_once 'db/config.php'; // Koneksi database
require_once 'vendor/autoload.php';

use League\OAuth2\Client\Provider\Github;


$githubProvider = new Github([
    'clientId'     => 'Ov23liNOTBh0jx1qEBnm',
    'clientSecret' => '5297d94ea99e4c364fb462276cf7bc1b',
    'redirectUri'  => 'http://localhost/html/projek-perpus/login.php?provider=github',
]);

// LOGIN GITHUB
if (isset($_GET['provider']) && $_GET['provider'] === 'github') {
    if (!isset($_GET['code'])) {
        // Generate URL login GitHub dengan state
        $authUrl = $githubProvider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $githubProvider->getState();
        header("Location: " . $authUrl);
        exit;
    } elseif (empty($_GET['state']) || $_GET['state'] !== $_SESSION['oauth2state']) {
        unset($_SESSION['oauth2state']);
        die('State tidak valid, ulangi login.');
    } else {
        // Mendapatkan token akses
        $token = $githubProvider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Mendapatkan data user dari GitHub
        $user = $githubProvider->getResourceOwner($token);
        $email = $user->getEmail() ?: $user->getNickname() . '@github.com';
        $name = $user->getName() ?: $user->getNickname();

        if (!isset($conn)) {
            die("Error: Koneksi database tidak ditemukan.");
        }

        // Cek apakah user sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Jika user baru, tambahkan ke database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, NULL)");
            $stmt->bind_param("ss", $name, $email);
            $stmt->execute();
        }

        // Ambil ID user dan set session
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        // Simpan email ke dalam cookie selama 7 hari
        setcookie("user_email", $email, time() + (7 * 24 * 60 * 60), "/");

        header("Location: index.php");
        exit;
    }
}

// LOGIN GOOGLE
$client = new Google_Client();
$client->setClientId('572532228729-un5n6nq380h55qluuhelpmkrangu2u9c.apps.googleusercontent.com');
$client->setClientSecret('GOCSPX-ThKZAUEfp4l-7s9xK1YHyED');
$client->setRedirectUri('http://localhost/html/projek-perpus/login.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code']) && !isset($_GET['provider'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        die("Error autentikasi: " . htmlspecialchars($token['error']));
    }

    $client->setAccessToken($token);
    $oauth = new Google_Service_Oauth2($client);
    $google_account_info = $oauth->userinfo->get();
    $email = $google_account_info->email;
    $name = $google_account_info->name;

    if (!isset($conn)) {
        die("Error: Koneksi database tidak ditemukan.");
    }

    // Cek apakah user sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Jika user baru, tambahkan ke database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, NULL)");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
    }

    // Ambil ID user dan set session
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Simpan email ke dalam cookie selama 7 hari
    setcookie("user_email", $email, time() + (7 * 24 * 60 * 60), "/");

    header("Location: index.php");
    exit;
}

// FORM LOGIN MANUAL
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = '6Ley17YqAAAAAHJ0VcEYuVJtIMEV-jz93';
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse";
    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        $message = "Verifikasi reCAPTCHA gagal. Coba lagi.";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];

                // Simpan email ke dalam cookie selama 7 hari
                setcookie("user_email", $user['email'], time() + (7 * 24 * 60 * 60), "/");

                header("Location: index.php");
                exit;
            } else {
                $message = "Password salah.";
            }
        } else {
            $message = "Username tidak ditemukan.";
        }
    }
}

// Inisialisasi session login_attempts jika belum ada
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query ke database untuk validasi login
    require 'db/config.php'; // Pastikan koneksi database sudah ada
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Jika user ditemukan
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Login sukses
            $_SESSION['login_attempts'] = 0; // Reset percobaan login
            header("Location: dashboard.php"); // Ganti dengan halaman setelah login
            exit();
        } else {
            // Jika password salah
            $_SESSION['login_attempts']++;
            $message = "Username atau password salah!";
        }
    } else {
        // Jika username tidak ditemukan
        $_SESSION['login_attempts']++;
        $message = "Username atau password salah!";
    }

    $stmt->close();
    $conn->close();
}

// Cek apakah opsi reset-password harus ditampilkan
$show_reset = ($_SESSION['login_attempts'] >= 3);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
    /* Loading Screen */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #1a1a2e;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-container {
        position: relative;
        width: 80px;
        height: 80px;
    }

    .line {
        position: absolute;
        width: 80px;
        height: 10px;
        background: linear-gradient(90deg, #ff9a9e, #fad0c4);
        border-radius: 5px;
        animation: rotateX 1.2s linear infinite;
    }

    .line:nth-child(2) {
        transform: rotate(90deg);
        animation: rotateY 1.2s linear infinite;
    }

    @keyframes rotateX {
        0%, 100% { transform: rotate(45deg); }
        50% { transform: rotate(-45deg); }
    }

    @keyframes rotateY {
        0%, 100% { transform: rotate(135deg); }
        50% { transform: rotate(45deg); }
    }

    body {
        background: url("images.jpg") no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .card {
        width: 100%;
        max-width: 400px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        background-color: rgba(255, 255, 255, 0.9);
        
        /* Animasi awal */
        opacity: 0;
        transform: translateY(50px);
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    </style>

    
<script>
    window.onload = function() {
        // Menghilangkan loading setelah halaman selesai dimuat
        document.querySelector('.loading-screen').style.display = 'none';

        // Tambahkan animasi ke .card setelah halaman selesai dimuat
        document.querySelector(".card").style.opacity = "1";
        document.querySelector(".card").style.transform = "translateY(0)";
    };
</script>
            
</head>

<body>
     <!-- Animasi Loading -->
     <div class="loading-screen">
        <div class="loading-container">
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </div>
    <div class="card p-4">
        <h2 class="text-center mb-4">Login</h2>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username/Email</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
                    <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" required>
                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                    <i class="bi bi-eye"></i> <!-- Ikon Mata -->
                </button>
            </div>
        </div>
                   

            <!-- Opsi Reset Password (Tersembunyi Jika Gagal Login < 3x) -->
            <p class="text-center mt-3 reset-password" style="display: none; opacity: 0; transition: opacity 0.5s ease;">
                <a href="reset_password.php">Lupa password? Klik di sini untuk mereset.</a>
            </p>


            <div class="g-recaptcha" data-sitekey="6Ley17YqAAAAAKDPGnal65mjGQtBQc4cnuoXnyTS"></div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
        </form>

        <hr>
        <?php if (isset($client)): ?>
            <a href="<?php echo $client->createAuthUrl(); ?>" class="btn btn-danger w-100 mt-2">Login with Google</a>
        <?php endif; ?>
        <a href="?provider=github" class="btn btn-dark w-100 mt-2">Login with GitHub</a>


        <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("togglePassword").addEventListener("click", function () {
            let passwordField = document.getElementById("password");
            let toggleIcon = this.querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("bi-eye");
                toggleIcon.classList.add("bi-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("bi-eye-slash");
                toggleIcon.classList.add("bi-eye");
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let showReset = <?php echo json_encode($show_reset); ?>;
        if (showReset) {
            let resetElement = document.querySelector(".reset-password");
            resetElement.style.display = "block"; // Munculkan elemen
            setTimeout(() => {
                resetElement.style.opacity = "1"; // Efek fade-in
            }, 100);
        }
    });
</script>


</body>

</html>
