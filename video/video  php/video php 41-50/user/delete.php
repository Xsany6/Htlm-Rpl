<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
    }
    
    $SQL = "DELETE FROM tbluser WHERE iduser = $id";
    $data_base -> run_sql($SQL);
    header("location:?folder=user&menu=select");

?>