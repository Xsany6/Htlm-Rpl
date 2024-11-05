<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        $SQL = "DELETE FROM tblmenu WHERE idmenu = $id";

        $data_base -> run_sql($SQL);
    
    }

    header("location:?folder=menu&menu=select");

?>