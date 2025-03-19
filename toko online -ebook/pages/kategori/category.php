<?php
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Pastikan ID kategori valid
if ($id <= 0) {
    echo "ID kategori tidak valid.";
    exit;
}

// Ambil data kategori berdasarkan ID
$query = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
$query->execute(['id' => $id]);
$category = $query->fetch(PDO::FETCH_ASSOC);

// Cek apakah kategori ditemukan
if (!$category) {
    echo "Kategori tidak ditemukan.";
    exit;
}

// Ambil file yang terkait dengan kategori ini
$fileQuery = $pdo->prepare("SELECT * FROM files WHERE category_id = :category_id");
$fileQuery->execute(['category_id' => $id]);
$files = $fileQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori - <?php echo htmlspecialchars($category['name']); ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* Default (desktop) styling for PDF preview */
        .book-item embed {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 50px;
            width: 249px;   /* Lebar untuk desktop */
            height: 240px;  /* Tinggi untuk desktop */
        }

        /* Styling for mobile devices */
        @media (max-width: 768px) {
            .book-item embed {
                width: 415px;   /* Lebar untuk mobile */
                height: 185px;  /* Tinggi untuk mobile */
            }
        }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($category['name']); ?></h1>
    <p><?php echo htmlspecialchars($category['description']); ?></p>

    <!-- Form untuk upload file PDF -->
    <h2>Upload File PDF</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
        <input type="file" name="pdf_file" accept="application/pdf" required>
        <button type="submit">Upload</button>
    </form>

    <!-- Daftar file PDF yang sudah diupload -->
    <h2>Daftar File PDF</h2>
    <?php if (isset($files) && count($files) > 0): ?>
        <ul>
            <?php foreach ($files as $file): ?>
                <li>
                    <a href="uploads/<?php echo htmlspecialchars($file['filename']); ?>" target="_blank">
                        <?php echo htmlspecialchars($file['filename']); ?>
                    </a>
                    - Uploaded on <?php echo $file['upload_date']; ?>

                    <!-- Tombol Hapus -->
                    <a href="delete.php?id=<?php echo $file['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?');">Hapus</a>
                    
                    <!-- Menampilkan File PDF di Halaman -->
                    <h3>Preview File PDF</h3>
                    <embed src="uploads/<?php echo htmlspecialchars($file['filename']); ?>" type="application/pdf">
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Tidak ada file PDF yang diupload untuk kategori ini.</p>
    <?php endif; ?>

    <a href="index.php">Kembali ke Daftar Kategori</a>
</body>
</html>
