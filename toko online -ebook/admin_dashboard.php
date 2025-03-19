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

// Cek login admin
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username == 'admin' && $password == 'admin') {
        $_SESSION['admin'] = true;
    } else {
        $error = "Username atau Password salah!";
    }
}

// Proses logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_dashboard.php");
    exit();
}

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    echo '<form method="POST">
            <h2>Login Admin</h2>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>';
    exit();
}

// Proses update stok barang
if (isset($_POST['update_stok'])) {
    $product_id = $_POST['product_id'];
    $stok_tambah = $_POST['stok_tambah'];

    $query = "UPDATE products SET stok = stok + ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $stok_tambah, $product_id);
    if ($stmt->execute()) {
        $success = "Stok berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui stok!";
    }
}

// Proses tambah produk baru
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stok = $_POST['stok'];

    $image = $_FILES['image'];
    $pdf = $_FILES['pdf'];

    // Path folder upload
    $uploadDir = 'uploads/images/';
    $pdfDir = 'uploads/pdf/';

    // Pastikan folder sudah ada, jika belum buat
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0777, true);
    }

    // Proses unggah gambar
    $imageName = NULL;
    if (isset($image) && $image['error'] == 0) {
        $allowedImageTypes = ['jpg', 'jpeg', 'png'];
        $imageExt = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

        if (in_array($imageExt, $allowedImageTypes)) {
            $imageName = time() . "_" . basename($image['name']); // Nama unik
            move_uploaded_file($image['tmp_name'], $uploadDir . $imageName);
        } else {
            $error = "Format gambar tidak valid! Hanya JPG, JPEG, PNG yang diperbolehkan.";
        }
    }

    // Proses unggah PDF
    $pdfName = NULL;
    if (isset($pdf) && $pdf['error'] == 0) {
        $pdfExt = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));

        if ($pdfExt == 'pdf') {
            $pdfName = time() . "_" . basename($pdf['name']); // Nama unik
            move_uploaded_file($pdf['tmp_name'], $pdfDir . $pdfName);
        } else {
            $error = "Format file tidak valid! Hanya file PDF yang diperbolehkan.";
        }
    }

    // Simpan data ke database jika tidak ada error
    if (!isset($error)) {
        $query = "INSERT INTO products (name, price, stok, image, pdf_file) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdiss", $name, $price, $stok, $imageName, $pdfName);

        if ($stmt->execute()) {
            $success = "Produk berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan produk!";
        }
    }
}

// Ambil data produk
$products = $conn->query("SELECT * FROM products");
// Ambil data user
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <a href="?logout=true" class="btn btn-danger">Logout</a>
        
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <h2 class="mt-4">Tambah Produk Baru</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Gambar Produk (JPG, JPEG, PNG)</label>
                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload File PDF</label>
                <input type="file" name="pdf" class="form-control" accept=".pdf" required>
            </div>
            <button type="submit" name="add_product" class="btn btn-success">Tambah Produk</button>
        </form>

        <h2 class="mt-4">Manajemen Stok Barang</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                    <th>Tambah Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $products->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['stok'] ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                <input type="number" name="stok_tambah" min="1" required>
                                <button type="submit" name="update_stok" class="btn btn-primary">Tambah</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
