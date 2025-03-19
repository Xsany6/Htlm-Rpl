<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

    // Memastikan file diupload dan formatnya PDF
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['type'] === 'application/pdf') {
        // Membersihkan nama file dari karakter yang tidak diizinkan
        $filename = preg_replace('/[^A-Za-z0-9\-\.\(\) ]/', '', $_FILES['pdf_file']['name']);
        $destination = 'uploads/' . $filename;

        // Pastikan folder uploads ada
        if (!file_exists('uploads')) {
            mkdir('uploads', 0755, true);
        }

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $destination)) {
            // Simpan informasi file ke database
            $query = $pdo->prepare("INSERT INTO files (category_id, filename) VALUES (:category_id, :filename)");
            $query->execute([
                'category_id' => $categoryId,
                'filename' => $filename
            ]);

            echo "File berhasil diupload.";
        } else {
            echo "Gagal mengupload file.";
        }
    } else {
        echo "Format file tidak didukung. Harap upload file PDF.";
    }
} else {
    echo "Akses tidak valid.";
}
