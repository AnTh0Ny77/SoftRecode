<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Client extends Table {

  public string $Table;
  public Database $Db;

  
  public function __construct($db,$table) {
    $this->Db = $db;
    $this->Table = $table;
}



  public function getAll(){
    $request =$this->Db->Pdo->query('SELECT client__id, client__societe ,  client__ville   FROM '.$this->Table.'');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

public function getOne($id){
    $request =$this->Db->Pdo->query("SELECT client__id,  client__societe , client__adr1 , client__cp , client__ville , client__tel  FROM " .$this->Table. " WHERE client__id = " . $id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
}

public function insertOne($name , $adresse, $adresse2 , $cp, $ville){
    $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table."(client__societe , client__adr1 , client__adr2, client__cp , client__ville )
     VALUES (:societe, :adr1, :adr2, :cp, :ville)");
    $request->bindValue(":societe", $name);
    $request->bindValue(":adr1", $adresse);
    $request->bindValue(":adr2", $adresse2);
    $request->bindValue(":cp", $cp);
    $request->bindValue(":ville", $ville);
    $request->execute();
    return $this->Db->Pdo->lastInsertId();
}

 
}