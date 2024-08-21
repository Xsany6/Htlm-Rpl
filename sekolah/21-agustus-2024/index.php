<?php
require_once "conten.php"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <img src="img/<?=$gambar;?>" alt="">
    <img src="img/<?=$gamabrs[1];?>" alt="">
    <?php
    foreach ($gamabrs as $key) {
        echo "<h1>gambar</h1>";
    echo "<img src='img/$key' alt=''>";
    echo '<img src="img/' . $key .'"alt="" >';
        # code...
    }
    ?>
    
</body>
</html>