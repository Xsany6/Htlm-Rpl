<?php 
    
    $jumlah = $data_base -> row_count("SELECT idpelanggan FROM tblpelanggan");
    
    $LENGTH = 4;
    $AMOUNT = ceil($jumlah/$LENGTH);
    
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
        $start = ($page*$LENGTH) - $LENGTH;
        
    } else {
        $start = 0;

    }
    
    $SQL = "SELECT * FROM tblpelanggan ORDER BY pelanggan ASC LIMIT $start,$LENGTH";
    
    $rows = $data_base -> get_all($SQL);
    $id = 1 + $start;

?>

<div class="float-start me-4">
    <a class="btn btn-primary" href="?folder=pelanggan&menu=insert" role="button">TAMBAH DATA</a>
</div>
<h3>Pelanggan</h3>
<table class="table table-bordered w-80">
    <thead>
        <tr>
            <th>#</th>
            <th>Pelanggan</th>
            <th>Alamat</th>
            <th>Telephone</th>
            <th>Email</th>
            <th>Delete</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php 
                
                    if ($row["aktif"] == 1) {
                        $status = "AKTIF";

                    } else {
                        $status = "TIDAK AKTIF";

                    }

                ?>
                <td><?php echo $id++ ?></td>
                <td><?php echo $row["pelanggan"] ?></td>
                <td><?php echo $row["alamat"] ?></td>
                <td><?php echo $row["telp"] ?></td>
                <td><?php echo $row["email"] ?></td>
                <td><a href="?folder=pelanggan&menu=delete&id=<?php echo $row['idpelanggan'] ?>">Delete</a></td>
                <td><a href="?folder=pelanggan&menu=update&id=<?php echo $row['idpelanggan'] ?>"><?php echo $status ?></a></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php 

    for ($i = 1; $i <= $AMOUNT; $i++) {
        echo "<a href='?folder=pelanggan&menu=select&page=$i'>".$i."</a>";
        echo "&nbsp &nbsp &nbsp";
    }

?>