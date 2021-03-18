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

  public function addPiste($user, $dateTime , $cmd , $action) : bool
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

  public function get_last_pistes() : array 
  {
    $request = $this->Db->Pdo->query('SELECT  pist__id__user ,  pist__dt , pist__id__cmd , pist__text , u.nom , u.prenom  FROM pistage  
    LEFT JOIN utilisateur as u ON u.id_utilisateur =  pist__id__user
    ORDER BY pist__dt DESC LIMIT 50 ');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function get_pistes_filtres(array $array_filter) : string 
  {
    $sql_clause = 'WHERE';
    $count = 0 ;
    foreach ($array_filter as $filter) 
    {
      $count ++;
       if ($count > 1 ) 
       {
              $sql_clause .= ' AND ';
       }
      switch ($filter) 
      {
        case (strlen($filter) == 2 and ctype_digit($filter)):
          //c'est un id user: 
          $sql_clause .= ' '.$filter.' = pist__id__user';
          break;

        default:
          //c'est un client ou une commande : 
          $sql_clause .= ' '.$filter.' = pist__id__cmd';
          break;
      }
    }
    
    return $sql_clause;
  }


}