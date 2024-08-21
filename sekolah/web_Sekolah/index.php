<?php
    require 'web_projek.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>web projek</title>
</head>
<body>
    <h1><?=$judul?></h1>
    <p><?=$isi?></p>
    <hr>
    <h1>jadwal Rpl tahun 2024/2025</h1>
    <table border="1">
        <tr>
            <th>
                <th><?= $hari[0];?></th>
                <th><?= $hari [1];?></th>
                <th><?= $hari[2];?></th>
                <th><?= $hari [3];?></th>
                <th><?= $hari [4];?></th>
            </th>
        </tr>
            <th>
                <th><?= $jawal[0];?></th>
                <th><?= $jawal[1];?></th>
                <th><?= $jawal[2];?></th>
                <th><?= $jawal[3];?></th>
                <th><?= $jawal[4];?></th>
            </th>

    </table>
        <hr>
    <img src="img/<?= $gambar[1];?>" alt=""; 


</body>
</html>