<?php 

    if (isset($_GET["total"])) {
        $total = $_GET["total"];
        $idorder = idorder();
        $idpelanggan = $_SESSION["idpelanggan"];
        $tanggal = date("y-m-d");

        $SQL = "SELECT * FROM tblorder WHERE idorder = $idorder";

        $count = $data_base -> row_count($SQL);

        if ($count == 0) {
            insert_order($idorder, $idpelanggan, $tanggal, $total);
            insert_order_detail($idorder);
            
        } else {
            insert_order_detail($idorder);

        }

        clear_session();
        header("location:?folder=home&menu=checkout");

    } else {
        info();

    }

    function idorder() {
        global $data_base;

        $SQL = "SELECT idorder FROM tblorder ORDER BY idorder DESC";

        $jumlah = $data_base -> row_count($SQL);

        if ($jumlah == 0) {
            $id = 1;

        } else {
            $item = $data_base -> get_item($SQL);
            $id = $item["idorder"] + 1;

        }

        return $id;

    }

    function insert_order($idorder, $idpelanggan, $tgl, $total) {
        global $data_base;

        $SQL = "INSERT INTO tblorder VALUES ($idorder, $idpelanggan, '$tgl', $total, 0, 0, 0)";

        $data_base -> run_sql($SQL);

    }

    function insert_order_detail($idorder=1) {
        global $data_base;

        foreach ($_SESSION as $key => $value) {
            if ($key != "pelanggan" && $key != "idpelanggan") {
                $id = substr($key, 1);

                $SQL = "SELECT * FROM tblmenu WHERE idmenu = $id";

                $rows = $data_base -> get_all($SQL);

                foreach ($rows as $row) {
                    $idmenu = $row["idmenu"];
                    $harga = $row["harga"];

                    $SQL = "INSERT INTO tblorderdetail VALUES ('', $idorder, $idmenu, $value, $harga)";
                    
                    $data_base -> run_sql($SQL);

                }

            }

        }

    }

    function clear_session() {
        global $data_base;

        foreach ($_SESSION as $key => $value) {
            if ($key != "pelanggan" && $key != "idpelanggan") {
                $id = substr($key, 1);

                unset($_SESSION["_".$id]);

            }

        }

    }

    function info() {
        echo "<h3>Terimakasih telah berbelanja</h3>";

    }

?>