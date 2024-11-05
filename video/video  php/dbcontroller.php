<?php 

    class DB {

        private $HOST = "127.0.0.1";
        private $USER = "root";
        private $PASSWORD = "";
        private $DATABASE = "dbrestoran";
        private $KONEKSI;

        public function __construct() {
            $this -> KONEKSI = $this -> koneksi_database();

        }

        public function koneksi_database() {
            $KONEKSI = mysqli_connect(
                $this -> HOST, $this -> USER, $this -> PASSWORD, $this -> DATABASE
            );
            return $KONEKSI;

        }

        public function get_all($sql) {
            $RESULT = mysqli_query($this -> KONEKSI, $sql);

            while ($row = mysqli_fetch_assoc($RESULT)) {
                $data[] = $row;
            }

            return $data;
        }

        public function get_item($sql) {
            $RESULT = mysqli_query($this -> KONEKSI, $sql);
            $ROW = mysqli_fetch_assoc($RESULT);
            
            return $ROW;
            
        }
        
        public function row_count($sql) {
            $RESULT = mysqli_query($this -> KONEKSI, $sql);
            $COUNT = mysqli_num_rows($RESULT);
            
            return $COUNT;
            
        }
        
        public function run_sql($sql) {
            $RESULT = mysqli_query($this -> KONEKSI, $sql);

        }

        public function pesan($text="") {
            echo $text;
        }
        
    }

    // $obj_1 = new DB;
    
    // $row = $obj_1 -> get_item("SELECT * FROM tblkategori WHERE idkategori = 21");
    // $count = $obj_1 -> row_count("SELECT * FROM tblkategori");

    // echo $row["kategori"];
    // echo $count

    // $obj_1 -> run_sql("INSERT INTO tblkategori VALUES ('', 'Colada')");

    // $obj_1 -> pesan("Hello World")

?>