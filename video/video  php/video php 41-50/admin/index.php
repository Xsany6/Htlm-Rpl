<?php 

    session_start();

    require_once "../dbcontroller.php";

    $data_base = new DB;

    if (!isset($_SESSION["user"])) {
        header("location:login.php");
    
    }

    if (isset($_GET["log"])) {
        session_destroy();
        header("location:index.php");

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page | Aplikasi Restoran</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h2>Restoran</h2>
            </div>

            <div class="col-md-9">
                <div class="float-end mt-4"><a href="?log=logout">logout</a></div>
                <div class="float-end mt-4 me-4">User : <a href="?folder=user&menu=update_user&id=<?php echo $_SESSION['iduser']; ?>"><?php echo $_SESSION["user"]; ?></a></div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-3">
                <nav class="nav flex-column">
                    <a class="nav-link active" aria-current="page" href="?folder=kategori&menu=select">Kategori</a>
                    <a class="nav-link" href="?folder=menu&menu=select">Menu</a>
                    <a class="nav-link" href="?folder=pelanggan&menu=select">Pelanggan</a>
                    <a class="nav-link" href="?folder=order&menu=select">Order</a> 
                    <a class="nav-link" href="?folder=order_detail&menu=select">Order Detail</a>
                    <a class="nav-link" href="?folder=user&menu=select">User</a> 
                    <!-- <a class="nav-link disabled">Disabled</a> -->
                </nav>
            </div>

            <div class="col-md-9">
                <?php 

                    if (isset($_GET["folder"]) && isset($_GET["menu"])) {
                        $folder = $_GET["folder"];
                        $menu = $_GET["menu"];

                        $file = "../".$folder."/".$menu.".php";

                        require_once $file;
                    }

                ?>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col">
                <p class="text-center">2021 - copyright@zuzuhcorp.com</p>
            </div>
        </div>
    </div>
</body>
</html>