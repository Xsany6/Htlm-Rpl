<?php 

    $rows = $data_base -> get_all("SELECT * FROM tbluser ORDER BY user ASC")

?>
<h3>Regristrasi Pelanggan</h3>
<form action="" method="post" enctype="multipart/form-data">
    <label for="">Pelanggan</label>
    <input type="text" name="pelanggan" id="" required placeholder="Isi Pelanggan" class="form-control w-50 mb-3">
    
    <label for="">Alamat</label>
    <input type="text" name="alamat" id="" required placeholder="Isi Alamat" class="form-control w-50 mb-3">
    
    <label for="">Telephone</label>
    <input type="tel" name="telp" id="" required placeholder="Isi Telephone" class="form-control w-50 mb-3">
    
    <label for="">Email</label>
    <input type="email" name="email" id="" required placeholder="Email" class="form-control w-50 mb-3">
    
    <label for="">Password</label>
    <input type="password" name="password" id="" required placeholder="Password" class="form-control w-50 mb-3">
    
    <label for="">Konfirmasi Password</label>
    <input type="password" name="konfirmasi" id="" required placeholder="Password" class="form-control w-50 mb-3">

    <input type="submit" value="Simpan" name="simpan" class="btn btn-primary mt-3">
</form>

<?php 

    if (isset($_POST["simpan"])) {
        $pelanggan = $_POST["pelanggan"];
        $alamat = $_POST["alamat"];
        $telp = $_POST["telp"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $konfirmasi = $_POST["konfirmasi"];

        if ($password == $konfirmasi) {
            $SQL = "INSERT INTO tblpelanggan VALUES ('', '$pelanggan', '$alamat', '$telp', '$email', '$password', 1)";
            $data_base -> run_sql($SQL);
            header("location:?folder=home&menu=info");

        } else {
            echo "<h3>Password Tidak Sesusai</h3>";

        }
    }

?>