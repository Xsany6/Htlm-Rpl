<?php 

require_once "koneksi.php";

$data = stripslashes(file_get_contents("php://input"));
$dataPelanggan = json_decode($data,true);

$idpelanggan = $dataPelanggan['idpelanggan'];
$pelanggan = $dataPelanggan['pelanggan'];
$alamat = $dataPelanggan['alamat'];
$telp = $dataPelanggan['telp'];

if (!empty($pelanggan) and !empty($alamat) and !empty($telp)) {

    $sql = "UPDATE tblpelanggann 
    SET pelanggan = '$pelanggan', 
    alamat = '$alamat', 
    telp = '$telp'
    WHERE idpelanggan = $idpelanggan";

    if ($result = mysqli_query($con,$sql)) {
        echo "Data Sudah Diubah !";
    } else {
        echo "Data Gagal Diubah !";
    }
}else {
    echo "Data Kosong";
}

?>