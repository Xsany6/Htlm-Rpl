<?php 
// $name = array('tejo', 'budi', 'siti', 100);
// var_dump($name);
// echo '<br>';
// foreach ($nama as $key) {
    // echo $key.'<br>';
// }

$name = array(
    "tejo" => "surabaya",
    "budi" => "malang",
    "siti" => "sidoarjo"
);

var_dump($name);
echo '<br>';
foreach ($name as $a => $b) {
    echo $a.'-'.$b;
    echo '<br>';
}


?>