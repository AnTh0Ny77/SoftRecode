<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Users extends Table {

  public string $Table;
  public Database $Db;
  private object $Request;

  public function __construct($db) {
    $this->Db = $db;
    $this->Table = 'utilisateur';
    $this->Request =$this->Db->Pdo->prepare("SELECT id_utilisateur , prenom , log_nec , nom , icone ,email  FROM  ".$this->Table. " WHERE 
    login=? AND password=? ");
}

  public function getAll(){
      $request =$this->Db->Pdo->query('SELECT * FROM utilisateur');
      $data = $request->fetchAll(PDO::FETCH_CLASS);
      return $data;
  }

  public function getByID($id){
    $request = $this->Db->Pdo->prepare("SELECT * FROM utilistateur WHERE id_utilisateur = ".$id."");
    $data = $request->fetch(PDO::FETCH_CLASS);
    return $data;
  }

  public function login($login,$pass){
    $this->Request->execute(array($login,$pass));
    $this->Request->setFetchMode(PDO::FETCH_OBJ);
    $data = $this->Request->fetch();
    return $data;
  }
}