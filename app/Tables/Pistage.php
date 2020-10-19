<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Pistage extends Table 
{
  public Database $Db;

  //constructeur
  public function __construct($db) 
  {
    $this->Db = $db;
  }

  public function addPiste($user, $dateTime , $cmd , $action)
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO pistage (pist__id__user , pist__dt , pist__id__cmd , pist__text )
    VALUES (:user, :dt, :cmd, :texte )');
   $request->bindValue(":user", $user);
   $request->bindValue(":dt", $dateTime);
   $request->bindValue(":cmd", $cmd);
   $request->bindValue(":texte", $action);
   $request->execute();
   return true;
  }


}