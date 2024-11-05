<?php 

    if (isset($_GET["hapus"])) {
        $id = $_GET["hapus"];

        unset($_SESSION["_".$id]);
        header("location:?folder=home&menu=beli");
        
    }

    if (isset($_GET["tambah"])) {
        $id = $_GET["tambah"];
        $_SESSION["_".$id]++;

    }

    if (isset($_GET["kurang"])) {
        $id = $_GET["kurang"];
        $_SESSION["_".$id]--;

        if ($_SESSION["_".$id] == 0) {
            unset($_SESSION["_".$id]);

        }

    }

    if (!isset($_SESSION["pelanggan"])) {
        header("location:?folder=home&menu=login");

    } else {
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            take_stuff($id);

            header("location:?folder=home&menu=beli");
            
        } else {
            trolley();

        }

    }

    function take_stuff($id) {
        if (isset($_SESSION["_".$id])) {
            $_SESSION["_".$id]++;

        } else {
            $_SESSION["_".$id] = 1;

        }

    }

    function trolley() {
        $total = 0;

        global $data_base;
        global $total;

        echo "
            <table class='table table-bordered w-70'>
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody>
        ";

        foreach ($_SESSION as $key => $value) {
            if ($key != "pelanggan" && $key != "idpelanggan") {
                $id = substr($key, 1);

                $SQL = "SELECT * FROM tblmenu WHERE idmenu = $id";

                $rows = $data_base -> get_all($SQL);

                foreach ($rows as $row) {
                    echo "<tr>";
                    echo "<td>".$row["menu"]."</td>";
                    echo "<td>".$row["harga"]."</td>";
                    echo "<td><a href='?folder=home&menu=beli&tambah=".$row["idmenu"]."'>[+]</a> &nbsp&nbsp".$value."&nbsp&nbsp <a href='?folder=home&menu=beli&kurang=".$row["idmenu"]."'>[-]</a></td>";
                    echo "<td>".$row["harga"] * $value."</td>";
                    echo "<td><a href='?folder=home&menu=beli&hapus=".$row["idmenu"]."'>Hapus</a></td>";
                    echo "</tr>";

                    $total += $value * $row["harga"];

                }

            }

        }

        echo "
                    <tr>
                        <td colspan='4'><h3>Grand Total</h3></td>
                        <td><h3>".$total."</h3></td>
                    </tr>
                </tbody>
            </table>
        ";

    }

    if (!empty($total)) {

?>
<a class="btn btn-primary" href="?folder=home&menu=checkout&total=<?php echo $total ?>" role="button">CHECKOUT</a>
<?php 

    }
    
?>