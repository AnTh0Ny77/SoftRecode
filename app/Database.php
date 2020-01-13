<?php

namespace App;

use PDO;
use PDOException;

class Database{
    private string $db_name;
    private string $db_user;
    private string $db_pass;
    private string $db_host;
    private PDO $pdo;


    private function __construct($db_name,$db_user='root',$db_pass='root',$db_host='localhost' ){
        $this->db_name = $db_name;
        $this->$db_pass = $db_pass;
        $this->$db_user = $db_user;
        $this->db_host = $db_host;
    }

    private function getPDO(){
        if ($this->pdo == null) {
            try {
                $pdo = new PDO('mysql:dbname=devisrecode1;host=localhost', 'root', 'root');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo = $pdo;
                } 
                catch (PDOException $e) {
               echo $e->getMessage();
                }
        }
       return $this->pdo;
    }
}