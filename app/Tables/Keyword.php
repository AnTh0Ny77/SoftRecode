<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Keyword extends Table {

  public string $Table = 'keyword';
  public Database $Db;
  public string  $i_con = 'i_con';
  
  public function __construct($db) {
    $this->Db = $db;
}



public function getAll(){
    $request =$this->Db->Pdo->query('SELECT keyword__id, keyword__lib , keyword__value FROM '.$this->Table.'');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

public function getI_con(){
  $request =$this->Db->Pdo->query('SELECT keyword__id, keyword__lib , keyword__value FROM keyword WHERE keyword__type= "i_con" ORDER BY  keyword__ordre  ASC , keyword__lib ASC');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function get2_icon(){
  $request =$this->Db->Pdo->query('SELECT  kw__type,  kw__lib , kw__value FROM 2_keyword WHERE kw__type= "i_con" ORDER BY  kw__value ASC ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getPresta(){
  $request =$this->Db->Pdo->query('SELECT  kw__type,  kw__lib , kw__value FROM 2_keyword WHERE kw__type= "pres" ORDER BY  kw__ordre ASC ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getStat(){
  $request =$this->Db->Pdo->query('SELECT kw__type,   kw__lib , kw__value  FROM 2_keyword WHERE kw__type= "stat" AND kw__value <> "IMP" AND kw__value <> "CMD"  ORDER BY kw__ordre ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getEtat(){
  $request =$this->Db->Pdo->query('SELECT kw__type,  kw__lib , kw__value FROM 2_keyword WHERE kw__type= "letat" ORDER BY  kw__ordre  ASC , kw__lib ASC');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getOne($id){
    $request =$this->Db->Pdo->query("SELECT keyword__id, keyword__lib  FROM " .$this->Table. " WHERE keyword__id = " . $id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
}



 
}