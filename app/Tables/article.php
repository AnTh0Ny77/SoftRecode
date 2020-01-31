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




 
}
