<?php 
    require_once "../function.php";
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
    $no=1;
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