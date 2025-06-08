<?php
session_start();
include 'db/config.php';

// Periksa apakah ID produk disediakan
if (!isset($_GET['id'])) {
    die("Produk tidak ditemukan.");
}

$product_id = intval($_GET['id']);

// Ambil data produk dari database
$query = $conn->prepare("SELECT * FROM products WHERE id = ?");
$query->bind_param("i", $product_id);
$query->execute();
$result = $query->get_result();
$product = $result->fetch_assoc();

// Jika produk tidak ditemukan
if (!$product) {
    die("Produk tidak ditemukan.");
}

// Logika untuk menambahkan ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Validasi jumlah
    if ($quantity < 1) {
        $quantity = 1;
    }

    // Cek stok sebelum menambahkan ke keranjang
    if ($product['stok'] <= 0) {
        echo "<script>alert('Stok habis! Tidak dapat menambahkan ke keranjang.'); window.location.href='product_detail.php?id=$product_id';</script>";
        exit;
    }

    // Inisialisasi keranjang belanja jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Tambahkan atau perbarui produk di keranjang
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => 'uploads/images/' . $product['image']
        ];
    }

    // Redirect untuk menghindari pengiriman ulang form
    header("Location: cart_view.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    .navbar {
    position: fixed; /* Tetap di atas */
    top: 0;
    left: 0;
    width: 100%; /* Pastikan tidak melebihi batas */
    display: flex;
    justify-content: space-between; /* Rata kiri-kanan */
    align-items: center;
    padding: 1rem 5%; /* Tambahkan padding agar tidak menempel */
    background-color: #333;
    z-index: 1000;
  }

    /* File: style.css */
    .product-details {
        display: flex;
        gap: 2rem;
        padding: 2rem;
        background-color: #f9f9f9;
        color: #333;
    }

    .product-gallery {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 5rem;
    }

    .product-gallery .main-image {
        width: 70%;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .thumbnail-gallery {
        padding-left: 20px;
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .thumbnail-gallery img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        border: 2px solid #ddd;
        cursor: pointer;
        transition: border-color 0.2s;
    }

    .thumbnail-gallery img:hover {
        border-color: #007bff;
    }

    .product-info {
        flex: 1;
    }

    .product-info h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
        padding-top: 5rem;
    }

    .product-info .product-price {
        font-size: 1.5rem;
        color: #007bff;
        margin-bottom: 1rem;
    }

    .product-info .product-description {
        margin-bottom: 1.5rem;
        line-height: 1.6;
        color: #555;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    form input[type="number"] {
        width: 80px;
        padding: 0.5rem;
        font-size: 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    form .btn.add-to-cart {
        background-color: #007bff;
        color: #fff;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    form .btn.add-to-cart:hover {
        background-color: #0056b3;
    }
    .btn.share {
            background-color: #25D366;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        .btn.share:hover {
            background-color: #1da851;
        }
        .btn.share img {
            width: 20px;
            height: 20px;
        }
        .popup {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            display: none;
            font-size: 14px;
            animation: fadeInOut 3s ease-in-out;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; }
        }
        .cart-icon {
            position: absolute;
            right: 13%;
            top: 10px;
        }
        .cart-icon img {
            width: 45px;
            height: 45px;
        }
</style>
<body>
<header class="navbar">
        <a href="index.php" class="logo">Library Book</a>
        <a href="cart.php" class="cart-icon">
        <img src="https://cdn-icons-png.flaticon.com/512/3144/3144456.png" alt="Cart">
    </a>
    </header>
        
    <main class="product-details">
        
        <div class="product-gallery">
            <?php 
                $image_path = 'uploads/images/' . $product['image'];
                if (file_exists($image_path)): 
            ?>
                <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="main-image">
            <?php else: ?>
                <img src="uploads/images/default.jpeg" alt="Default Image" class="main-image">
            <?php endif; ?>
        </div>
                
        <div class="product-info">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p class="product-price">Rp<?= number_format($product['price'], 0, ',', '.') ?></p>
            <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
            <p><strong>Stok: <?= $product['stok'] ?></strong></p>
                
            <form method="POST">
                <input type="hidden" name="action" value="add_to_cart">
                <label for="quantity">Kuantitas:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stok'] ?>" required>
                <button type="submit" class="btn add-to-cart" <?= $product['stok'] <= 0 ? 'disabled' : '' ?>>Tambahkan ke Keranjang</button>
            </form>
            <button class="btn share" onclick="copyLink()">
            <img src="https://cdn-icons-png.flaticon.com/512/786/786205.png" alt="Share"> Bagikan Produk
            <div id="popup" class="popup">Link produk telah disalin!</div>
            <?php if ($product['stok'] <= 0): ?>
                <p style="color: red;">Stok Habis</p>
            <?php endif; ?>
            
        <script>
            function copyLink() {
                const link = window.location.href;
                navigator.clipboard.writeText(link).then(() => {
                    const popup = document.getElementById('popup');
                    popup.style.display = 'block';
                    setTimeout(() => {
                        popup.style.display = 'none';
                    }, 3000);
                });
            }
            
        </script>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Online Library</p>
    </footer>
</body>

</html>