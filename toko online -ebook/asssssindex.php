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
    $name = $_POST['product_name'] ?? null;
    $description = $_POST['product_description'] ?? null;
    $price = $_POST['product_price'] ?? null;
    $stok = $_POST['stok'] ?? 0;

    if (!$name || !$description || !$price) {
        die("Pastikan semua field telah diisi.");
    }

    // Direktori penyimpanan file
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_file = "";
    $pdf_file = "";

    // Menyimpan file gambar
    if (!empty($_FILES['product_image']['name'])) {
        $image_file = $upload_dir . basename($_FILES['product_image']['name']);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $image_file);
    }

    // Menyimpan file PDF
    if (!empty($_FILES['product_pdf']['name'])) {
        $pdf_file = $upload_dir . basename($_FILES['product_pdf']['name']);
        move_uploaded_file($_FILES['product_pdf']['tmp_name'], $pdf_file);
    }

    // Query menggunakan prepared statement
    $sql = "INSERT INTO products (name, description, price, stok, image, pdf) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiss", $name, $description, $price, $stok, $image_file, $pdf_file);

    if ($stmt->execute()) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location.href='asssssindex.php';</script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
}

// Ambil daftar user
$user_list = $conn->query("SELECT id, username, email, role FROM users");

// Ambil daftar produk
$product_list = $conn->query("SELECT * FROM products");
// Ambil jumlah total produk
$product_count_result = $conn->query("SELECT COUNT(*) AS total_products FROM products");
$total_products = $product_count_result->fetch_assoc()['total_products'] ?? 0;

// Proses hapus user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id'] ?? 0);
    if ($user_id > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('User berhasil dihapus!'); window.location.href='asssssindex.php';</script>";
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
            
                // UPDATE last_login saat login berhasil
                $update_login_time = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_login_time->bind_param("i", $user['id']);
                $update_login_time->execute();
                
                header("Location: index-admiiin.php");
                exit();
            }
            
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
            echo "<script>Swal.fire('Dihapus!', 'Produk telah berhasil dihapus.', 'success');</script>";
        } else {
            echo "<script>Swal.fire('Gagal!', 'Gagal menghapus produk!', 'error');</script>";
        }
        $stmt->close();
    }
}

// Ambil daftar pengguna yang sudah pernah login
$logged_in_users = $conn->query("SELECT username, last_login FROM users WHERE last_login IS NOT NULL ORDER BY last_login DESC");

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar {
            height: 100vh;
            background-color: #2a1e75;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #4c3fc7;
        }
        .sidebar .active {
            background-color: #ffffff;
            color: #2a1e75;
            border-radius: 10px;
        }
        /* Mengatur tampilan tombol dalam satu baris */
td .btn {
    margin: 2px; /* Beri jarak antar tombol */
    display: inline-block;
}

/* Mengatur agar tombol di dalam tabel lebih proporsional */
.table td {
    vertical-align: middle;
    text-align: center;
}

/* Jika tombol terlalu besar pada tampilan kecil */
@media (max-width: 768px) {
    td .btn {
        display: block; /* Susun tombol ke bawah pada layar kecil */
        width: 100%;
        margin-bottom: 5px;
    }
}

        
    </style>
    <!-- Tambahkan SweetAlert2 -->



</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="text-center mb-4">
                    <h4>📚 Library Book</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="id"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item">
                    <a class="nav-link" href="#AkunPelanggan"><i class="fas fa-users"></i> Customers</a>
                            <ul class="list-group">
                                <?php while ($user = $logged_in_users->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($user['username']) ?>
                                        <small class="text-muted"><?= date('d M Y H:i', strtotime($user['last_login'])) ?></small>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </li>


                    <li class="nav-item"><a class="nav-link" href="send_message.php"><i class="fas fa-comment"></i> Messages</a></li>
                    <li class="nav-item"><a class="nav-link" href="cs.php"><i class="fas fa-question-circle"></i> Help</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cog"></i> Add Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="daftar_pembelian.php"><i class="fas fa-lock"></i> Data Penjualan</a></li>
                    <li class="nav-item"><a class="nav-link" href="?logout=true"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
                </ul>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Admin Dashboard</h1>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-info text-white p-3">
                            <h4>Total Produk</h4>
                            <h2><?php echo $total_products; ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white p-3">
                            <h4>Total Users</h4>
                            <h2><?php echo $total_users; ?></h2>
                        </div>
                    </div>
                </div>
                  <!-- List Produk -->
            <!-- List Produk -->

            <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index-admiiin.php">Dashboard Pengguna</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Dropdown Akun Pelanggan -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="akunDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users"></i> Akun Pelanggan
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="akunDropdown">
                        <?php if ($logged_in_users->num_rows > 0): ?>
                            <?php while ($user = $logged_in_users->fetch_assoc()): ?>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <?= htmlspecialchars($user['username']) ?> 
                                        <small class="text-muted">(<?= date('d M Y H:i', strtotime($user['last_login'])) ?>)</small>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li><a class="dropdown-item text-muted" href="#">Belum ada pelanggan login</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Tombol Logout -->
                <li class="nav-item">
                    <a class="btn btn-danger text-white ms-2" href="?logout=true">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div id="AkunPelanggan">
    <ul class="list-group">
        <?php while ($user = $logged_in_users->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($user['username']) ?>
                <small class="text-muted"><?= date('d M Y H:i', strtotime($user['last_login'])) ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
</div>



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
                            <a href="./uploads/pdf/<?= htmlspecialchars($product['pdf_file']) ?>" target="_blank">View PDF</a>
                        <?php else: ?>
                            No PDF
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Tombol Edit Produk -->
                        <button class="btn btn-warning btn-sm" onclick="editProduct(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['price'] ?>, <?= $product['stok'] ?>)">Edit</button>

                        <!-- Tombol Hapus Produk -->
                        <form method="POST" id="delete-form-<?= $product['id'] ?>">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="delete_product" value="1">
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $product['id'] ?>)">Hapus</button>
                        </form>


                    </td>
                </tr>
<script>function confirmDelete(productId) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        document.getElementById('delete-form-' + productId).submit();
    }
}
</script>
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

            <script>
            function editProduct(id, name, price, stok) {
    Swal.fire({
        title: 'Edit Produk',
        html: `
            <form id="edit-form">
                <input type="hidden" name="product_id" value="${id}">
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" class="form-control" value="${name}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" class="form-control" value="${price}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="${stok}" required>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Simpan Perubahan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const form = document.getElementById('edit-form');
            const formData = new FormData(form);

            return fetch('edit_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire('Berhasil!', 'Produk berhasil diedit.', 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', 'Gagal mengedit produk.', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
            });
        }
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(productId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Produk yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        showClass: {
            popup: 'animate__animated animate__fadeInDown' // Animasi masuk
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp' // Animasi keluar
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                document.getElementById('delete-form-' + productId).submit();
            });
        }
    });
}
</script>
            <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>
    </html>
