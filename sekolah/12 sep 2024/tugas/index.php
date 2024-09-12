<form action="" method="GET">
    <input type="number" name="bil1" id="">
    <input type="number" name="bil2" id="">
    <input type="submit" name= "tambah"value="tambah">
    <input type="submit" name ="kali"value="kali">
    <input type="submit" name ="kurang" value="kurang">
    input
</form>
<?php
if (isset($_GET['tambah'])) {
    $bil1 = $_GET['bil1'];
    $bil2 = $_GET['bil2'];
    $hasil = $bil1+$bil2;
    echo $hasil; 
}

if (isset($_GET["kali"])) {
    $bil1 = $_GET['bil1'];
    $bil2 = $_GET['bil2'];
    $hasil = $bil1*$bil2;
    echo $hasil; 
    # code...
}
if (isset($_GET["bagi"])) {
    $bil1 = $_GET['bil1'];
    $bil2 = $_GET['bil2'];
    $hasil = $bil1%$bil2;
    echo $hasil; 
}
if (isset($_GET["kurang"])) {
    $bil1 = $_GET['bil1'];
    $bil2 = $_GET['bil2'];
    $hasil = $bil1-$bil2;
    echo $hasil; 
}
?>