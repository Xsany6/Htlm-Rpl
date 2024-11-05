<h3>Insert Kategori</h3>
<form action="" method="post">
    <label for="">Nama Kategori</label>
    <input type="text" name="kategori" id="" required placeholder="Isi Kategori" class="form-control w-50 mt-3">
    <input type="submit" value="Simpan" name="simpan" class="btn btn-primary mt-3">
</form>
<?php 

    if (isset($_POST["simpan"])) {
        $kategori = $_POST["kategori"];
    
        $SQL = "INSERT INTO tblkategori VALUES ('', '$kategori')";
    
        $data_base -> run_sql($SQL);
    
        header("location:?folder=kategori&menu=select");
    }

?>