<h3>Menu</h3>
<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        $where = "WHERE idkategori = $id";

        $id = "&id=".$id;

    } else {
        $where = "";
        $id = "";

    }

    $jumlah = $data_base -> row_count("SELECT idmenu FROM tblmenu $where");
    
    $LENGTH = 3;
    $AMOUNT = ceil($jumlah/$LENGTH);
    
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
        $start = ($page*$LENGTH) - $LENGTH;
        
    } else {
        $start = 0;

    }
    
    $SQL = "SELECT * FROM tblmenu $where ORDER BY menu ASC LIMIT $start,$LENGTH";
    
    $rows = $data_base -> get_all($SQL);

?>
<?php if (!empty($rows)) { ?>
<?php foreach ($rows as $row): ?>
    <div class="card" style="width: 18rem; float: left; margin: 10px;">
        <img style="height: 305px; object-fit: cover;" src="gambar/<?php echo $row['gambar'] ?>" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title"><?php echo $row["menu"] ?></h5>
            <p class="card-text"><?php echo $row["harga"] ?></p>
            <a class="btn btn-primary" href="?folder=kategori&menu=insert" role="button">BELI</a>
        </div>
    </div>
<?php endforeach ?>
<?php } ?>
<div style="clear: both;">
<?php 

    for ($i = 1; $i <= $AMOUNT; $i++) {
        echo "<a href='?folder=home&menu=produk&page=".$i.$id."'>".$i."</a>";
        echo "&nbsp &nbsp &nbsp";
        
    }

?>
</div>