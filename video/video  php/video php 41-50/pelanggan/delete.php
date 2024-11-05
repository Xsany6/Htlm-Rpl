<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
    }
    
    $SQL = "DELETE FROM tblpelanggan WHERE idpelanggan = $id";
    $data_base -> run_sql($SQL);
    header("location:?folder=pelanggan&menu=select");

?>