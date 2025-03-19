// Fungsi membuka modal
function openModal() {
    let modal = document.getElementById("inboxModal");
    modal.style.display = "flex";
    document.body.style.overflow = "hidden";
}

// Fungsi menutup modal
function closeModal() {
    let modal = document.getElementById("inboxModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
}

// Fungsi menghapus pesan dari database
function deleteMessage(id, event) {
    event.stopPropagation(); // Mencegah klik dari menandai sebagai terbaca

    if (confirm("Apakah Anda yakin ingin menghapus pesan ini?")) {
        fetch("delete-message.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + id
        })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                event.target.parentElement.remove(); // Hapus dari tampilan
            } else {
                alert("Gagal menghapus pesan!");
            }
        });
    }
}

// Fungsi menandai pesan sebagai sudah dibaca
function markAsRead(id, element) {
    if (!element.classList.contains("unread")) return; // Jika sudah terbaca, tidak perlu diproses

    fetch("mark-as-read.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            element.classList.remove("unread"); // Hapus gaya tidak terbaca
        }
    });
}

// Menutup modal jika klik di luar modal
window.onclick = function(event) {
    let modal = document.getElementById("inboxModal");
    if (event.target === modal) {
        closeModal();
    }
};
function deleteMessage(messageId, event) {
    event.stopPropagation(); // Mencegah event klik pada pesan terbuka saat tombol hapus ditekan

    if (!confirm("Apakah Anda yakin ingin menghapus pesan ini?")) {
        return;
    }

    fetch('delete_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'message_id=' + messageId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Pesan berhasil dihapus!");
            event.target.parentElement.remove(); // Hapus pesan dari tampilan
        } else {
            alert("Gagal menghapus pesan: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
}
function deleteMessage(messageId, event) {
    event.preventDefault();
    event.stopPropagation(); // Mencegah event klik pada pesan terbuka saat tombol hapus ditekan

    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menghapus pesan ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message_id=' + messageId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: "Terhapus!",
                        text: "Pesan berhasil dihapus.",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Hapus elemen pesan dari tampilan
                    const messageElement = event.target.closest('.message-item');
                    if (messageElement) {
                        messageElement.remove();
                    }
                } else {
                    Swal.fire("Gagal", "Tidak dapat menghapus pesan: " + data.error, "error");
                }
            })
            .catch(error => {
                Swal.fire("Error", "Terjadi kesalahan saat menghapus pesan.", "error");
                console.error("Error:", error);
            });
        }
    });
}
