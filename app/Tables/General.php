<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class General extends Table 
{
  public Database $Db;

  //constructeur
  public function __construct($db) 
  {
    $this->Db = $db;
  }

  //fonction générique qui update la colonne voulue dans la table voulue:
  // 1-> table,
  // 2-> data, 
  // 5 -> colone de la condition,
  // 6 -> clause  
  public function updateAll( string $table, $data, string $column, string $condition, string $clause )
  {
    $update = $this->Db->Pdo->prepare
      ('UPDATE  '.$table.'
      SET '. $column .' = ? 
      WHERE '.$condition.' = ?');
        
    $update->execute([$data, $clause]);
  }

  

}