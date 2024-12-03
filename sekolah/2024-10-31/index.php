<?php 
    $identitas = ["nama"=>"rizkyan dwi fahrizah","alamat"=>"sidoarjo","tlp"=>"0981228424","email"=>"ff723o89@gmail.com","tioktok"=>"noname"];
    $sekolah =["tk"=>"sabililfala","sd"=>"kebonagung 2","smp"=>"smpn 1 candi","smk"=>"smkn 2 buduran"];
    $hobi = ["tidur","tidur","tidur","turu","tilem"];
    $skil = ["c++"=>"expert","Html"=>"newbie","css"=>"intermediete","php"=>"newbie"];
    $deskripsi_singkat= "saya adalah seorang yang suka bermain tetang oprasi sistem pada perangkat lunak.";
    $deskripsi         ="saya adalah siswa dari smkn 2 buduran dengan mengambil <h2>Rekayasa perangkat lunak</h2>
                         saya suka bermain sistem oprasi pada sebuah perangkat lunak, dengan ini saya suka mengerjakan pada malam hari 
                         dengan mendengarkan sebuh music favorit saya, tetapi saya suka mengerjakan tampa ada gangguan atau suara yang menggangu";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar riwayat hidup</title>
    <style>
        
        .container {
            width: 800px;
            margin: auto;
            /* display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center; */
        }

        /* table {
            margin: 10px auto;
            border-collapse: collapse;
            width: 80%;
        }

        th, td {
            padding: 8px;
            border: 1px solid #000;
        } */
    </style>
</head>
<body>
    <div class="container">
        <h1>Daftar Riwayat Hidup</h1>
        <h2>Data Diri </h2>
        <table border="1px">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Deskripsi</th>
                </tr>
                <tbody>
                    <?php 
                        foreach ($identitas as $key => $value) {
                            ?>
                            <tr>
                                <td><?= $key ?></td>
                                <td><?= $value?></td>
                            </tr>
                            <?php
                        }
                    ?>
                </tbody>
            </thead>
        </table>
        <hr>
        <h2>Riwayat Pendidikan</h2>
            <table border="1px">
                <thead>
                    <tbody>
                        <tr>
                            <th>Pendidikan</th>
                            <th>Nama Sekolah</th>
                        </tr>
                        <?php 
                            foreach ($sekolah as $key => $value) {
                                ?>
                                <tr>
                                    <td><?= $key ?></td>    
                                    <td><?= $value ?></td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </thead>
            </table>
            <hr>
            <h2>skil coding</h2>
            <table border="1px">
                <thead>
                    <tbody>
                        <tr>
                            <th>skil</th>
                            <th>Level</th>
                        </tr>
                        <?php 
                            foreach ($skil as $key => $value) {
                                ?>
                                <tr>
                                    <td><?= $key?></td>
                                    <td><?= $value?></td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </thead>
            </table>
            <hr>
            <h2>hoby</h2>
                <ul>
            <?php 
                foreach ($hobi as $key) {
                    ?>
                        <li><?= $key ?></li>
            <?php
                }
                ?></ul>
            <hr>
            <h2>tetang aku</h2>
            <p><?= $deskripsi ?></p>
        </div>
</body>
</html>