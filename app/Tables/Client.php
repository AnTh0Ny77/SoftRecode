<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Client extends Table {

  public string $Table;
  public Database $Db;
  

  public function __construct($db,$table) {
    $this->Db = $db;
    $this->Table = $table;
}

  public function getAll(){
    $request =$this->Db->Pdo->query('SELECT id_client,  nsoc , adr1 , cp , ville , tel  FROM '.$this->Table.'');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

public function getOne($id){
    $request =$this->Db->Pdo->query("SELECT id_client,  nsoc , adr1 , cp , ville , tel  FROM " .$this->Table. " WHERE id_client = " . $id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
}

 
}