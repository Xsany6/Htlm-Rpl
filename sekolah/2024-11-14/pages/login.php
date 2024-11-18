<div class="login">
    <h1>Login</h1>
    <form action="" method="post">
        <input type="email" name="email" id="" required placeholder="Masukan Email">
        <input type="password" name="password" id="" required placeholder="Masukan Password">
        <input type="submit" name="login" value="login">
    </form>
</div>
<?php
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM costumer WHERE email = '$email' AND password = '$password'";
    // echo $sql;
    $hasil = mysqli_query($koneksi, $sql);
    $baris = mysqli_num_rows($hasil);
    // echo $baris;
    if ($baris > 0) {
        $_SESSION['email'] = $email;
        header("location:index.php");
    }else{
        echo "Email atau Password salah";
    }
}

?>