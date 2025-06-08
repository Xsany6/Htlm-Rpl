<?php
// Konfigurasi database
$host = 'localhost';
$dbname = 'library_db';
$username = 'root';
$password = '';

try {
    // Koneksi ke database menggunakan PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Proses jika metode HTTP adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

    // Upload file PDF
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['type'] === 'application/pdf') {
        $pdfFilename = preg_replace('/[^A-Za-z0-9\-\.\(\) ]/', '', $_FILES['pdf_file']['name']);
        $pdfDestination = 'uploads/pdf/' . $pdfFilename;

        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdfDestination)) {
            $query = $pdo->prepare("INSERT INTO files (category_id, filename) VALUES (:category_id, :filename)");
            $query->execute(['category_id' => $categoryId, 'filename' => $pdfFilename]);
            echo "File PDF berhasil diupload.<br>";
        } else {
            echo "Gagal mengunggah file PDF.<br>";
        }
    }

    // Upload gambar latar
    if (isset($_FILES['background_image'])) {
        $imageFileType = strtolower(pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageFilename = 'bg_' . time() . '.' . $imageFileType;
            $imageDestination = 'uploads/images/' . $imageFilename;

            if (move_uploaded_file($_FILES['background_image']['tmp_name'], $imageDestination)) {
                $query = $pdo->prepare("UPDATE categories SET background_image = :background_image WHERE id = :id");
                $query->execute(['background_image' => $imageFilename, 'id' => $categoryId]);
                echo "Gambar latar berhasil diupload.<br>";
            } else {
                echo "Gagal mengunggah gambar latar.<br>";
            }
        } else {
            echo "Format gambar tidak valid.<br>";
        }
    }
} else {
    echo "Akses tidak valid.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Upload File</h1>
        <form method="post" enctype="multipart/form-data" class="mt-4">
            <div class="mb-3">
                <label for="category_id" class="form-label">Kategori</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <?php
                    // Ambil kategori dari database
                    $categories = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($categories as $category) {
                        echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="pdf_file" class="form-label">Upload PDF</label>
                <input type="file" id="pdf_file" name="pdf_file" class="form-control" accept="application/pdf" required>
            </div>
            <div class="mb-3">
                <label for="background_image" class="form-label">Upload Gambar Latar</label>
                <input type="file" id="background_image" name="background_image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>
</html>
