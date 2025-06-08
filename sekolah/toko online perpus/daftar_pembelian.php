<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
?>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchOrders() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            console.log("Filter Tanggal:", startDate, "sampai", endDate); // Debugging

            $.ajax({
                url: 'fetch_orders.php',
                type: 'GET',
                data: { start_date: startDate, end_date: endDate },
                success: function(data) {
                    $('#orderTable tbody').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        $(document).ready(function() {
            fetchOrders(); // Muat data pertama kali

            $('#filterBtn').click(function () {
                fetchOrders();
            });

            // Event listener untuk perubahan input tanggal agar filter otomatis bekerja
            $('#startDate, #endDate').on('change', function () {
                fetchOrders();
            });
        });
    </script>
</head>
<body class="container mt-4">
    <h2 class="text-center">Daftar Pembelian</h2>

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
                <th>Email User</th>
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
