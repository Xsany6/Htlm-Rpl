<nav>
    <ul>
        <li><a href="?menu=Isi">Isi</a></li>
        <li><a href="?menu=Hapus">Hapus</a></li>
        <li><a href="?menu=Destroy">Destroy</a></li>
    </ul>
</nav>

<?php
session_start();
if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];

    echo $menu;
    switch ($menu) {
        case 'Isi':
            isisession();
            break;
        case 'Hapus':
            unset($_SESSION['user']);
            break;
        case 'Destroy':
            session_destroy();
            break;
        
        
        default:
            # code...
            break;
    }
}

// var_dump($_SESSION);
function isisession(){
    $_SESSION['user'] = 'joni';
    $_SESSION['nama'] = 'joni rambo';
    $_SESSION['alamat'] = 'sidoarjo';
}
?>