<?php

    class DB{
    // Properti
    public $host = "127.0.0.1";
    private $user = "root";
    private $password =  "";
    private $database = "dbretoran";

    public function __construct(){

    echo 'function construct';
    }

    // Method
    public function selectData(){
        echo 'select data';
    }

    public function getDatabase(){
        return $this->database;
    }

    public function tampil(){
        $this->selectData();
    }

    public static function insertData(){
        echo 'Static function';
    }


    }

    DB ::insertData();


    // $db = new DB;

    // echo '<br>';

    // $db->selectData();

    // echo '<br>';

    // echo $db->host;

    // echo '<br>';

    // echo $db->getDatabase().'<br>';

    // $db ->tampil();




?>