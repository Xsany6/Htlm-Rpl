<?php 

    $rows = $data_base -> get_all("SELECT * FROM tbluser ORDER BY user ASC")

?>
<h3>Insert User</h3>
<form action="" method="post" enctype="multipart/form-data">
    <label for="">User</label>
    <input type="text" name="user" id="" required placeholder="Isi user" class="form-control w-50 mb-3">
    
    <label for="">Email</label>
    <input type="email" name="email" id="" required placeholder="Email" class="form-control w-50 mb-3">
    
    <label for="">Password</label>
    <input type="password" name="password" id="" required placeholder="Password" class="form-control w-50 mb-3">
    
    <label for="">Konfirmasi Password</label>
    <input type="password" name="konfirmasi" id="" required placeholder="Password" class="form-control w-50 mb-3">
    
    <label for="">Level User</label>
    <select name="level" id="" class="form-select w-50 mb-3">
        <option value="admin">Admin</option>
        <option value="koki">Koki</option>
        <option value="kasir">Kasir</option>
    </select>

    <input type="submit" value="Simpan" name="simpan" class="btn btn-primary mt-3">
</form>

<?php 

    if (isset($_POST["simpan"])) {
        $user = $_POST["user"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $konfirmasi = $_POST["konfirmasi"];
        $level = $_POST["level"];

        if ($password == $konfirmasi) {
            $SQL = "INSERT INTO tbluser VALUES ('', '$user', '$email', '$password', '$level', 1)";
            $data_base -> run_sql($SQL);
            header("location:?folder=user&menu=select");

        } else {
            echo "<h3>Password Tidak Sesusai</h3>";

        }
    }

?>