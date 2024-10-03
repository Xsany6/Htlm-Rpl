selesaikan peritungan ini 5+3*7/4-9 modulo 8
7modulo 5+8*4-9/3




<?php


$x = modulo(7,5);
$x = tambah($x, kali(8,4)); 
$y = bagi(9,3);
$z = kurang($x ,$y);
echo $z;

echo "<br>";
// $hsil = tambah (a:5 b: kali(a: 3,b:7));
// $hasil = bagi (a:$hasil,b:4);
// $hasil =kurang(a:$hasil, b:9);
     
    function kali ($A, $B): void {
    $c= $A * $B ;
        echo $c;

    }
     kali(A:250, B:50);
     echo "<br>";
    function tambah ($a,$b):void{
        $C=$a + $b;
        echo "$C";
    }
    tambah(a:13, b:13);
    echo "<br>";
    function kurang():void{
        echo "belajar kurang";
    }
    kurang();
    echo "<br>";
    function bagi ():void{
        echo "belajar bagi";
    }
    bagi();
    echo "<br>";
    function modulo ($a,$b):void{
        $C=$a % $b;
        echo "$C";
    }
    modulo (a:255, b:32)
?>