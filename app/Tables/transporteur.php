<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Transporteur extends Table {

  public string $Table = 'transporteur';
  public Database $Db;
  
  
  public function __construct($db) {
    $this->Db = $db;
}

  public function getAll(){
    $request =$this->Db->Pdo->query('SELECT * FROM '.$this->Table.'');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}


 
}