<?php

require_once "function.php";

//$id=3;

$sql = "DELETE FROM tblkategori WHERE idkategori = $id";

$result = mysqli_query($koneksi, $sql);

header("location:http://localhost/php%20dasar%20website/dasar/SELECT%20DATA.php")

?>
