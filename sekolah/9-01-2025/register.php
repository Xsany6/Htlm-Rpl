<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db/config.php';

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $message = '';

    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .btn-success {
            background: linear-gradient(90deg, #28a745, #218838);
            border: none;
        }
        .btn-success:hover {
            background: linear-gradient(90deg, #218838, #1e7e34);
        }
        .form-control:focus {
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
            border-color: #28a745;
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
                <label for="username" class="form-label">Username</label>
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
            <button type="submit" class="btn btn-success w-100">Daftar</button>
        </form>
        <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
