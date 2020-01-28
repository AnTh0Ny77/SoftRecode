<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Keyword extends Table {

  public string $Table = 'keyword';
  public Database $Db;

  
  public function __construct($db) {
    $this->Db = $db;
}



  public function getAll(){
    $request =$this->Db->Pdo->query('SELECT keyword__id, keyword__lib  FROM '.$this->Table.'');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

public function getOne($id){
    $request =$this->Db->Pdo->query("SELECT keyword__id, keyword__lib  FROM " .$this->Table. " WHERE keyword__id = " . $id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
}



 
}