<?php
for ($i = 0; $i <= 10; $i++) {
    echo $i;
}

echo "<br>";
echo "<br>";

for ($i = 10; $i >= 0; $i--) {
    echo $i;
}

echo "<br>";
echo "<br>";

$ganjil = 5 % 3;
echo $ganjil;

echo "<br>";
echo "<br>";

for ($i = 1; $i < 100; $i++) {
    $ganjil = $i % 2;
    // echo $ganjil;
    if ($ganjil == 1) {
        echo $i;
    }
}

echo "<br>";
echo "<br>";


for ($i = 1; $i < 100; $i++) {
    $ganjil = $i % 2;
    // echo $ganjil;
    if ($ganjil == 0) {
        echo $i;
    }
}

echo "<br>";
echo "<br>";

$kkn = 80;
$nilai = 95;
if ($nilai > $kkn) {
    echo "lulus";
} else {
    echo "tidak lulus";
}

echo "<br>";
echo "<br>";

$status = "tidak lulus";

if ($nilai > $kkn) {
    $status = "lulus";
}

echo $status;


echo "<br>";
echo "<br>";

$raport = 0;
$tugas = 1;

if ($tugas == 1) {
    $raport = 80;
}

echo $raport;


echo "<br>";
echo "<br>";

$bulan = 8;
$tanggal = 15;
$keterangan = "salah";

if ($bulan > 0 && $bulan < 13) {
    // $keterangan = "benar";
    if ($tanggal > 0 && $tanggal < 32) {
        // $keterangan = "benar";
        if ($bulan == 3 && $tanggal > 20 && $tanggal < 32) {
            $keterangan = "Aries";
        }
        if ($bulan == 4 && $tanggal > 0 && $tanggal < 20) {
            $keterangan = "Aries";
        }

        if ($bulan == 4 && $tanggal > 19 && $tanggal < 31) {
            $keterangan = "Taurus";
        }
        if ($bulan == 5 && $tanggal > 0 && $tanggal < 21) {
            $keterangan = "Taurus";
        }

        if ($bulan == 5 && $tanggal > 20 && $tanggal < 32) {
            $keterangan = "Gemini";
        }
        if ($bulan == 6 && $tanggal > 0 && $tanggal < 21) {
            $keterangan = "Gemini";
        }

        if ($bulan == 6 && $tanggal > 20 && $tanggal < 31) {
            $keterangan = "Cancer";
        }
        if ($bulan == 7 && $tanggal > 0 && $tanggal < 23) {
            $keterangan = "Cancer";
        }

        if ($bulan == 7 && $tanggal > 22 && $tanggal < 32) {
            $keterangan = "Leo";
        }
        if ($bulan == 8 && $tanggal > 0 && $tanggal < 23) {
            $keterangan = "Leo";
        }

        if ($bulan == 8 && $tanggal > 22 && $tanggal < 32) {
            $keterangan = "Virgo";
        }
        if ($bulan == 9 && $tanggal > 0 && $tanggal < 23) {
            $keterangan = "Virgo";
        }

        if ($bulan == 9 && $tanggal > 22 && $tanggal < 31) {
            $keterangan = "Libra";
        }
        if ($bulan == 10 && $tanggal > 0 && $tanggal < 23) {
            $keterangan = "Libra";
        }

        if ($bulan == 10 && $tanggal > 22 && $tanggal < 32) {
            $keterangan = "Scorpio";
        }
        if ($bulan == 11 && $tanggal > 0 && $tanggal < 22) {
            $keterangan = "Scorpio";
        }

        if ($bulan == 11 && $tanggal > 23 && $tanggal < 31) {
            $keterangan = "Sagitarius";
        }
        if ($bulan == 12 && $tanggal > 0 && $tanggal < 22) {
            $keterangan = "Sagitarius";
        }

        if ($bulan == 12 && $tanggal > 21 && $tanggal < 32) {
            $keterangan = "Capricorn";
        }
        if ($bulan == 1 && $tanggal > 0 && $tanggal < 20) {
            $keterangan = "Capricorn";
        }

        if ($bulan == 1 && $tanggal > 19 && $tanggal < 32) {
            $keterangan = "Aquarius";
        }
        if ($bulan == 2 && $tanggal > 0 && $tanggal < 19) {
            $keterangan = "Aquarius";
        }

        if ($bulan == 2 && $tanggal > 18 && $tanggal < 31) {
            $keterangan = "Pisces";
        }
        if ($bulan == 3 && $tanggal > 0 && $tanggal < 21) {
            $keterangan = "Pisces";
        }
    }
}

echo $keterangan;