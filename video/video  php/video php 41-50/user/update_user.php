<?php 


    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        
        $SQL = "SELECT * FROM tbluser WHERE iduser = $id";
        $rows = $data_base -> get_item($SQL);

    }

?>
<h3>Update User</h3>
<form action="" method="post" enctype="multipart/form-data">
    <label for="">User</label>
    <input type="text" name="user" id="" required value="<?php echo $rows['user']; ?>" class="form-control w-50 mb-3">
    
    <label for="">Email</label>
    <input type="email" name="email" id="" required value="<?php echo $rows['email']; ?>" class="form-control w-50 mb-3">
    
    <label for="">Password</label>
    <input type="password" name="password" id="" required value="<?php echo $rows['password']; ?>" class="form-control w-50 mb-3">
    
    <label for="">Konfirmasi Password</label>
    <input type="password" name="konfirmasi" id="" required value="<?php echo $rows['password']; ?>" class="form-control w-50 mb-3">
    
    <label for="">Level User</label>
    <select name="level" id="" class="form-select w-50 mb-3">
        <option value="admin" <?php if ($rows["level"] == "admin") echo "selected" ?>>Admin</option>
        <option value="koki" <?php if ($rows["level"] == "koki") echo "selected" ?>>Koki</option>
        <option value="kasir" <?php if ($rows["level"] == "kasir") echo "selected" ?>>Kasir</option>
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
            $SQL = "UPDATE tbluser SET user = '$user', email = '$email', password = '$password', level = '$level' WHERE iduser = $id";
            $data_base -> run_sql($SQL);
            header("location:?folder=user&menu=select");

        } else {
            echo "<h3>Password Tidak Sesusai</h3>";

        }
    }

?>