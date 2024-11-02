<?php
    // Array Dimensi

    // $nama = array("joni", "terjo", "budi", "siti", 100, 2.5);

    // var_dump($nama);

    // echo "<br>";

    // echo $nama[5];

    // echo "<br>";

    // for ($i=0; $i < 6; $i++) { 
    //     echo $i;
    //     echo $nama[$i]."<br>";
    // }

    // foreach ($nama as $k) {
    //     echo $k. '<br>';
    // }

    // Array asosiatif
    // $nama = array(
    //     "joni" => "Surabaya",
    //     "budi" => "malang",
    //     "teji" => "jakarta",
    //     "siti" => "sidoarjo",
    // );

    $nama["joni"]="surabaya";
    $nama["budi"]="malang raya";
    $nama["tejo"]="jakarta";
    $nama["siti"]="sidoarjo";
    $nama["edi"]="semarang";


    var_dump($nama);
    echo "<br>";

    foreach ($nama as $k => $v) {
        echo $k. " => ".$v;

        echo "<br>";
    }


?>
