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
            padding: 0;
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

        .cart-table th, .cart-table td {
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

        .btn-clear, .btn-checkout {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .btn-clear:hover, .btn-checkout:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .cart-container {
                padding: 10px;
            }

            .cart-table th, .cart-table td {
                padding: 5px;
            }

            .cart-table img {
                width: 80px;
            }

            .cart-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn-clear, .btn-checkout {
                width: 100%;
                text-align: center;
                margin-bottom: 10px;
            }
            
        @media (max-width: 480px) {
            .cart-table th, .cart-table td {
                display: block;
                width: 100%;
                text-align: right;
            }

            .cart-table th::before, .cart-table td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
            }

            .cart-table th, .cart-table td {
                padding: 10px;
                border-bottom: 1px solid #ddd;
            }

            .cart-table img {
                width: 60px;
            }

            .cart-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn-clear, .btn-checkout {
                width: 100%;
                text-align: center;
                margin-bottom: 10px;
            }
        }
        }

    </style>
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">Library Book</a>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
        </nav>
    </header>

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
                            <td><img src="<?= htmlspecialchars($productImage) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image"></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>Rp<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <form action="cart.php" method="get">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" onchange="this.form.submit()" required>
                                </form>
                            </td>
                            <td>Rp<?= number_format($total, 2) ?></td>
                            <td>
                                <a href="cart.php?action=remove&id=<?= $id ?>" class="btn-remove">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Total Harga</strong></td>
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
</html>
