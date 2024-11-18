<?php

    require_once "../function.php";

    // $id=3;

    $sql ="DELETE FROM tblkategori WHERE idkategori = $id";

    $result = mysqli_query($koneksi, $sql);
    echo $sql;

    header("location: http://localhost/PHPMySQL/restoran/kategori/select.php");
?>