<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil produk yang telah dibeli oleh user
$query = "SELECT DISTINCT p.id, p.name, p.image, p.price 
          FROM orders o
          INNER JOIN order_items oi ON o.id = oi.order_id
          INNER JOIN products p ON oi.product_id = p.id
          WHERE o.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Ambil produk favorit dari cookies
$favorites = isset($_COOKIE['favorite_products']) ? json_decode($_COOKIE['favorite_products'], true) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rating Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f5f5f5;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background: white;
            transition: 0.3s;
        }
        .product-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            width: 100%;
            height: auto;
        }
        .product-card .card-body {
            padding: 15px;
        }
        .btn-orange {
            background-color: #ee4d2d;
            color: white;
            width: 100%;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-orange:hover {
            background-color: #d7441c;
        }
        .text-price {
            color: #ee4d2d;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background: white;
        }
        .product-card img {
            width: 100%;
            height: auto;
        }
        .product-card .card-body {
            padding: 15px;
        }
        .btn-orange {
            background-color: #ee4d2d;
            color: white;
            width: 100%;
        }
        .btn-orange:hover {
            background-color: #d7441c;
        }
        .text-price {
            color: #ee4d2d;
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Produk yang Telah Dibeli</h2>
        
        <?php if (isset($_COOKIE['purchased_products'])): ?>
            <ul>
                <?php foreach (json_decode($_COOKIE['purchased_products'], true) as $product): ?>
                    <li><?= htmlspecialchars($product['name']) ?> - Rp <?= number_format($product['price'], 2, ',', '.') ?> - Status: <b><?= htmlspecialchars($product['status']) ?></b></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Belum ada produk yang dibeli.</p>
        <?php endif; ?>

        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="product-card shadow-sm mb-4">
                            <img src="uploads/images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                <p class="text-price">Rp <?= number_format($row['price'], 2, ',', '.') ?></p>
                                <form method="POST" action="simpan-ulasan.php">
                                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                    <label class="form-label">Rating:</label>
                                    <select name="rating" class="form-select mb-2">
                                        <option value="5">⭐⭐⭐⭐⭐ - Sangat Bagus</option>
                                        <option value="4">⭐⭐⭐⭐ - Bagus</option>
                                        <option value="3">⭐⭐⭐ - Biasa</option>
                                        <option value="2">⭐⭐ - Kurang</option>
                                        <option value="1">⭐ - Buruk</option>
                                    </select>
                                    <label class="form-label">Komentar:</label>
                                    <textarea name="comment" class="form-control mb-2" rows="3" required></textarea>
                                    <button type="submit" class="btn btn-orange">Beri Nilai</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                
            <?php endif; ?>
        </div>

        <h2 class="text-center mt-5">Produk Favorit Anda</h2>
        <div class="row">
            <?php if (!empty($favorites)): ?>
                <?php foreach ($favorites as $favProduct): ?>
                    <div class="col-md-4">
                        <div class="product-card shadow-sm mb-4">
                            <img src="uploads/images/<?= htmlspecialchars($favProduct['image']) ?>" alt="<?= htmlspecialchars($favProduct['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($favProduct['name']) ?></h5>
                                <p class="text-price">Rp <?= number_format($favProduct['price'], 2, ',', '.') ?></p>
                                <a href="product-detail.php?id=<?= $favProduct['id'] ?>" class="btn btn-orange">Lihat Produk</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center text-muted">Belum ada produk favorit.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
