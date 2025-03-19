<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../db/config.php';

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $captcha_response = $_POST['g-recaptcha-response'];
    $message = '';

    // Validasi reCAPTCHA
    $secretKey = '6Ley17YqAAAAAHJ0VcEYuVJtIMEVw-0QQlwBjz93'; // Ganti dengan kunci rahasia reCAPTCHA Anda
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha_response");
    $responseKeys = json_decode($response, true);

    if ($responseKeys['success']) {
        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Pastikan kolom points sudah ada di database
            $stmt = $conn->prepare("INSERT INTO users (username, password, points) VALUES (?, ?, 0)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $message = "Username sudah digunakan.";
            }
        } else {
            $message = "Password dan konfirmasi password tidak sama.";
        }
    } else {
        $message = "Verifikasi reCAPTCHA gagal. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
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
        .btn-primary {
            background: linear-gradient(90deg, #007bff, #0056b3);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #0056b3, #003f87);
        }
        .form-control:focus {
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="card p-4">
        <h2 class="text-center mb-4">Register</h2>
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
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="g-recaptcha mb-3" data-sitekey="6Ley17YqAAAAAKDPGnal65mjGQtBQc4cnuoXnyTS"></div> <!-- Ganti dengan kunci situs Anda -->
            <button type="submit" class="btn btn-success w-100">Daftar</button>
        </form>
        <p class="text-center mt-3">Sudah punya akun? <a href="loginKT.php">Login</a></p>
    </div>
</body>
</html>
