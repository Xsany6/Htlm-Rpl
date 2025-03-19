<?php
require 'db/config.php';

// **Ubah status Pending ke Gagal jika lebih dari 10 menit**
$conn->query("UPDATE order_history 
              SET status = 'Gagal' 
              WHERE status = 'Pending' 
              AND TIMESTAMPDIFF(MINUTE, order_date, NOW()) >= 10");

// **Ambil parameter tanggal dari AJAX**
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// **Query dasar dengan filter duplikasi berdasarkan waktu < 1 menit**
$query = "SELECT oh.order_id, oh.user_email, p.name AS product_name, oh.quantity, oh.total_price, oh.order_date, oh.status
          FROM order_history oh
          JOIN products p ON oh.product_id = p.id
          WHERE NOT EXISTS (
              SELECT 1 FROM order_history sub_oh
              WHERE sub_oh.product_id = oh.product_id
              AND sub_oh.user_email = oh.user_email
              AND TIMESTAMPDIFF(SECOND, sub_oh.order_date, oh.order_date) BETWEEN 0 AND 59
              AND sub_oh.order_date < oh.order_date
          )";

// **Tambahkan filter tanggal jika tersedia**
if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND DATE(oh.order_date) BETWEEN ? AND ?";
}

// **Urutkan berdasarkan tanggal terbaru**
$query .= " ORDER BY oh.order_date DESC";

// **Gunakan prepared statement untuk keamanan**
$stmt = $conn->prepare($query);

if (!empty($startDate) && !empty($endDate)) {
    $stmt->bind_param("ss", $startDate, $endDate);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        // **Tentukan class status**
        $statusClass = "";
        if ($row['status'] === "Pending") {
            $statusClass = "table-warning";
        } elseif ($row['status'] === "Sukses") {
            $statusClass = "table-success";
        } elseif ($row['status'] === "Gagal") {
            $statusClass = "table-danger";
        }

        // **Tampilkan hasil yang sudah difilter**
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['user_email']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['quantity']}</td>
                <td>Rp " . number_format($row['total_price'], 2, ',', '.') . "</td>
                <td>{$row['order_date']}</td>
                <td class='$statusClass'>{$row['status']}</td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>Belum ada pembelian.</td></tr>";
}

$stmt->close();
$conn->close();
?>
