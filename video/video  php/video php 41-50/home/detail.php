<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

    }

    $jumlah = $data_base -> row_count("SELECT idorderdetail FROM vorderdetail WHERE idorder = $id");
    
    $LENGTH = 4;
    $AMOUNT = ceil($jumlah/$LENGTH);
    
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
        $start = ($page*$LENGTH) - $LENGTH;
        
    } else {
        $start = 0;

    }
    
    $SQL = "SELECT * FROM vorderdetail WHERE idorder = $id ORDER BY idorderdetail DESC LIMIT $start, $LENGTH";
    
    $rows = $data_base -> get_all($SQL);
    $id = 1 + $start;

?>
<h3>Detail Pembelian</h3>
<table class="table table-bordered w-70">
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Menu</th>
            <th>Harga</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rows)) { ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $id++ ?></td>
                <td><?php echo $row["tglorder"] ?></td>
                <td><?php echo $row["menu"] ?></td>
                <td><?php echo $row["harga"] ?></td>
                <td><?php echo $row["jumlah"] ?></td>
            </tr>
        <?php endforeach ?>
        <?php } ?>
    </tbody>
</table>

<?php 

    for ($i = 1; $i <= $AMOUNT; $i++) {
        echo "<a href='?folder=home&menu=detail&id=".$row["idorder"]."&page=$i'>".$i."</a>";
        echo "&nbsp &nbsp &nbsp";
    }

?>