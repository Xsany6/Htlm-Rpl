<?php
session_start();
require_once 'db/config.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek login
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

// Proses logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Ambil data dashboard
$stock_result = $conn->query("SELECT SUM(stok) AS total_stock FROM products");
$stock = $stock_result->fetch_assoc()['total_stock'] ?? 0;

$user_result = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = $user_result->fetch_assoc()['total_users'] ?? 0;

// Proses tambah produk
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['product_description'] ?? '');
    $price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $stok = filter_var($_POST['stok'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);

    if (!$name || !$description || !$price || $stok === false) {
        die("Pastikan semua field diisi dengan benar.");
    }

    // Direktori penyimpanan file
    $image_dir = "uploads/images";
    $pdf_dir = "uploads/pdf";

    if (!is_dir($image_dir)) mkdir($image_dir, 0777, true);
    if (!is_dir($pdf_dir)) mkdir($pdf_dir, 0777, true);

    $image_file = "";
    $pdf_file = "";

    // Validasi & Simpan File Gambar
    if (!empty($_FILES['product_image']['name'])) {
        $image_type = mime_content_type($_FILES['product_image']['tmp_name']);
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($image_type, $allowed_image_types)) {
            die("Format gambar tidak valid! Gunakan JPG, PNG, atau GIF.");
        }

        $image_name = uniqid() . "_" . basename($_FILES['product_image']['name']);
        $image_path = $image_dir . "/" . $image_name;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $image_path)) {
            $image_file = $image_path;
        } else {
            die("Gagal mengunggah gambar.");
        }
    }

    // Validasi & Simpan File PDF
    if (!empty($_FILES['product_pdf']['name'])) {
        $pdf_type = mime_content_type($_FILES['product_pdf']['tmp_name']);

        if ($pdf_type !== 'application/pdf') {
            die("Hanya file PDF yang diperbolehkan!");
        }

        $pdf_name = time() . "_" . basename($_FILES['product_pdf']['name']);
        $pdf_file = $pdf_dir . "/" . $pdf_name;

        if (move_uploaded_file($_FILES['product_pdf']['tmp_name'], $pdf_file)) {
            $pdf_file = $pdf_dir . "/" . $pdf_name;
        } else {
            die("Gagal mengunggah file PDF.");
        }
    }

    // Simpan ke database dengan prepared statement
    $sql = "INSERT INTO products (name, description, price, stok, image, pdf_file) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiss", $name, $description, $price, $stok, $image_file, $pdf_file);

    if ($stmt->execute()) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location.href='index-admiiin.php';</script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
}

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

// Proses hapus produk
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id'] ?? 0);
    if ($product_id > 0) {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            echo "<script>alert('Produk berhasil dihapus!'); window.location.href='index-admiiin.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus produk!');</script>";
        }
        $stmt->close();
    }
}

$conn->close();
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <!-- Tambahkan ini di dalam <head> -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <!-- Bootstrap CSS -->

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>

    <?php if (!$is_logged_in): ?>
        <!-- Login Form -->
        <div class="container mt-5">
            <h2 class="text-center">Login</h2>
            <?php if (isset($login_error)) echo "<p class='text-danger text-center'>$login_error</p>"; ?>
            <form method="POST" class="w-50 mx-auto">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    <?php else: ?>


        <div class="container mt-3">
            <h2>Dashboard</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-primary">Total Stock: <?= $stock ?></div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success">Total Users: <?= $total_users ?></div>
                </div>
            </div>

            <!-- User Management (Hanya Admin) -->
            <?php if ($is_logged_in): ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Library System</a>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?logout=true">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

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
            <?php if ($user['role'] !== 'admin'): ?>
        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteUser(<?= $user['id'] ?>)">Hapus</button>

        <!-- Form hapus (disembunyikan, akan dikirim via JS) -->
        <form id="delete-form-<?= $user['id'] ?>" method="POST">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <input type="hidden" name="delete_user" value="true">
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
    <?php else: ?>
        <div class="container mt-5">
            <h2 class="text-center">Silakan Login</h2>
            <form method="POST" class="w-50 mx-auto">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    <?php endif; ?>

    <script>
function confirmDeleteUser(userId) {
    Swal.fire({
        title: "Yakin ingin menghapus akun ini?",
        text: "Aksi ini tidak dapat dibatalkan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + userId).submit();
        }
    });
}
</script>
   

    <?php endif; ?>
            <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>
    </html>
