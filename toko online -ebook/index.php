<?php
session_start();
include 'db/config.php'; // Pastikan config.php memiliki koneksi $conn

// Cek apakah pengguna sudah login
$isLoggedIn = isset($_SESSION['user_id']);
$username = '';
$profile_pic = 'assets/default-profile.png';
$cartItems = []; // Pastikan variabel dideklarasikan sebelum digunakan
$unreadMessages = 0; // Inisialisasi jumlah pesan yang belum dibaca
$messages = []; // Inisialisasi pesan

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    
    // Ambil data pengguna
    $queryUser = "SELECT username, profile_pic FROM users WHERE id = ?";
    $stmtUser = $conn->prepare($queryUser);
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    
    if ($resultUser->num_rows > 0) {
        $user = $resultUser->fetch_assoc();
        $username = htmlspecialchars($user['username']);
        if (!empty($user['profile_pic'])) {
            $profile_pic = htmlspecialchars($user['profile_pic']);
        }
    }
    
    // Ambil jumlah pesan yang belum dibaca dan pesan terbaru
    $queryMessages = "SELECT id, sender_id, message, is_read FROM messages WHERE receiver_id = ? ORDER BY created_at DESC LIMIT 5";
    $stmtMessages = $conn->prepare($queryMessages);
    $stmtMessages->bind_param("i", $userId);
    $stmtMessages->execute();
    $resultMessages = $stmtMessages->get_result();
    
    while ($row = $resultMessages->fetch_assoc()) {
        $messages[] = $row;
        if ($row['is_read'] == 0) {
            $unreadMessages++;
        }
    }
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ambil daftar produk (diluar kondisi login agar tetap tampil untuk semua user)
$sql = "SELECT name, price, image FROM products LIMIT 5";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Ambil data keranjang
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Proses pencarian produk
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($searchQuery !== '') {
    $query = "SELECT * FROM products WHERE name LIKE ?";
    $stmt = $conn->prepare($query);
    $likeQuery = "%" . $searchQuery . "%";
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT * FROM products";
    $result = $conn->query($query);
}

if (!$result) {
    die("Error pada query: " . $conn->error);
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style-index.css">
    
    
    <style>
        .hidden {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .show {
            opacity: 1;
            transform: translateY(0);
        }

            /* Membuat dropdown transparan */
        .custom-dropdown {
        
            backdrop-filter: blur(8px); /* Efek blur agar tetap terlihat jelas */
            border: 1px solid rgba(255, 255, 255, 0.2); /* Border halus */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); /* Bayangan lembut */
        }

        /* Warna teks dropdown */
        .custom-dropdown .dropdown-item {
            color: #fff; /* Warna teks putih */
        }

        /* Efek hover */
        .custom-dropdown .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ddd;
        }

        /* Mengubah warna dropdown button */
        .btn-light {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: none;
        }
  
          .user-info {
        display: flex;
        align-items: center;
    }
        .user-info img {
        width: 40px; /* Atur ukuran foto agar lebih kecil */
        height: 40px; /* Pastikan proporsi tetap */
        border-radius: 50%; /* Membuat gambar bundar */
        object-fit: cover; /* Mencegah distorsi dan memastikan gambar terpotong dengan baik */
        border: 2px solid white; /* Opsional: Tambahkan border agar lebih jelas */
    }
    .user-section {
            display: flex;
            align-items: center;
            gap: 15px; /* Berikan jarak */
            z-index: 1000%;
        }

        .inbox-btn {
            background-color: #555;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .inbox-btn:hover {
            background-color: #777;
        }

        .user-profile {
            display: flex;
            align-items: center;
            background-color: #444;
            padding: 8px 12px;
            border-radius: 8px;
            color: white;

        }

        .user-profile img {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            margin-right: 8px;
        }

        /* --- MODAL STYLE --- */
        .swal2-container {
    z-index: 9999 !important;
}

        .modal {
            display: none;
            position: fixed;
            z-index: 9999; /* Pastikan modal di atas semua elemen */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            color: black;
        }

        .modal-content {
            background-color: white;
            width: 350px;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
            z-index: 10000; /* Pastikan konten modal tidak tertutup */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
            font-weight: bold;
            z-index: 100%;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .message-list {
            text-align: left;
            margin-top: 15px;
        }

        .message-item {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .message-item:hover {
            background-color: #e0e0e0;
        }
         .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #1a1a2e;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-container {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .line {
            position: absolute;
            width: 80px;
            height: 10px;
            background: linear-gradient(90deg, #ff9a9e, #fad0c4);
            border-radius: 5px;
            animation: rotateX 1.2s linear infinite;
        }

        .line:nth-child(2) {
            transform: rotate(90deg);
            animation: rotateY 1.2s linear infinite;
        }

        @keyframes rotateX {
            0%, 100% { transform: rotate(45deg); }
            50% { transform: rotate(-45deg); }
        }

        @keyframes rotateY {
            0%, 100% { transform: rotate(135deg); }
            50% { transform: rotate(45deg); }
        }
    </style>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const hiddenElements = document.querySelectorAll(".hidden");

    function checkScroll() {
        hiddenElements.forEach((el) => {
            const elementTop = el.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (elementTop < windowHeight - 50) {
                el.classList.add("show");
            }
        });
    }

    window.addEventListener("scroll", checkScroll);
    checkScroll();
});

        // Menghilangkan loading setelah halaman selesai dimuat
        window.onload = function() {
            document.querySelector('.loading-screen').style.display = 'none';
        };
    
        // Fungsi untuk toggle popup keranjang
        function toggleCartPopup() {
            const popup = document.querySelector('.cart-popup');
            popup.classList.toggle('active');
        }

        // Fungsi untuk sembunyikan/tampilkan bar pencarian saat scroll
        document.addEventListener("DOMContentLoaded", function() {
        let lastScrollTop = 0;
        const navbar = document.querySelector(".navbar");
        
        window.addEventListener("scroll", function() {
            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            
            if (currentScroll > lastScrollTop) {
                navbar.style.transform = "translateY(-100%)"; // Sembunyikan navbar
            } else {
                navbar.style.transform = "translateY(0)"; // Tampilkan navbar kembali
            }
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        }, false);
    });
    </script>
    
</head>

<body>
    <!-- Animasi Loading -->
    <div class="loading-screen">
        <div class="loading-container">
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </div>
<header class="navbar">
    
<div class="navbar-left">

<a href="index.php" class="logo">📚 Library Book</a>

    
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <div class="dropdown">
            <a href="#" class="dropdown-toggle">Kategori</a>
            <ul class="dropdown-menu">
                <li><a href="#hiburan">Hiburan</a></li>
                <li><a href="#entertain">Entertain</a></li>
                <li><a href="#edukasi">Edukasi</a></li>
                <li><a href="#motivasi">Motivasi</a></li>
            </ul>
        </div>
        <a href="pages/kategori/">Share Books</a>
        <a href="pages/class.html">Class</a>
    </nav>
</div>
<div class="navbar-kanan user-info">
    <?php if ($isLoggedIn): ?>
        
        <div class="dropdown">
            
            <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?= $profile_pic ?>" alt="Foto Profil" class="me-2">
                <span class="user-name"><?= $username ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="user-profil.php">Akun Saya</a></li>
                <li><a class="dropdown-item" href="riwayat_transaksi.php">Pesanan Saya</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Log Out</a></li>
            </ul>
        </div>
        
    <?php else: ?>
        <a href="login.php" class="btn btn-sm btn-primary">Login</a>
        <a href="register.php" class="btn btn-sm btn-success">Register</a>
    <?php endif; ?>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="navbar-right">
    <form action="index.php" method="GET">
        <input type="text" name="q" placeholder="Cari produk..." value="<?= htmlspecialchars($searchQuery) ?>">
        <button type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                <path fill="currentColor" d="M15.5 14h-.79l-.28-.27a6.518 6.518 0 0 0 1.48-5.34C15.13 5.02 12.11 2 8.5 2S1.87 5.02 1.87 8.5 5.02 15 8.5 15c1.61 0 3.09-.59 4.24-1.57l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.5 0s.41-1.08 0-1.5l-4.26-4.25zM3.87 8.5C3.87 6.02 5.97 4 8.5 4s4.63 2.02 4.63 4.5S11.03 13 8.5 13s-4.63-2.02-4.63-4.5z"/>
            </svg>
        </button>
    </form>
    <div class="user-section">
        <button class="inbox-btn" onclick="openModal()">📥 Kotak Masuk (<?= $unreadMessages ?>)</button>
    </div>
</header>
<div id="inboxModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span>Kotak Masuk</span>
            <button class="close-btn" onclick="closeModal()">❌</button>
        </div>
        <div class="message-list">
            <?php foreach ($messages as $message): ?>
                <div class="message-item <?= !$message['is_read'] ? 'unread' : ''; ?>" 
                     onclick="markAsRead(<?= $message['id']; ?>, this)">
                    📩 <?= htmlspecialchars($message['message']) ?>
                    <button class="delete-btn" onclick="deleteMessage(<?= $message['id']; ?>, event)">🗑️</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script src="script-index.js"></script> <!-- Pastikan file JavaScript sudah dipanggil -->

<!-- Tombol Cart -->
<div class="cart-dropdown">
    <button class="cart-button">
        <a href="cart.php">
            <img src="images.ico" alt="Cart Icon" class="cart-icon">
        </a>
        <span class="cart-count"><?= isset($cartItems) && is_array($cartItems) ? count($cartItems) : 0 ?></span>
    </button>
    <div class="cart-popup">
        <h3>Baru Ditambahkan</h3>
        <?php if (!empty($cartItems) && is_array($cartItems)): ?>
            <ul class="cart-list">
                <?php 
                $grandTotal = 0;
                foreach ($cartItems as $id => $item): 
                    $total = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                    $grandTotal += $total;

                    // Cek apakah gambar tersedia dan valid
                    $productImage = !empty($item['image']) ? "uploads/images/{$item['image']}" : "assets/default-product.png";
                    
                    // Pastikan file ada jika bukan URL
                    if (!filter_var($productImage, FILTER_VALIDATE_URL) && !file_exists($productImage)) {
                        $productImage = "assets/default-product.png";
                    }
                    ?>
                    
                <li class="cart-item">
                    <img 
                        src="<?= htmlspecialchars($productImage) ?>" 
                        alt="<?= htmlspecialchars($item['name'] ?? 'Nama Tidak Tersedia') ?>" 
                        class="item-image" 
                        style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="item-info">
                        <span class="item-name"><?= htmlspecialchars($item['name'] ?? 'Nama Tidak Tersedia') ?></span>
                        <span class="item-price">Rp<?= number_format($item['price'] ?? 0, 2) ?></span>
                        <span class="item-quantity">Jumlah: <?= (int)($item['quantity'] ?? 1) ?></span>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="cart-total">
                <strong>Total: </strong>Rp<?= number_format($grandTotal, 2) ?>
            </div>
            <a href="cart.php" class="view-cart-button">Tampilkan Keranjang Belanja</a>
        <?php else: ?>
            <p class="empty-message">Keranjang Anda kosong.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetchProducts();
    });

    function fetchProducts() {
        fetch("get_products.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error("Gagal mengambil produk.");
                }
                return response.json();
            })
            .then(data => {
                const productContainer = document.getElementById("product-list");
                productContainer.innerHTML = "";

                if (!Array.isArray(data) || data.length === 0) {
                    productContainer.innerHTML = "<p>Tidak ada produk yang tersedia.</p>";
                    return;
                }

                data.forEach(product => {
                    const productCard = `
                        <div class="product-card">
                            <img src="${product.image ? product.image : 'assets/default-product.png'}" 
                                 alt="${product.name ? product.name : 'Produk'}" 
                                 onerror="this.src='assets/default-product.png'">
                            <h3>${product.name}</h3>
                            <p>Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</p>
                            <button onclick="addToCart(${product.id})">Tambah ke Keranjang</button>
                        </div>
                    `;
                    productContainer.innerHTML += productCard;
                });
            })
            .catch(error => console.error("Error mengambil produk:", error));
    }

    function addToCart(productId) {
        if (!productId) {
            alert("ID produk tidak valid.");
            return;
        }

        fetch(`add_to_cart.php?product_id=${encodeURIComponent(productId)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Gagal menambahkan produk.");
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert("Produk berhasil ditambahkan ke keranjang!");
                } else {
                    alert(data.message || "Gagal menambahkan produk.");
                }
            })
            .catch(error => console.error("Error:", error));
    }
</script>
<main class="product-container">
   <div class="parallax">
    <h1>Welcome to Our Library</h1>
</div>

<?php if ($searchQuery !== ''): ?>
    <p>Showing results for "<strong><?= htmlspecialchars($searchQuery) ?></strong>":</p>
<?php endif; ?>

<div class="product-grid">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <a href="product_detail.php?id=<?= $row['id'] ?>">
                    <img src="uploads/images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                    <h2><?= htmlspecialchars($row['name']) ?></h2>
                </a>
                <p>Harga: Rp<?= number_format($row['price'], 2) ?></p>
                <a href="cart.php?action=add&id=<?= $row['id'] ?>&name=<?= urlencode($row['name']) ?>&price=<?= $row['price'] ?>&stok=<?= $row['stok'] ?>" class="btn-add-to-cart">Add to Cart</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No products found for "<strong><?= htmlspecialchars($searchQuery) ?></strong>".</p>
    <?php endif; ?>
</div>

    <button class="btn btn-danger chat-button" data-bs-toggle="modal" data-bs-target="#chatModal">
    Hubungi CS
</button>
</main>

<!-- MODAL CHAT -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title text-dark" id="chatModalLabel">Chat Customer Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="send_chatuser.php" method="POST">
                    <input type="hidden" name="pengirim" value="<?= htmlspecialchars($username) ?>">
                    <div class="mb-3">
                        <label class="form-label text-dark">Pesan Anda:</label>
                        <textarea name="pesan" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Kirim</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Slider Metode Pembayaran -->
<div class="payment-slider">
    <div class="payment-track">
        <img src="./images/png-clipart-bank-central-asia-logo-bca-finance-business-bank-blue-cdr.png" alt="BCA">
        <img src="./images/images.jpg" alt="BNI">
        <img src="./images/BANK_BRI_logo_with_slogan.svg" alt="BRI">
        <img src="./images/Visa_Brandmark_Blue_RGB_2021.png" alt="Visa">
        <img src="./images/png-transparent-bank-mandiri-bank-syariah-mandiri-logo-bank-text-logo-loan-thumbnail.png" alt="Mandiri">
        <img src="./images/Logo GoPay - Dianisa.com.png" alt="GoPay">
        <img src="./images/Logo DANA -  dianisa.com.png" alt="Dana">
        <img src="./images/OVO - dianisa.com.png" alt= "OVO">
        <img src="./images/Logo ShopeePay - Dianisa.com.png" alt="ShopeePay">
   <!-- Duplikasi untuk looping -->
  
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const track = document.querySelector(".payment-track");

    // Duplikasi elemen secara dinamis supaya looping mulus
    track.innerHTML += track.innerHTML;
});


</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
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
            <ul>
                <li>Facebook</li>
                <li>Instagram</li>
                <li>Twitter</li>
                <li>LinkedIn</li>
            </ul>
        </div>
        
    </div>
</footer>
<style>
    footer {
        background: white;
        padding: 20px;

    }
    .footer-container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .footer-section {
        width: 15%;
        color:black;
    }
    .footer-section h4 {
        font-size: 26px;
        margin-bottom: 5px;
    }
    .footer-section ul {
        list-style: none;
        padding: 0;
    }
    .footer-section ul li {
        margin-bottom: 5px;
        color:black; 
        display: flex;
    }
    .payment-methods img {
        width: 50px;
        margin-right: 5px;
    }
    .qr-code {
        width: 100px;
    }
</style>
</html>
