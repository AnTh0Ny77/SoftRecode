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







public function get2_icon(){
  $request =$this->Db->Pdo->query('SELECT  kw__type,  kw__lib , kw__value FROM keyword WHERE kw__type= "i_con" ORDER BY  kw__value ASC ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getPresta(){
  $request =$this->Db->Pdo->query('SELECT  kw__type,  kw__lib , kw__value FROM keyword WHERE kw__type= "pres" ORDER BY  kw__ordre ASC ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getStat(){
  $request =$this->Db->Pdo->query('SELECT kw__type,   kw__lib , kw__value  FROM keyword WHERE kw__type= "stat" AND kw__value <> "IMP" AND kw__value <> "CMD"  ORDER BY kw__ordre ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getEtat(){
  $request =$this->Db->Pdo->query('SELECT kw__type,  kw__lib , kw__value FROM keyword WHERE kw__type= "letat" ORDER BY  kw__ordre  ASC , kw__lib ASC');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getGaranties(){
  $request =$this->Db->Pdo->query('SELECT kw__type,  kw__lib , kw__value FROM keyword WHERE kw__type= "garan" ORDER BY  kw__ordre  ASC ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function getModele(){
  $request =$this->Db->Pdo->query('SELECT kw__type,  kw__lib , kw__value FROM keyword WHERE kw__type= "modvi" ORDER BY  kw__ordre  ASC ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}


public function getTransporteur(){
  $request =$this->Db->Pdo->query('SELECT * FROM keyword WHERE kw__type= "trans" ORDER BY  kw__ordre  ASC ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}





 
}