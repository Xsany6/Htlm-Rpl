<!-- update data -->
<?php

    require_once "../function.php";

    $sql = "SELECT * FROM tblkategori WHERE idkategori = $id";
    $result = mysqli_query($koneksi, $sql);
    $row = mysqli_fetch_assoc($result);

    // echo $row['kategori'];
    // $kategori = 'Jelly Bean';
    // $id = 18;
    // $kategori = '';
    // $sql = "UPDATE tblkategori SET kategori='$kategori' WHERE idkategori= $id";

    // $result = mysqli_query($koneksi, $sql);
    // echo $sql;

?>

<form action="" method="post">
    kategroi :
    <input type="text" name="kategori" value="<?php echo $row['kategori']?>">
    <input type="submit" name="simpan" value="simpan">
</form>

<?php

    if (isset($_POST['simpan'])) {
        $kategori = $_POST['kategori'];
        $sql = "UPDATE tblkategori SET kategori='$kategori' WHERE idkategori = $id";

        $result = mysqli_query($koneksi, $sql);
        echo $sql;
        header("location:http://localhost/PHPMySQL/restoran/kategori/select.php");
    }

?>