<?php
include 'db.php';

if (isset($_GET['id'])) {
    $fileId = (int)$_GET['id'];

    // Ambil nama file dari database
    $query = $pdo->prepare("SELECT filename FROM files WHERE id = :id");
    $query->execute(['id' => $fileId]);
    $file = $query->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $filePath = 'uploads/' . $file['filename'];

        // Hapus file dari folder
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Hapus informasi file dari database
        $deleteQuery = $pdo->prepare("DELETE FROM files WHERE id = :id");
        $deleteQuery->execute(['id' => $fileId]);

        echo "File berhasil dihapus.";
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "ID file tidak ditemukan.";
}
