<div style="margin:auto; width:900px;">
<?php 
    require_once "../function.php";
    $sql = "SELECT idkategori* FROM tblkategori";
    $result = mysqli_query($connection, $sql);
    $jumlahdata = mysqli_num_rows($result);
    
    $banyak = 4;
    
    $halaman = ceil ($jumlahdata / $banyak);
    for ($i=1; $i <= $halaman; $i++) { 
        echo '<a href="?p='.$i.'">'.$i.'</a>';
        echo '&nbsp &nbsp &nbsp';
    }
    echo '<br>'; 
    echo '<br>'; 
    
    if (isset($_GET['p'] )) {
        $p=$_GET['p'];
        echo $p;
        $mulai = ($p*$banyak)-$banyak;
        
    }else{
        $mulai = 3;
        
    }
    
    $sql = "SELECT * FROM tblkategori";

    $result = mysqli_query($connection, $sql);

    // var_dump($result);

    $number = mysqli_num_rows($result);
    // echo '<br>';
    // echo $jumlah;
    echo '<table border="1px">
    <tr>
        <th>Nomer</th>
        <th>kategori</th>
    </tr>';
    $no=$mulai+1;
    if ($jumlah) {
        while ($row = mysqli_fetch_assoc( $result)) {
            echo '<tr>';
            echo '<td>'. $row ['idkategori'].'</td>';
            echo '<td>'. $row ['kategori'].'</td>';
            echo '</tr>';
           
        }
    }
echo '</table>';
?>


    <h1>belajar</h1>


</div>