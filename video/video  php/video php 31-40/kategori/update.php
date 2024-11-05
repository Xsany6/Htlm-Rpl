<?php 

    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        $SQL = "SELECT * FROM tblkategori WHERE idkategori = $id";

        $row = $data_base -> get_item($SQL);

    }

?>

<h3>Update Kategori</h3>
<form action="" method="post">
    <label for="">Nama Kategori</label>
    <input type="text" name="kategori" id="" required value="<?php echo $row['kategori'] ?>" class="form-control w-50 mt-3">
    <input type="submit" value="Simpan" name="simpan" class="btn btn-primary mt-3">
</form>

<?php 

    if (isset($_POST["simpan"])) {
        $kategori = $_POST["kategori"];

        $SQL = "UPDATE tblkategori SET kategori = '$kategori' WHERE idkategori = $id";
        
        $data_base -> run_sql($SQL);

        header("location:?folder=kategori&menu=select");

    }

?>