<?php 

    // require_once "../function.php";

    // if (isset($_GET["update"])) {
    //     $id = $_GET["update"];
    //     require_once("update.php");
    // }

    // if (isset($_GET["hapus"])) {
    //     $id = $_GET["hapus"];
    //     require_once("delete.php");
    // }

    // echo "<br>";

    // $SQL = "SELECT idkategori FROM tblkategori";

    // $result = mysqli_query($koneksi, $SQL);
    // $jumlah = mysqli_num_rows($result);

    // $START = 3;
    // $LENGTH = 3;

    // $AMOUNT = ceil($jumlah/$LENGTH);

    // for ($i = 1; $i <= $AMOUNT; $i++) {
    //     echo "<a href='?p=$i'>".$i."</a>";
    //     echo "&nbsp &nbsp &nbsp";
    // }

    // if (isset($_GET["p"])) {
    //     $p = $_GET["p"];
    //     $start = ($p*$LENGTH) - $LENGTH;
        
    // } else {
    //     $start = 0;
    // }

    // echo "<br> <br>";

    // $SQL = "SELECT * FROM tblkategori LIMIT $start, $LENGTH";

    // $result = mysqli_query($koneksi, $SQL);
    // $jumlah = mysqli_num_rows($result);
    
    // echo "
    //     <table border='1px'>
    //         <tr>
    //             <th>#</th>
    //             <th>kategori</th>
    //             <th>hapus</th>
    //             <th>update</th>
    //         </tr>
    // ";

    // if ($jumlah > 0) {
    //     while ($row = mysqli_fetch_assoc($result)) {
    //         echo "<tr>";
    //         echo "<td>".$row["idkategori"]."</td>";
    //         echo "<td>".$row["kategori"]."</td>";
    //         echo "<td>"."<a href='?hapus=".$row["idkategori"]."'>Hapus</a>"."</td>";
    //         echo "<td>"."<a href='?update=".$row["idkategori"]."'>Update</a>"."</td>";
    //         echo "</tr>";
    //     }
    // }
    
    // echo "</table>";
    
    $jumlah = $data_base -> row_count("SELECT idkategori FROM tblkategori");
    
    $LENGTH = 4;
    $AMOUNT = ceil($jumlah/$LENGTH);
    
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
        $start = ($page*$LENGTH) - $LENGTH;
        
    } else {
        $start = 0;

    }
    
    $SQL = "SELECT * FROM tblkategori ORDER BY kategori ASC LIMIT $start,$LENGTH";
    
    $rows = $data_base -> get_all($SQL);
    $id = 1 + $start;

?>

<div class="float-start me-4">
    <a class="btn btn-primary" href="?folder=kategori&menu=insert" role="button">TAMBAH DATA</a>
</div>
<h3>Kategori</h3>
<table class="table table-bordered w-50">
    <thead>
        <tr>
            <th>#</th>
            <th>Kategori</th>
            <th>Delete</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rows)) { ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $id++ ?></td>
                <td><?php echo $row["kategori"] ?></td>
                <td><a href="?folder=kategori&menu=delete&id=<?php echo $row['idkategori'] ?>">Delete</a></td>
                <td><a href="?folder=kategori&menu=update&id=<?php echo $row['idkategori'] ?>">Update</a></td>
            </tr>
        <?php endforeach ?>
        <?php } ?>
    </tbody>
</table>

<?php 

    for ($i = 1; $i <= $AMOUNT; $i++) {
        echo "<a href='?folder=kategori&menu=select&page=$i'>".$i."</a>";
        echo "&nbsp &nbsp &nbsp";
    }

?>