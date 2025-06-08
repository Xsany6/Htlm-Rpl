<?php 
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Simpan halaman yang sedang diakses untuk redirect setelah login
    $_SESSION['redirect_after_login'] = 'cart.php';
    header('Location: login.php');
    exit();
}
// echo '<pre>';
// echo 'Product Image Path: ' . 'uploads/images/' . htmlspecialchars($product['image']);
// echo '</pre>';

// Simpan cart dalam sesi
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Tambahkan produk ke cart
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $productId = $_GET['id'];
    $productName = $_GET['name'];
    $productPrice = $_GET['price'];

    // Ambil gambar produk dari database
    include 'db/config.php'; // Pastikan Anda sudah menghubungkan ke database
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $productImage = 'uploads/images/default.jpeg'; // Gambar default

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $productImage = 'uploads/images/' . $row['image']; // Ambil gambar dari database
    }

    // Tambahkan produk ke cart
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = [
            'name' => $productName,
            'price' => $productPrice,
            'image' => $productImage,
            'quantity' => 1,
        ];
    } else {
        $_SESSION['cart'][$productId]['quantity']++;
    }

    header('Location: cart.php');
    exit();
}

// Update quantity produk dalam cart
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['quantity'])) {
    $productId = $_GET['id'];
    $newQuantity = (int) $_GET['quantity'];

    if ($newQuantity > 0) {
        $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
    } else {
        // Jika quantity <= 0, hapus produk dari cart
        unset($_SESSION['cart'][$productId]);
    }

    header('Location: cart.php');
    exit();
}

// Hapus produk dari cart
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $productId = $_GET['id'];
    unset($_SESSION['cart'][$productId]);

    header('Location: cart.php');
    exit();
}

// Kosongkan cart
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    $_SESSION['cart'] = [];

    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="cart.css">
    <style>
    /* Menyesuaikan ukuran gambar dalam tabel */


    body {
        background-image: url('bagrowsnnn.jpeg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        margin: 0;
        padding: 70px;
        font-family: Arial, sans-serif;
    }

    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .cart-table th,
    .cart-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .cart-table img {
        width: 100px;
        height: auto;
        object-fit: cover;
    }

    .cart-table input[type="number"] {
        width: 60px;
        padding: 5px;
        text-align: center;
    }

    .cart-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn-clear,
    .btn-checkout {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .btn-clear:hover,
    .btn-checkout:hover {
        background-color: #0056b3;
    }

    @media (max-width: 768px) {
        .cart-container {
            padding: 10px;
        }

        .cart-actions {
            flex-direction: column;
            align-items: center;
        }

        .btn-clear,
        .btn-checkout {
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
        }

        .cart-table {
            border: 0;
        }

        .cart-table thead {
            display: none;
        }

        .cart-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: white;
        }

        .cart-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            text-align: left;
            border: none;
        }

        .cart-table td::before {
            content: attr(data-label);
            font-weight: bold;
            width: 40%;
            display: inline-block;
        }

        .cart-table img {
            width: 60px;
            height: auto;
            object-fit: cover;
        }

        .cart-table input[type="number"] {
            width: 60px;
        }
    }
    </style>
</head>

<body>

    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php">Xstore</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav d-flex flex-row gap-3 mb-0 list-unstyled">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="bi bi-house"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="bi bi-cart"></i> Cart
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Scroll Shrink Script -->
        <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shrink');
            } else {
                navbar.classList.remove('shrink');
            }
        });
        </script>

        <!-- Bootstrap & Bootstrap Icons CDN -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Custom Navbar Styling -->
        <style>
        .navbar {
            transition: all 0.3s ease;
            background-color: #343a40 !important;
        }

        .navbar-brand,
        .nav-link {
            color: #ffffff !important;
        }

        .navbar.shrink {
            padding-top: 5px;
            padding-bottom: 5px;
            background-color: #212529 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-nav {
            list-style: none;
            padding-left: 0;
        }
        </style>



        <main class="cart-container">
            <h1>Your Cart</h1>

            <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
            <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    foreach ($_SESSION['cart'] as $id => $item): 
                        $total = $item['price'] * $item['quantity'];
                        $grandTotal += $total;

                        // Tentukan jalur gambar produk
                        $productImage = $item['image'];

                        // Cek apakah gambar produk ada di server, jika tidak ada gunakan gambar default
                        if (!file_exists($productImage)) {
                            $productImage = 'uploads/images/default.jpeg'; // Gambar default
                        }
                    ?>
                    <tr>
                        <td data-label="Image">
                            <img src="<?= htmlspecialchars($productImage) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                        </td>
                        <td data-label="Product"><?= htmlspecialchars($item['name']) ?></td>
                        <td data-label="Price">Rp<?= number_format($item['price'], 2) ?></td>
                        <td data-label="Quantity">
                            <form action="cart.php" method="get">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1"
                                    onchange="this.form.submit()" required>
                            </form>
                        </td>
                        <td data-label="Total">Rp<?= number_format($total, 2) ?></td>
                        <td data-label="Action">
                            <a href="cart.php?action=remove&id=<?= $id ?>"
                                class="btn-remove btn btn-danger btn-sm">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Grand Total</strong></td>
                        <td colspan="2">Rp<?= number_format($grandTotal, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
            <div class="cart-actions">
                <a href="cart.php?action=clear" class="btn-clear">Clear Cart</a>
                <a href="checkout.php" class="btn-checkout">Checkout</a>
            </div>
            <?php endif; ?>
        </main>

    </body>
    <!-- Add this div above the footer to create spacing -->
    <div class="cart-footer-spacing"></div>

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h4>Layanan Pelanggan</h4>
                <ul>
                    <li>Bantuan</li>
                    <li>Metode Pembayaran</li>
                    <li>ShopeePay</li>
                    <li>Koin Shopee</li>
                    <li>Lacak Pesanan Pembeli</li>
                    <li>Lacak Pengiriman Penjual</li>
                    <li>Gratis Ongkir</li>
                    <li>Pengembalian Barang & Dana</li>
                    <li>Garansi Shopee</li>
                    <li>Hubungi Kami</li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Jelajahi Xstore</h4>
                <ul>
                    <li>Tentang Kami</li>
                    <li>Karir</li>
                    <li>Kebijakan Shopee</li>
                    <li>Kebijakan Privasi</li>
                    <li>Blog</li>
                    <li>Shopee Mall</li>
                    <li>Seller Centre</li>
                    <li>Flash Sale</li>
                    <li>Kontak Media</li>
                    <li>Shopee Affiliate</li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Pembayaran</h4>
                <div class="payment-methods">
                    <img src="images  payout/images.png" alt="BCA">
                    <img src="images  payout/images.jpg" alt="BNI">
                    <img src="images  payout/images (1).png" alt="BRI">
                    <img src="images  payout/images (2).png" alt="Visa">
                </div>
            </div>
            <div class="footer-section">
                <h4>Ikuti Kami</h4>
                <ul class="social-links">
                    <li>Facebook</li>
                    <li>Instagram</li>
                    <li>Twitter</li>
                    <li>LinkedIn</li>
                </ul>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Xstore. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <style>
    /* Spacing between cart and footer */
    .cart-footer-spacing {
        height: 60px;
        width: 100%;
    }

    footer {
        background: #f5f5f5;
        padding: 40px 20px 20px;
        margin-top: 0;
        border-top: 1px solid #e0e0e0;
        width: 100%;
    }

    .footer-container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
    }

    .footer-section {
        width: 23%;
        margin-bottom: 30px;
    }

    .footer-section h4 {
        font-size: 16px;
        margin-bottom: 15px;
        color: #333;
        font-weight: 600;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-section ul li {
        margin-bottom: 8px;
    }

    .footer-section ul li a {
        color: #666;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .footer-section ul li a:hover {
        color: #f57224;
    }

    .payment-methods {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .payment-methods img {
        width: 50px;
        height: 30px;
        object-fit: contain;
        background: white;
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .social-links {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .footer-bottom {
        width: 100%;
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
        margin-top: 20px;
        color: #999;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .footer-section {
            width: 48%;
        }

        .cart-footer-spacing {
            height: 40px;
        }
    }

    @media (max-width: 480px) {
        .footer-section {
            width: 100%;
        }

        .cart-footer-spacing {
            height: 30px;
        }
    }
    </style>

</html>