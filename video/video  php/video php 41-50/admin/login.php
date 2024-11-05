<?php 

    session_start();

    require_once "../dbcontroller.php";

    $data_base = new DB;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <title>Login Restoran</title>
</head>
<body>
    <div class="container">
        <div class="col-4 mx-auto mt-4">
            <h1>LOGIN RESTORAN</h1>
            <form action="" method="post">
                <div class="row mb-3">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" name="email" class="form-control" id="inputEmail3">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" name="password" class="form-control" id="inputPassword3">
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary ms-auto mb-3">Sign in</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php 

    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $SQL = "SELECT * FROM tbluser WHERE email = '$email' AND password =  '$password'";
        $COUNT = $data_base -> row_count($SQL);

        if ($COUNT == 0) {
            echo "<center><h3>User Tidak Ditemukan</h3></center>";

        } else {
            $rows = $data_base -> get_item($SQL);

            $_SESSION["user"] = $rows["email"];
            $_SESSION["level"] = $rows["level"];
            $_SESSION["iduser"] = $rows["iduser"];

            header("location:index.php");

        }

    }

?>