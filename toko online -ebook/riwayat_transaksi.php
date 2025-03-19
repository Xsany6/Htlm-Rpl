<?php
session_start();
require 'db/config.php';

// Cek apakah email tersedia di cookie
$userEmail = isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : '';

if (empty($userEmail)) {
    echo "<tr><td colspan='6' class='text-center text-danger'>User tidak ditemukan.</td></tr>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchUserOrders() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            $.ajax({
                url: 'fetch_user_orders.php',
                type: 'GET',
                data: { 
                    start_date: startDate, 
                    end_date: endDate,
                    user_email: '<?= $userEmail ?>' // Kirim email user dari cookie
                },
                success: function(data) {
                    $('#orderTable tbody').html(data);
                }
            });
        }

        $(document).ready(function() {
            fetchUserOrders(); // Load data saat halaman dibuka
            $('#filterBtn').click(fetchUserOrders); // Filter saat tombol diklik
        });
    </script>
</head>
<body class="container mt-4">
    <h2 class="text-center">Selamat Datang, <?= htmlspecialchars($userEmail) ?>!</h2>

    <!-- Form Filter Tanggal -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="startDate" class="form-label">Tanggal Mulai:</label>
            <input type="date" id="startDate" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="endDate" class="form-label">Tanggal Akhir:</label>
            <input type="date" id="endDate" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button id="filterBtn" class="btn btn-primary w-100">Filter</button>
        </div>
    </div>

    <table class="table table-bordered table-striped" id="orderTable">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Tanggal Pembelian</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data akan dimuat secara otomatis melalui AJAX -->
        </tbody>
    </table>
</body>
</html>
