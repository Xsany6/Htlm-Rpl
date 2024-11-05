<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        $SQL = "SELECT * FROM tblmenu WHERE idmenu = $id";
        $ITEM = $data_base -> get_item($SQL);
        
        $idkategori = $ITEM["idkategori"];
        $gambar = $ITEM["gambar"];

    }

    $rows = $data_base -> get_all("SELECT * FROM tblkategori ORDER BY kategori ASC")

?>
<h3>Update Menu</h3>
<form action="" method="post" enctype="multipart/form-data">
    <label for="">Kategori</label>
    
    <select name="idkategori" id="" class="form-select w-50 mb-3">
        <?php foreach($rows as $row): ?>
        <option <?php if ($idkategori == $row["idkategori"]) echo "selected"; ?> value="<?php echo $row['idkategori'] ?>">
            <?php echo $row["kategori"] ?>
        </option>
        <?php endforeach ?>
    </select>

    <label for="">Nama Menu</label>
    <input type="text" name="menu" id="" required value="<?php echo $ITEM['menu'] ?>" class="form-control w-50 mb-3">

    <label for="">Harga</label>
    <input type="text" name="harga" id="" number required value="<?php echo $ITEM['harga'] ?>" class="form-control w-50 mb-3">
    
    <label for="">Gambar</label>
    <input type="file" name="gambar" id="" class="form-control w-50 mb-3">

    <input type="submit" value="Simpan" name="simpan" class="btn btn-primary mt-3">
</form>

<?php 

    if (isset($_POST["simpan"])) {
        $menu = $_POST["menu"];
        $idkategori = $_POST["idkategori"];
        $harga = $_POST["harga"];
        $gambar = $ITEM["gambar"];
        $temp = $_FILES["gambar"]["tmp_name"];
        
        if (!empty($temp)) {
            $gambar = $_FILES["gambar"]["name"];
            move_uploaded_file($temp, "../gambar/".$gambar);
            
        }

        $SQL = "UPDATE tblmenu SET idkategori = $idkategori, menu = '$menu', gambar = '$gambar', harga = $harga WHERE idmenu = $id";

        $data_base -> run_sql($SQL);
        header("location:?folder=menu&menu=select");
    
    }

?>