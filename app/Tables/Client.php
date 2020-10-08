<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Client extends Table {

  public string $Table = 'client';
  public Database $Db;

  
  public function __construct($db) {
    $this->Db = $db;
}

  public function getAll(){
    $request =$this->Db->Pdo->query('SELECT  LPAD(client__id,6,0) as client__id, client__societe ,  client__ville , client__cp  FROM client WHERE client__id  > 10 ORDER BY client__societe DESC LIMIT 150000');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

public function getOne($id){
    $request =$this->Db->Pdo->query("SELECT LPAD(client__id,6,0) as client__id,  client__societe , client__adr1 , client__adr2, client__cp , client__ville , client__tel , client__tva_intracom , client__id_vendeur  FROM " .$this->Table. " WHERE client__id = " . $id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
}

public function insertOne($name , $adresse, $adresse2 , $cp, $ville){
    $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table."(client__societe , client__adr1 , client__adr2, client__cp , client__ville )
     VALUES (:societe, :adr1, :adr2, :cp, :ville)");
    $request->bindValue(":societe", strtoupper($name));
    $request->bindValue(":adr1", $adresse);
    $request->bindValue(":adr2", $adresse2);
    $request->bindValue(":cp", $cp);
    $request->bindValue(":ville", $ville);
    $request->execute();
    return $this->Db->Pdo->lastInsertId();
}

public function getSpecials()
{
  $request =$this->Db->Pdo->query("SELECT LPAD(client__id,6,0) as client__id,  
  client__societe , client__adr1 , client__adr2, client__cp , client__ville , client__tel , client__tva_intracom  
  FROM " .$this->Table. " WHERE client__id  < 10 ");
  $data = $request->fetchALL(PDO::FETCH_OBJ);
  return $data;
}

 
}