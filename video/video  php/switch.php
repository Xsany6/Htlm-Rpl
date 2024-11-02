<?php 
$day = 4;

switch ($day) {

case 1:
    echo 'week'; break;
case 2:
    echo 'monday';
    break;

case 3:
    echo 'Tuesday';
    break;

default:
    echo 'day not created yet'; 
    break;
}
 




$option = 'ubah';

switch ($options) {

case 'tambah':
    echo 'anda pilih tambah';
    break;

case 'ubah':
    echo 'anda pilih ubah';
    break;

case 'hapus':
    echo 'anda pilih hapus';

break;

default:
    echo 'pilihan belum ada';
    break;
}
?>