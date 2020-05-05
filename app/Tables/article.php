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
  $famil = 'famil';
  $request = $this->Db->Pdo->query(
  'SELECT afmm__id , afmm__modele, k.kw__lib as famille , m.am__marque as Marque
    FROM art_fmm
    INNER JOIN art_marque as m ON afmm__marque = m.am__id
    INNER JOIN 2_keyword as k on afmm__famille = k.kw__value 
    order by k.kw__ordre ASC, afmm__modele ASC');

  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data ; 
}



public function getPn($id){
$request = $this->Db->Pdo->query(
  'SELECT  apn__pn , apn__afmm__id  , apn__desc_short , apn__pn_long
   FROM art_pn
   WHERE apn__afmm__id = '.$id.' 
   ORDER BY apn__pn ASC ');
   $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data ; 
}




 
}
