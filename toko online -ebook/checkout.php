<?php
session_start();
require 'vendor/autoload.php';
require 'db/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-g6_POD2Koz8RALVP0AAMKkS3';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Keranjang Anda kosong. <a href='index.php'>Belanja sekarang</a>.</p>";
    exit();
}

// Inisialisasi data user
$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['user_email'] ?? 'customer@example.com';

// Hitung total harga
$total = 0;
$protectionFee = 500;
$shippingFee = 0;
$serviceFee = 1000;

foreach ($_SESSION['cart'] as $productId => $product) {
    $subtotal = $product['price'] * $product['quantity'];
    $total += $subtotal;
}

// Diskon
$discountCode = $_POST['discount_code'] ?? '';
$discountAmount = 0;
if ($discountCode === 'Xsanyy') {
    $discountAmount = $total * 0.15;
} elseif ($discountCode === 'bukuhebat') {
    $discountAmount = $total * 0.50;
}

$total -= $discountAmount;
$totalPayment = max(0, $total + $protectionFee + $shippingFee + $serviceFee);
$orderId = 'ORDER-' . time();

// Simpan data ke tabel order_history
$stmt = $conn->prepare("INSERT INTO order_history (order_id, user_id, product_id, quantity, total_price, status, user_email) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Gagal menyiapkan query: " . $conn->error);
}

$status = 'Pending'; // Status awal "Pending"
foreach ($_SESSION['cart'] as $productId => $product) {
    $stmt->bind_param("siiidss", $orderId, $userId, $productId, $product['quantity'], $totalPayment, $status, $userEmail);
    $stmt->execute();
}
$stmt->close();

// **GENERATE SNAP TOKEN DARI MIDTRANS**
$transaction = [
    'transaction_details' => [
        'order_id' => $orderId,
        'gross_amount' => $totalPayment,
    ],
    'customer_details' => [
        'email' => $userEmail,
    ],
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($transaction);
} catch (Exception $e) {
    die("Gagal mendapatkan token pembayaran: " . $e->getMessage());
}

// **PROSES PENGIRIMAN EMAIL JIKA USER MELAKUKAN PEMBAYARAN**
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $emailTo = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($emailTo, FILTER_VALIDATE_EMAIL)) {
        echo "Email tidak valid.";
        exit();
    }

    // **Generate PDF Invoice**
    $options = new Options();
    $options->set('defaultFont', 'Courier');
    $dompdf = new Dompdf($options);
    
    $html = "<h1>Invoice Pembelian</h1>";
    $html .= "<p>Order ID: $orderId</p>";
    $html .= "<table border='1'><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr>";
    foreach ($_SESSION['cart'] as $productId => $product) {
        $subtotal = $product['price'] * $product['quantity'];
        $html .= "<tr><td>{$product['name']}</td><td>Rp" . number_format($product['price'], 2) . "</td><td>{$product['quantity']}</td><td>Rp" . number_format($subtotal, 2) . "</td></tr>";
    }
    $html .= "</table><h3>Total: Rp" . number_format($totalPayment, 2) . "</h3>";
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $pdfOutput = $dompdf->output();
    $pdfFilePath = 'uploads/invoice_' . $orderId . '.pdf';
    file_put_contents($pdfFilePath, $pdfOutput);

    // **Kumpulkan link download file eBook**
    $fileLinks = "";
    foreach ($_SESSION['cart'] as $productId => $product) {
        $stmt = $conn->prepare("SELECT pdf_file FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $pdfPath = 'uploads/pdf/' . $row['pdf_file'];
            if (file_exists($pdfPath)) {
                $fileLinks .= "<a href='https://vaguely-renewed-weevil.ngrok-free.app/html/projek-perpus/$pdfPath'>{$row['pdf_file']}</a><br>";
            }
        }
        $stmt->close();
    }

    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ff29042028@gmail.com';
        $mail->Password = 'ijgl ywsb lbzt mndw';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Library Book');
        $mail->addAddress($emailTo);
        $mail->Subject = 'Invoice dan Buku Digital Anda';
        $mail->isHTML(true);
        $mail->Body = "Terima kasih telah membeli buku dari Library Book.<br>
                       Berikut adalah invoice pembelian Anda.<br><br>
                       <b>Order ID:</b> $orderId <br>
                       <b>Total Pembayaran:</b> Rp " . number_format($totalPayment, 2) . "<br><br>
                       <b>Silakan download eBook Anda dari link berikut:</b> <br> $fileLinks";
        
        $mail->addAttachment($pdfFilePath);

        if ($mail->send()) {
            echo "Email berhasil dikirim ke $emailTo.";
        } else {
            echo "Gagal mengirim email.";
        }
    } catch (Exception $e) {
        echo "Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="checkout.css">
</head>
<body>

<header class="navbar">
    <a href="index.php" class="logo">Library Book</a>
    <nav>
        <a href="index.php">Home</a>
        <a href="cart.php">Cart</a>
    </nav>
</header>

<main class="checkout-container">
    <h1>Checkout</h1>
    <div class="order-summary">
        <h2>Produk Dipesan</h2>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($_SESSION['cart'] as $productId => $product): ?>
                    <?php
                    // Ambil gambar produk dari database
                    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
                    $stmt->bind_param("i", $productId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $productImage = 'uploads/images/default.jpeg'; // Gambar default

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $productImage = 'uploads/images/' . $row['image']; // Ambil gambar dari database
                    }
                    ?>
                    <tr>
                        <td>
                            <div class="product-details">
                                <img src="<?= htmlspecialchars($productImage) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                                <span><?= htmlspecialchars($product['name']) ?></span>
                            </div>
                        </td>
                        <td>Rp<?= number_format($product['price'], 2) ?></td>
                        <td><?= $product['quantity'] ?></td>
                        <td>Rp<?= number_format($product['price'] * $product['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="summary-details">
            <p><strong>Subtotal Produk:</strong> Rp<?= number_format($total, 2) ?></p>
            <p><strong>Biaya Perlindungan:</strong> Rp<?= number_format($protectionFee, 2) ?></p>
            <p><strong>Biaya Pengiriman:</strong> Rp<?= number_format($shippingFee, 2) ?></p>
            <p><strong>Biaya Layanan:</strong> Rp<?= number_format($serviceFee, 2) ?></p>
            <?php if ($discountAmount > 0): ?>
                <p><strong>Diskon:</strong> -Rp<?= number_format($discountAmount, 2) ?></p>
            <?php endif; ?>
            <h3>Total Pembayaran: Rp<?= number_format($totalPayment, 2) ?></h3>
        </div>
    </div>

    <div class="payment-methods">
        <button id="pay-button" class="btn-checkout" type="button">Bayar Sekarang</button>
    </div>
</main>
<!-- Popup Email Modern -->
<div id="email-popup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:#fff; padding:20px; text-align:center; border-radius:10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);">
        <h2 style="color:#333;">📩 Pembayaran Berhasil</h2>
        <p>Masukkan email untuk menerima ebook & invoice.</p>
        <input type="email" id="email" placeholder="Masukkan email" required style="padding:10px; width:80%; margin-bottom:10px; border:1px solid #ddd; border-radius:5px;">
        <br>
        <button onclick="kirimEmail()" style="padding:10px 20px; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer;">Kirim</button>
    </div>
</div>


<!-- Midtrans JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-vkqFRTfr7YQulw_h"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const payButton = document.getElementById("pay-button");
        const snapToken = "<?= $snapToken ?>";

        console.log("Snap Token:", snapToken); // Debugging

        if (!snapToken) {
            alert("Token pembayaran tidak tersedia. Mohon ulangi checkout.");
            payButton.disabled = true;
            return;
        }

        payButton.addEventListener("click", function () {
            snap.pay(snapToken, {
                onSuccess: function (result) {
                    console.log("Success:", result);
                    document.getElementById("email-popup").style.display = "flex";
                },
                onPending: function (result) {
                    console.log("Pending:", result);
                    alert("Pembayaran Anda pending.");
                },
                onError: function (result) {
                    console.log("Error:", result);
                    alert("Terjadi kesalahan saat pembayaran.");
                },
                onClose: function () {
                    alert("Anda menutup pembayaran tanpa menyelesaikannya.");
                }
            });
        });
    });
   function kirimEmail() {
    let email = document.getElementById("email").value;

    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Silakan masukkan email terlebih dahulu!',
        });
        return;
    }

    Swal.fire({
        title: 'Mengirim...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("checkout.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "email=" + encodeURIComponent(email)
    })
    .then(response => response.text())
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Ebook dan invoice telah dikirim ke email Anda!',
            confirmButtonText: 'Lanjutkan'
        }).then(() => {
            window.location.href = "reting-produk.php"; // Redirect ke halaman rating produk
        });

        document.getElementById("email-popup").style.display = "none";
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat mengirim email. Coba lagi nanti.',
        });
    });
}

</script>

</body>
</html>
