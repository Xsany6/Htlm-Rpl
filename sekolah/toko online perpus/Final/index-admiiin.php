<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek login
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

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
    $name = $conn->real_escape_string($_POST['product_name']);
    $price = floatval($_POST['price']);
    $stok = intval($_POST['stok']);
    $conn->query("INSERT INTO products (name, price, stok) VALUES ('$name', '$price', '$stok')");
}

// Ambil daftar user
$user_list = $conn->query("SELECT id, username, email, role FROM users");

// Ambil daftar produk
$product_list = $conn->query("SELECT * FROM products");

// Proses hapus user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    $conn->query("DELETE FROM users WHERE id = $user_id");
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Debugging: Cek data admin
        // var_dump($user); exit(); 
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Cek apakah admin
            if ($user['role'] === 'admin') {
                $_SESSION['is_admin'] = true;
            } else {
                $_SESSION['is_admin'] = false;
            }

            header("Location: index.php");
            exit();
        } else {
            $login_error = "Password salah!";
        }
    } else {
        $login_error = "Username tidak ditemukan!";
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    
    // Direktori penyimpanan file
    $image_dir = "../uploads/images/uploads/images/";
    $pdf_dir = "../uploads/pdf/";
    
    // Menyimpan file gambar
    if (!empty($_FILES['product_image']['name'])) {
        $image_file = $image_dir . basename($_FILES['product_image']['name']);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $image_file);
    } else {
        $image_file = "";
    }
    
    // Menyimpan file PDF
    if (!empty($_FILES['product_pdf']['name'])) {
        $pdf_file = $pdf_dir . basename($_FILES['product_pdf']['name']);
        move_uploaded_file($_FILES['product_pdf']['tmp_name'], $pdf_file);
    } else {
        $pdf_file = "";
    }
    
    // Menyimpan ke database
    $sql = "INSERT INTO products (name, description, price, image, pdf) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdss", $product_name, $product_description, $product_price, $image_file, $pdf_file);
    
    if ($stmt->execute()) {
        echo "Produk berhasil ditambahkan!";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
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

    <!-- Navbar -->
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


        <!-- Tambah Produk -->
       <!-- Tambah Produk -->
<h3>Add Product</h3>
<form action="add_product.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input type="number" name="price" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="stok" class="form-label">Stok</label>
        <input type="number" name="stok" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="deskripsi" class="form-label">Deskripsi Produk</label>
        <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Upload Gambar</label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png" required>
    </div>
    <div class="mb-3">
        <label for="pdf" class="form-label">Upload PDF</label>
        <input type="file" name="pdf" class="form-control" accept=".pdf" required>
    </div>
    <button type="submit" name="add_product" class="btn btn-primary">Tambah Produk</button>
</form>


        <!-- List Produk -->
        <!-- List Produk -->
<h3 class="mt-4">Product List</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>PDF</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($product = $product_list->fetch_assoc()): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= number_format($product['price'], 2) ?></td>
                <td><?= $product['stok'] ?></td>
                <td>
                    <?php if (!empty($product['pdf_file'])): ?>
                        <a href="../uploads/pdf/<?= htmlspecialchars($product['pdf_file']) ?>" target="_blank">View PDF</a>
                    <?php else: ?>
                        No PDF
                    <?php endif; ?>
                </td>
                <td>
                    <!-- Tombol Edit Produk -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $product['id'] ?>">Edit</button>

                    <!-- Tombol Hapus Produk -->
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="delete_product" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</button>
                    </form>
                </td>
            </tr>

            <!-- Modal Edit Produk -->
            <div class="modal fade" id="editProductModal<?= $product['id'] ?>" tabindex="-1" aria-labelledby="editProductLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProductLabel">Edit Produk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Harga</label>
                                    <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="stok" class="form-control" value="<?= $product['stok'] ?>" required>
                                </div>
                                <button type="submit" name="edit_product" class="btn btn-primary">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    </tbody>
</table>

    </div>

<?php endif; ?>
        <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
