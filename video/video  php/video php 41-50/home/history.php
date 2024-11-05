<?php 

    $email = $_SESSION["pelanggan"];
    $jumlah = $data_base -> row_count("SELECT idorder FROM vorder WHERE email = '$email'");
    
    $LENGTH = 4;
    $AMOUNT = ceil($jumlah/$LENGTH);
    
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
        $start = ($page*$LENGTH) - $LENGTH;
        
    } else {
        $start = 0;

    }
    
    $SQL = "SELECT * FROM vorder WHERE email = '$email' ORDER BY tglorder DESC LIMIT $start,$LENGTH";
    
    $rows = $data_base -> get_all($SQL);
    $id = 1 + $start;

?>
<h3>History Pembelian</h3>
<table class="table table-bordered w-50">
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Total</th>
            <th>detail</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rows)) { ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $id++ ?></td>
                <td><?php echo $row["tglorder"] ?></td>
                <td><?php echo $row["total"] ?></td>
                <td><a href="?folder=home&menu=detail&id=<?php echo $row['idorder'] ?>">Detail</a></td>
            </tr>
        <?php endforeach ?>
        <?php } ?>
    </tbody>
</table>

<?php 

    for ($i = 1; $i <= $AMOUNT; $i++) {
        echo "<a href='?folder=home&menu=history&page=$i'>".$i."</a>";
        echo "&nbsp &nbsp &nbsp";
    }

?>