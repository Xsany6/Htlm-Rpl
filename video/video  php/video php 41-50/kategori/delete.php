<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        $SQL = "DELETE FROM tblkategori WHERE idkategori = $id";

        $data_base -> run_sql($SQL);
    
    }

    header("location:?folder=kategori&menu=select");

?>