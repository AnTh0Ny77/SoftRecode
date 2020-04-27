<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Article extends Table {

  public string $Table = 'articles2';
  public Database $Db;

  
  public function __construct($db) {
    $this->Db = $db;
}

  public function getAll(){
    $request =$this->Db->Pdo->query('SELECT DISTINCT art_type  FROM articles2 ORDER BY  art_type ASC');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

public function getModels(){
  $request = $this->Db->Pdo->query('SELECT  afmm__id , afmm__modele FROM art_fmm ORDER BY afmm__id ASC ');
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data ; 
}

public function getPn($id){
$request = $this->Db->Pdo->query(
  'SELECT  apn__pn , apn__afmm__id 
   FROM art_pn
   WHERE apn__afmm__id = '.$id.' 
   ORDER BY apn__pn ASC ');
   $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data ; 
}




 
}
