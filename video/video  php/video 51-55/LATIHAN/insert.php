<form action="" method="post">
    kategori :
    <input type="text" name="kategori">
    <input type="submit" name="simpan" value="simpan">

</form>


<?php

    require_once "../function.php";

    if (isset($_POST['simpan'])) {

        $kategori = $_POST['kategori'];

        $sql = "INSERT INTO tblkategori VALUES ('', '$kategori')";
    
        $result = mysqli_query($koneksi, $sql);
    
        echo "Data Sudah Disimpan";

        header("location: http://localhost/PHPMySQL/restoran/kategori/select.php");
    }
    
    

?>