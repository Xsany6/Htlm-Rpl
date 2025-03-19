<?php
// File: trending.php
session_start();
include 'db/config.php';

// Fungsi untuk meningkatkan visit_count setiap kali produk dikunjungi
if (isset($_GET['id'])) {
    $productId = $_GET['id']; // ID produk dari URL
    $query = "UPDATE products SET visit_count = visit_count + 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
}

// Ambil data produk yang diurutkan berdasarkan visit_count
$query = "SELECT * FROM products ORDER BY visit_count DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error pada query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending Products</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body, h1, h2, h3, p, ul, li, a, img {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-decoration: none;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            line-height: 1.6;
            margin: 0;
        }

        header {
            background-color: #333;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo {
            font-size: 1.5rem;
            color: #fff;
            text-decoration: none;
        }

        header .nav-links {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        header .nav-links a {
            margin-right: 1rem;
            text-decoration: none;
            color: #ddd;
        }

        header .nav-links a:hover {
            color: #fff;
        }

        header .navbar-right {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        header .navbar-right form input {
            padding: 0.5rem;
            margin-right: 0.5rem;
            border-radius: 4px;
            border: none;
        }

        header .navbar-right form button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
        }

        header .navbar-right form button:hover {
            background-color: #0056b3;
        }

        .product-container {
            padding: 2rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 30px;
            padding: 2rem;
            justify-items: start;
        }

        .product-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #212121;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: left;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }

        .product-card .product-info {
            padding: 1rem;
        }

        .product-card h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: white;
        }

        .product-card p {
            font-size: 0.9rem;
            color: #aaa;
            margin-bottom: 1rem;
            text-align: justify;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-card .btn-add-to-cart {
            position: relative;
            display: inline-block;
            background-color: #5cb85c;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            padding: 0.5rem 1rem;
            margin-top: 1rem;
            transition: background-color 0.3s ease;
        }

        .product-card .btn-add-to-cart:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">Library Book</a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="#trending">Trending</a>
            <a href="pages/kategori/">Share Books</a>
            <a href="pages/class.html">Class</a>
            <a href="cart.php">Cart</a>
        </nav>
        <div class="navbar-right">
            <form action="index.php" method="GET">
                <input type="text" name="q" placeholder="Search..." value="">
                <button type="submit">Search</button>
            </form>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="product-container">
        <h1>Trending Products</h1>
        <p>Produk yang paling banyak dikunjungi pengguna:</p>

        <div class="product-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <a href="product_detail.php?id=<?= $row['id'] ?>">
                            <img src="uploads/images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            <h2><?= htmlspecialchars($row['name']) ?></h2>
                        </a>
                        <p>Harga: Rp<?= number_format($row['price'], 2) ?></p>
                        <p><strong>Jumlah Kunjungan: <?= $row['visit_count'] ?></strong></p>
                        <a href="cart.php?action=add&id=<?= $row['id'] ?>&name=<?= urlencode($row['name']) ?>&price=<?= $row['price'] ?>" class="btn-add-to-cart">Add to Cart</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Belum ada produk yang masuk dalam daftar trending.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
<footer>@Xsanyy6</footer>
</html>
