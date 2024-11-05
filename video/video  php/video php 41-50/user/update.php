<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        $rows = $data_base -> get_item("SELECT * FROM tbluser WHERE iduser = $id");

        if ($rows["aktif"] == 0) {
            $aktif = 1;

        } else {
            $aktif = 0;

        }

        $SQL = "UPDATE tbluser SET aktif = $aktif WHERE iduser = $id";
        $data_base -> run_sql($SQL);
        header("location:?folder=user&menu=select");
    }

?>