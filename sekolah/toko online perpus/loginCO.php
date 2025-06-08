<?php
session_start();
require_once 'db/config.php'; // Koneksi database
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('572532228729-un5n6nq380h55qluuhelpmkrangu2u9c.apps.googleusercontent.com');
$client->setClientSecret('GOCSPX-ThKZAUEfp4l-7s9xK1YHD');
$client->setRedirectUri('http://localhost/html/projek-perpus/login.php');
$client->addScope("email");
$client->addScope("profile");

// Proses Login Google
if (isset($_GET['code'])) {
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
    if (!$stmt) die("Error SQL: " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Jika user baru, tambahkan ke database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, NULL)");
        if (!$stmt) die("Error SQL saat insert: " . $conn->error);
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

    header("Location: index.php");
    exit;
}

// Proses Login dengan Username dan Password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi reCAPTCHA
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = '6Ley17YqAAAAAHJ0VcEYuVJtIMEVw-0QQlwBjz93';
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse";
    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        $message = "Verifikasi reCAPTCHA gagal. Coba lagi.";
    } else {
        // Proses login biasa
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        }
    </style>
        <script>
                // Menghilangkan loading setelah halaman selesai dimuat
                window.onload = function() {
                    document.querySelector('.loading-screen').style.display = 'none';
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
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="g-recaptcha" data-sitekey="6Ley17YqAAAAAKDPGnal65mjGQtBQc4cnuoXnyTS"></div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
        </form>

        <hr>
        <?php if (isset($client)): ?>
            <a href="<?php echo $client->createAuthUrl(); ?>" class="btn btn-danger w-100 mt-2">Login with Google</a>
        <?php endif; ?>

        <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
    </div>
</body>
</html>
