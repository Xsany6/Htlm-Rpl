<?php
    class DB{
    private $host = "127.0.0.1";
    private $user = "root";
    private $password =  "";
    private $database = "dbretoran";
    private $koneksi;

    public function __construct() {
        
    $this->koneksi = $this->koneksiDB();
    }

    public function koneksiDB(){
    $koneksi = mysqli_connect($this->host, $this->user, $this->password, $this->database);
    return $koneksi;    
    }

    public function getALL($sql){
    $result = mysqli_query($this->koneksi, $sql);
    while ($row=mysqli_fetch_assoc($result)) {
            $data[]=$row;   
    }

    return $data;
    }

    public function getItem($sql){
        $result = mysqli_query($this->koneksi, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    public function rowCOUNT($sql){
    $result = mysqli_query($this->koneksi, $sql); 
    $count = mysqli_num_rows($result);

    return $count;
    }

    public function runSQL($sql){
    $result = mysqli_query($this->koneksi, $sql);
    }

    public function pesan($text = ""){
    echo $text; 
    }

    }

    // $db = new DB;

    // $db->pesan("berhasil");

    // $db->runSQL("INSERT INTO tblkategori VALUES ('','Minuman')");
    // $db->runSQL("DELETE FROM tblkategori WHERE idkategori = 25");

    // $count = $db->rowCOUNT("SELECT * FROM tblkategori");

    // echo $count;
    
    // $row = $db->getItem("SELECT * FROM tblkategori WHERE idkategori = 14");

    // var_dump($row);

    // foreach ($row as $key) {
    //     echo $key['kategori'].'<br>';
    // }

    // echo $row['kategori'];


?>