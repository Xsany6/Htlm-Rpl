<?php
require '../db/config.php';

$stmt = $pdo->prepare("SELECT * FROM posts WHERE category_id = 4");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hiburan</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h1>Berita Hiburan</h1>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <small><?= $post['created_at'] ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
