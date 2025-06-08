<?php
include 'db/config.php';

// Ambil kata kunci pencarian dari parameter GET
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($searchQuery !== '') {
    // Query untuk mencari produk sesuai dengan kata kunci
    $query = "SELECT * FROM products WHERE name LIKE ?";
    $stmt = $conn->prepare($query);
    $likeQuery = "%" . $searchQuery . "%";
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Menampilkan hasil pencarian
        while ($row = $result->fetch_assoc()) {
            echo '<div class="result-item" onclick="selectResult(' . $row['id'] . ', \'' . addslashes($row['name']) . '\')">';
            echo '<strong>' . htmlspecialchars($row['name']) . '</strong>';
            echo '</div>';
        }
    } else {
        echo '<div class="result-item">No results found</div>';
    }
}
?>
