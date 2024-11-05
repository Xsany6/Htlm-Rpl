<?php 

    $rows = $data_base -> get_all("SELECT * FROM tblkategori ORDER BY kategori ASC")

?>
<div class="float-start me-4">
    <a class="btn btn-primary" href="?folder=menu&menu=insert" role="button">TAMBAH DATA</a>
</div>
<h3>Menu</h3>
<?php 

    if (isset($_POST["opsi"])) {
        $opsi = $_POST["opsi"];
        $where = "WHERE idkategori = $opsi";

    } else {
        $opsi = 0;
        $where = "";

    }

?>
<div class="my-4">
    <form action="" method="post">
        <select name="opsi" id="" onchange="this.form.submit()">
            <?php foreach($rows as $row): ?>
            <option <?php if ($row["idkategori"] == $opsi) echo "selected"; ?> value="<?php echo $row["idkategori"] ?>">
                <?php echo $row["kategori"] ?>
            </option>
            <?php endforeach ?>
        </select>
    </form>
</div>

<?php 
    
    $jumlah = $data_base -> row_count("SELECT idmenu FROM tblmenu $where");
    
    $LENGTH = 4;
    $AMOUNT = ceil($jumlah/$LENGTH);
    
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
        $start = ($page*$LENGTH) - $LENGTH;
        
    } else {
        $start = 0;

    }
    
    $SQL = "SELECT * FROM tblmenu $where ORDER BY menu ASC LIMIT $start,$LENGTH";
    
    $rows = $data_base -> get_all($SQL);
    $id = 1 + $start;

?>

<table class="table table-bordered w-80">
    <thead>
        <tr>
            <th>#</th>
            <th>menu</th>
            <th>Gambar</th>
            <th>Harga</th>
            <th>Delete</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rows)) { ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $id++ ?></td>
                <td><?php echo $row["menu"] ?></td>
                <td><img style="width: 100px;" src="../gambar/<?php echo $row['gambar'] ?>" alt=""></td>
                <td><?php echo $row["harga"] ?></td>
                <td><a href="?folder=menu&menu=delete&id=<?php echo $row['idmenu'] ?>">Delete</a></td>
                <td><a href="?folder=menu&menu=update&id=<?php echo $row['idmenu'] ?>">Update</a></td>
            </tr>
        <?php endforeach ?>
        <?php } ?>
    </tbody>
</table>

<?php 

    for ($i = 1; $i <= $AMOUNT; $i++) {
        echo "<a href='?folder=menu&menu=select&page=$i'>".$i."</a>";
        echo "&nbsp &nbsp &nbsp";
    }

?>