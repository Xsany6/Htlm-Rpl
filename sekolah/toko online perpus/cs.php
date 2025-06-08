<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: logincs.php");
    exit();
}

// Hapus pesan yang lebih lama dari 5 menit
$conn->query("DELETE FROM chat WHERE waktu < NOW() - INTERVAL 5 MINUTE");

// Ambil data chat dari database
$result = $conn->query("SELECT * FROM chat ORDER BY waktu ASC");
if (!$result) {
    die("Query error: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service Chat</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .chat-container {
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
        }
        .chat-box {
            height: 400px;
            overflow-y: auto;
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        .message {
            max-width: 75%;
            padding: 10px;
            border-radius: 10px;
            word-wrap: break-word;
            margin-bottom: 10px;
        }
        .user {
            background-color: #e9ecef;
            color: black;
            align-self: flex-start;
            text-align: left;
        }
        .admin {
            background-color: #007bff;
            color: white;
            align-self: flex-end;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container chat-container">
    <h2 class="text-center mb-4">Live Chat Customer Service</h2>

    <div class="chat-box" id="chatBox">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="message <?= ($row['pengirim'] == 'admin') ? 'admin' : 'user' ?>">
                <strong><?= htmlspecialchars($row['pengirim']) ?>:</strong><br>
                <?= htmlspecialchars($row['isi']) ?><br>
                <small class="text-muted"><?= $row['waktu'] ?></small>
            </div>
        <?php } ?>
    </div>

    <!-- Input Chat -->
    <form action="send_chat.php" method="POST" class="mt-3">
    <div class="input-group">
        <input type="text" name="pesan" class="form-control" placeholder="Ketik balasan sebagai Admin..." required>
        <button type="submit" class="btn btn-danger">Kirim</button>
    </div>
</form>


 

<!-- Auto-refresh untuk menghapus pesan setiap 5 menit -->
<script>
    setInterval(() => {
        fetch('delete_old_messages.php')
            .then(response => response.text())
            .then(() => location.reload());
    }, 300000); // 5 menit (300000 ms)
</script>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function loadChat() {
        $.ajax({
            url: 'get_chat.php',
            method: 'GET',
            success: function(data) {
                let chatBox = $('#chatBox');
                chatBox.html(''); // Kosongkan sebelum mengisi ulang

                let chats = JSON.parse(data);
                chats.forEach(chat => {
                    let messageClass = (chat.pengirim === 'admin') ? 'admin' : 'user';
                    chatBox.append(`
                        <div class="message ${messageClass}">
                            <strong>${chat.pengirim}:</strong><br>
                            ${chat.isi}<br>
                            <small class="text-muted">${chat.waktu}</small>
                        </div>
                    `);
                });

                // Scroll otomatis ke bawah
                chatBox.scrollTop(chatBox[0].scrollHeight);
            }
        });
    }

    // Jalankan loadChat setiap 2 detik
    setInterval(loadChat, 2000);
    loadChat(); // Load pertama kali saat halaman dibuka

    // Mengirim pesan tanpa reload
    $('form').submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: 'send_chat.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === "success") {
                    $('input[name="pesan"]').val(''); // Kosongkan input setelah mengirim
                    loadChat(); // Refresh chat setelah mengirim pesan
                } else {
                    alert(response.message); // Tampilkan error jika ada
                }
            }
        });
    });
</script>

</html>
