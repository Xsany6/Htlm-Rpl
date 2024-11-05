<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        $rows = $data_base -> get_item("SELECT * FROM tblpelanggan WHERE idpelanggan = $id");

        if ($rows["aktif"] == 0) {
            $aktif = 1;

        } else {
            $aktif = 0;

        }

        $SQL = "UPDATE tblpelanggan SET aktif = $aktif WHERE idpelanggan = $id";
        $data_base -> run_sql($SQL);
        header("location:?folder=pelanggan&menu=select");
    }

?>