<?php 

    $rows = $data_base -> get_all("SELECT * FROM tblkategori ORDER BY kategori ASC")

?>
<h3>Insert Menu</h3>
<form action="" method="post" enctype="multipart/form-data">
    <label for="">Kategori</label>
    
    <select name="idkategori" id="" class="form-select w-50 mb-3">
        <?php foreach($rows as $row): ?>
        <option value="<?php echo $row['idkategori'] ?>">
            <?php echo $row["kategori"] ?>
        </option>
        <?php endforeach ?>
    </select>

    <label for="">Nama Menu</label>
    <input type="text" name="menu" id="" required placeholder="Isi menu" class="form-control w-50 mb-3">

    <label for="">Harga</label>
    <input type="text" name="harga" id="" number required placeholder="Isi harga" class="form-control w-50 mb-3">
    
    <label for="">Gambar</label>
    <input type="file" name="gambar" id="" class="form-control w-50 mb-3">

    <input type="submit" value="Simpan" name="simpan" class="btn btn-primary mt-3">
</form>

<?php 

    if (isset($_POST["simpan"])) {
        $menu = $_POST["menu"];
        $idkategori = $_POST["idkategori"];
        $harga = $_POST["harga"];
        $gambar = $_FILES["gambar"]["name"];
        $temp = $_FILES["gambar"]["tmp_name"];
    
        if (empty($gambar)) {
            echo "<h3>Gambar Kosong</h3>";

        } else {
            $SQL = "INSERT INTO tblmenu VALUES ('', $idkategori, '$menu', '$gambar', $harga)";

            move_uploaded_file($temp, "../gambar/".$gambar);
            $data_base -> run_sql($SQL);
            header("location:?folder=menu&menu=select");

        }
    
    }

?>