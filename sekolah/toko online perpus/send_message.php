<?php
session_start();
include './db/config.php';

// Ambil daftar pengguna untuk dropdown
$queryUsers = "SELECT id, username FROM users";
$resultUsers = $conn->query($queryUsers);

$successMessage = "";
$errorMessage = "";

// Proses pengiriman pesan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    if (!empty($receiver_id) && !empty($message)) {
        $query = "INSERT INTO messages (sender_id, receiver_id, message, is_read, created_at) VALUES (NULL, ?, ?, 0, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $receiver_id, $message);

        if ($stmt->execute()) {
            $successMessage = "Pesan berhasil dikirim!";
        } else {
            $errorMessage = "Gagal mengirim pesan.";
        }
    } else {
        $errorMessage = "Pilih pengguna dan isi pesan sebelum mengirim.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Pesan ke User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert Library -->
</head>
<body class="container mt-5">

    <h2>Kirim Pesan ke User</h2>
    <form action="send_message.php" method="POST">
        <div class="mb-3">
            <label for="receiver_id" class="form-label">Pilih User:</label>
            <select class="form-select" name="receiver_id" required>
                <option value="">-- Pilih User --</option>
                <?php while ($user = $resultUsers->fetch_assoc()): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Pesan:</label>
            <textarea class="form-control" name="message" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Kirim Pesan</button>
    </form>

    <script>
        <?php if (!empty($successMessage)): ?>
            Swal.fire({
                title: "Berhasil!",
                text: "<?= $successMessage; ?>",
                icon: "success",
                confirmButtonText: "OK"
            }).then(() => {
                window.location = "send_message.php"; // Refresh halaman setelah klik OK
            });
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            Swal.fire({
                title: "Gagal!",
                text: "<?= $errorMessage; ?>",
                icon: "error",
                confirmButtonText: "OK"
            });
        <?php endif; ?>
    </script>

</body>
</html>
