<?php
session_start();
include 'db.php';

// Cek apakah pengguna sudah login
$isLoggedIn = isset($_SESSION['user_id']);

// Jika pengguna tidak login, arahkan ke halaman loginKT.php
if (!$isLoggedIn) {
    header("Location: ./loginKT.php");
    exit;
}

// Mengambil daftar kategori dari database
$query = $pdo->query("SELECT * FROM categories");
$categories = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Kategori</title>
    <link rel="stylesheet" href="./assets/style copy.css">
</head>
<body>
    <h1>Pilih Kategori</h1>
    <div class="category-grid">
        <?php foreach ($categories as $category): ?>
            <div class="category-card" style="background-image: url('assets/default-bg.jpg');">
                <a href="category.php?id=<?php echo $category['id']; ?>" class="category-link">
                    <div class="overlay">
                        <h2><?php echo htmlspecialchars($category['name']); ?></h2>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
