<?php
include 'db/config.php';

if (isset($_GET['file_id'])) {
    $fileId = (int)$_GET['file_id'];

    // Ambil nama file berdasarkan ID
    $query = $pdo->prepare("SELECT filename FROM files WHERE id = :id");
    $query->execute(['id' => $fileId]);
    $file = $query->fetch();

    if ($file) {
        $filePath = 'uploads/pdf/' . $file['filename'];

        // Hapus file dari server
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Hapus data file dari database
        $deleteQuery = $pdo->prepare("DELETE FROM files WHERE id = :id");
        $deleteQuery->execute(['id' => $fileId]);

        echo "File berhasil dihapus.";
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "ID file tidak valid.";
}
?>
