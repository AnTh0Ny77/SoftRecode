<?php

namespace App;

use PDO;
use PDOException;

class Totoro {

   public string  $db_Name;
   private string $db_user;
   private string $db_pass;
   private string $db_host;
   public PDO $Pdo;


    public function __construct($db_name='euro',$db_user='intranet',$db_pass='yenapa',$db_host='192.168.1.133')
    {

        $this->db_Name = $db_name;
        $this->$db_pass = $db_pass;
        $this->$db_user = $db_user;
        $this->db_host = $db_host;
        
    }

    public function DbConnect(){

        if(!isset($this->Pdo)){
            try {
                $pdo = new PDO('mysql:dbname=euro;host=192.168.1.133', 'intranet', 'yenapa' ,  array(1002 => 'SET NAMES utf8'));
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->Pdo = $pdo;
                return $this->Pdo;
                } 
                catch (PDOException $e) {
                echo $e->getMessage() . "impossible de se connecter à la base de donnée";
                }
        }
       
    }


   
}