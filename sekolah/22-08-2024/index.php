<?php
require_once "content.php"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="stayle.css">
</head>
<body>


    <div class="header">
        <h1>ini adalah header</h1>
    <?php 
        foreach($pages as $key => $value){
    ?>

            <li><a href="?page=<?= $value?>"><?= $key?></a></li>
    <?php
         }
    ?>
    </div>
    <div class="content">
        <h1>ini adalah content</h1>
        <?php 
            echo $page = $_GET["page"];
            // echo $page;
            if (isset($pages)){
            if ($page == "contact"){
                require_once('pages/contact.php');      
            }
            if ($page == "jurusan"){
                require_once('pages/jurusan.php');      
            }
            if ($page == "prestasi"){
                require_once('pages/prestasi.php');      
            }
            if ($page == "sejarah"){
                require_once('pages/sejarah.php');      
            }
        } else{
            
        }
        ?>
    </div>
    <div class="footer">
        <h1>ini adalah footer</h1>
    </div>
<?php
    foreach ($gambar as $key => $value) {
        ?>
    <img src="img/2.jpeg" alt="">
    <img src="img/<?= $key?>" alt=""srcset="">
        <?php
    }
?>
        <img src="img/1.jpeg" alt="" srcset="">
</body>
</html>