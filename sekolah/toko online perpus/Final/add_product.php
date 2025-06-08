<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stok = $_POST['stok'];

    $image = $_FILES['image'];
    $pdf = $_FILES['pdf'];

    // Path folder upload
    $uploadDir = '../uploads/images/uploads/images/';
    $pdfDir = '../uploads/pdf/';

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
<html>
<head>
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Tambah Produk</h1>
        <a href="./Final/admin_dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>

        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

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
    </div>
</body>
</html>
