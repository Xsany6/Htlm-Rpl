<?php 

    class DB {
        
        // Property
        public $HOST = "127.0.0.1";
        private $USER = "root";
        private $PASSWORD = "";
        private $DATABASE = "dbrestoran";

        
        public function __construct() {
            echo "construct";

        }

        // Method
        public function select_data() {
            echo "Select Data";

        }

        public function getDataBase() {
            // Perintah this adalah untuk mengakses class property atau variable dalam class
            echo $this -> DATABASE;
            
        }
        
        public function show() {
            // Perintah this adalah untuk mengakses method lain dalam class
            $this -> select_data();

        }
        
        // Fungsi static dapat dipanggil tanpa harus membuat objek
        public static function insert() {
            echo "Static functioan";
        }

    }

    // Calling a static method inside the class
    DB::insert();
    echo "<br>";

    // Initialize properti and method inside the class also create a new object
    $obj_1 = new DB;
    echo "<br>";

    // Calling a method inside the class
    $obj_1 -> select_data();
    echo "<br>";
    
    // Calling a property or variable inside the class (directly)
    echo $obj_1 -> HOST;
    echo "<br>";
    
    // Calling a property or variable inside the class (indirectly)
    $obj_1 -> getDataBase();
    echo "<br>";

    $obj_1 -> show();
    echo "<br>";

?>