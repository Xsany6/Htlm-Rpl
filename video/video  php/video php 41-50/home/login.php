<div class="col-4 mt-4">
    <h2>LOGIN RESTORAN</h2>
    <form action="" method="post">
        <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-3 col-form-label">Email</label>
            <div class="col-sm-9">
                <input type="email" name="email" class="form-control" id="inputEmail3">
            </div>
        </div>
        <div class="row mb-3">
            <label for="inputPassword3" class="col-sm-3 col-form-label">Password</label>
            <div class="col-sm-9">
                <input type="password" name="password" class="form-control" id="inputPassword3">
            </div>
        </div>
        <button type="submit" name="login" class="btn btn-primary ms-auto mb-3">Login</button>
    </form>
</div>
<?php 

    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $SQL = "SELECT * FROM tblpelanggan WHERE email = '$email' AND password =  '$password' AND aktif = 1";
        $COUNT = $data_base -> row_count($SQL);

        if ($COUNT == 0) {
            echo "<center><h3>User Tidak Ditemukan</h3></center>";

        } else {
            $rows = $data_base -> get_item($SQL);

            $_SESSION["pelanggan"] = $rows["email"];
            $_SESSION["idpelanggan"] = $rows["idpelanggan"];

            header("location:index.php");

        }

    }

?>