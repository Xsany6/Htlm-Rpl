<?php 

    session_start();
    require_once "dbcontroller.php";

    $SQL = "SELECT * FROM tblkategori ORDER BY kategori";

    $data_base = new DB;
    $rows = $data_base -> get_all($SQL);

    if (isset($_GET["log"])) {
        session_destroy();
        header("location:index.php");

    }

    function total_items() {
        global $data_base;

        $item = 0;

        foreach ($_SESSION as $key => $value) {
            if ($key != "pelanggan" && $key != "idpelanggan") {
                $id = substr($key, 1);

                $SQL = "SELECT * FROM tblmenu WHERE idmenu = $id";

                $rows = $data_base -> get_all($SQL);

                foreach ($rows as $row) {
                    $item++;

                }

            }

        }

        return $item;

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran SMK | Aplikasi Restoran</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3 mt-4">
                <h2><a href="index.php">Restoran SMK</a></h2>
            </div>

            <div class="col-md-9">
                <?php 
                
                    if (isset($_SESSION["pelanggan"])) {
                        echo "
                            <div class='float-end mt-4'><a href='?log=logout'>Logout</a></div>
                            <div class='float-end mt-4 me-4'>Pelanggan : ".$_SESSION["pelanggan"]."</div>
                            <div class='float-end mt-4 me-4'>Keranjang : (<a href='?folder=home&menu=beli'>".total_items()."</a>)</div>
                            <div class='float-end mt-4 me-4'><a href='?folder=home&menu=history'>History</a></div>
                        ";

                    } else {
                        echo "
                            <div class='float-end mt-4 me-4'><a href='?folder=home&menu=login'>Login</a></div>
                            <div class='float-end mt-4 me-4'><a href='?folder=home&menu=daftar'>Daftar</a></div>
                        ";

                    }
                
                ?>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-3">
                <h3>Kategori</h3>
                <nav class="nav flex-column">
                    <?php if(!empty($rows)) { ?>
                    <?php foreach($rows as $row): ?>
                    <a class="nav-link active" aria-current="page" href="?folder=home&menu=produk&id=<?php echo $row['idkategori'] ?>"><?php echo $row['kategori'] ?></a>
                    <?php endforeach ?>
                    <?php } ?>
                </nav>
            </div>

            <div class="col-md-9">
                <?php 

                    if (isset($_GET["folder"]) && isset($_GET["menu"])) {
                        $folder = $_GET["folder"];
                        $menu = $_GET["menu"];

                        $file = $folder."/".$menu.".php";

                        require_once $file;

                    } else {
                        require_once "home/produk.php";

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