<?php
require 'db/config.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? '';
$status = $data['transaction_status'] ?? '';
$email = $data['email'] ?? '';
$payment_data = json_encode($data['payment_data'] ?? []);

if ($order_id && $status && $email) {
    // Simpan transaksi ke database
    $stmt = $conn->prepare("INSERT INTO transactions (order_id, status, email, payment_data) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $order_id, $status, $email, $payment_data);

    if ($stmt->execute()) {
        // Ambil data produk yang dibeli
        $stmt = $conn->prepare("SELECT p.name, p.price, p.pdf_file, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.order_id = ?");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        // Buat PDF invoice
        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $dompdf = new Dompdf($options);
        
        $html = "<h1>Invoice Pembelian</h1><p>Order ID: $order_id</p><table border='1'><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr>";
        $total = 0;
        foreach ($items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
            $html .= "<tr><td>{$item['name']}</td><td>Rp" . number_format($item['price'], 2) . "</td><td>{$item['quantity']}</td><td>Rp" . number_format($subtotal, 2) . "</td></tr>";
        }
        $html .= "</table><h3>Total: Rp" . number_format($total, 2) . "</h3>";
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $pdfOutput = $dompdf->output();
        $pdfFilePath = "uploads/invoice_$order_id.pdf";
        file_put_contents($pdfFilePath, $pdfOutput);
        
        // Kirim email dengan PHPMailer
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
            $mail->addAddress($email);
            $mail->Subject = 'Invoice dan Buku Digital Anda';
            $mail->Body = "Terima kasih telah melakukan pembelian. Berikut adalah invoice dan buku digital Anda.";
            $mail->addAttachment($pdfFilePath);
            
            // Tambahkan setiap eBook sebagai lampiran
            foreach ($items as $item) {
                $pdfPath = 'uploads/pdf/' . $item['pdf_file'];
                if (file_exists($pdfPath)) {
                    $mail->addAttachment($pdfPath);
                }
            }
            
            if ($mail->send()) {
                echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil, email telah dikirim.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim email.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
}
?>
