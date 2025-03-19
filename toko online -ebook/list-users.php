<?php
// Koneksi ke database
require 'db/config.php';



// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data pengguna yang pernah login
$query = "SELECT id, username, email, role, last_login FROM users WHERE last_login IS NOT NULL ORDER BY last_login DESC";
$result = $conn->query($query);

// Ambil daftar user
$user_list = $conn->query("SELECT id, username, email, role FROM users");

// Ambil daftar produk
$product_list = $conn->query("SELECT * FROM products");

// Proses hapus user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id'] ?? 0);
    if ($user_id > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('User berhasil dihapus!'); window.location.href='index-admiiin.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus user!');</script>";
        }
        $stmt->close();
    }
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$username || !$password) {
        $login_error = "Username dan password harus diisi!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['is_admin'] = ($user['role'] === 'admin');

                header("Location: index-admiiin.php");
                exit();
            } else {
                $login_error = "Password salah!";
            }
        } else {
            $login_error = "Username tidak ditemukan!";
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna yang Pernah Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
   <div class="container mt-3">
            <h3>Daftar Pengguna yang Pernah Login</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($user = $user_list->fetch_assoc()): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td>
                <?php if ($user['role'] !== 'admin'): ?> <!-- Hanya non-admin yang bisa dihapus -->
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus</button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled>Admin Tidak Dapat Dihapus</button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>

                </tbody>
            </table>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
