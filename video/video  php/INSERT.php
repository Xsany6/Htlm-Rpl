<form action="" method="post">
    kategori:
    <input type="text" name="kategori">
    <br>
    <input type="submit" name="save" value="save">
</form>

<?php

require_once "function.php";
if (isset($_POST['simpan'])) {
    $kategori = $_POST ['kategori'];
    $kategori = 'es mambo';
    $sql = "INSERT INTO tblkategori VALUES ('','$kategori')";
    $result = mysqli_query($connection, $sql);
    header("location:http://localhost/php%20dasar%20website/dasar/SELECT%20DATA.php");
    
}
?>