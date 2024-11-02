<form action="" method="get">
    nama :
    <input type="text" name="nama">
    alamat :
    <input type="text" name="alamat">
    <input type="submit" name="kirim"  value="simpan">
</form>
<?php
        if (isset($_GET['kirim'])) {
            $email = $_GET['email'];
            $password = $_GET['password'];
        
            echo $email;
            echo '<br>';
            echo $password;
        }


?>