<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Contact extends Table {

  public string $Table = 'contact';
  public Database $Db;

  public function __construct($db) {
    $this->Db = $db;
}

public function getFromLiaison($idClient){
    $request =$this->Db->Pdo->query("SELECT contact__id,  contact__nom , contact__prenom , contact__fonction , k.kw__lib 
    FROM contact AS c 
    INNER JOIN liaison_client_contact AS l ON c.contact__id = l.liaison__contact__id 
    JOIN keyword as k ON contact__fonction = k.kw__value AND k.kw__type = 'i_con'
    WHERE l.liaison__client__id =".$idClient."");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

public function get_contact_search($idClient , int $limit)
{
    $request = $this->Db->Pdo->query("SELECT contact__id,  contact__nom , contact__prenom , 
    contact__fonction , k.kw__lib , contact__civ , contact__telephone , contact__gsm , contact__email 
    FROM contact AS c 
    INNER JOIN liaison_client_contact AS l ON c.contact__id = l.liaison__contact__id 
    JOIN keyword as k ON contact__fonction = k.kw__value AND k.kw__type = 'i_con'
    WHERE l.liaison__client__id = ". $idClient ."
    ORDER BY contact__id ASC LIMIT ". $limit."");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

public function getOne($id){
    $request =$this->Db->Pdo->query("SELECT contact__id,  contact__nom , contact__prenom , contact__fax ,  contact__civ , contact__telephone , contact__email , k.kw__lib , contact__fonction
    FROM contact 
    JOIN keyword AS k ON contact__fonction = k.kw__value AND k.kw__type = 'i_con'
    WHERE contact__id = ". $id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
}

public function insertOne($fonction , $civilite, $nom , $prenom, $tel, $fax, $mail, $idClient){
    $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table."(contact__fonction , contact__civ , contact__nom, contact__prenom , contact__telephone, contact__fax, contact__email )
     VALUES (:fonction, :civilite, :nom, :prenom, :tel, :fax, :mail)");
     $requestLiaison = $this->Db->Pdo->prepare('INSERT INTO liaison_client_contact(liaison__client__id, liaison__contact__id) VALUES (:idClient, :idContact)');
     
    $request->bindValue(":fonction", $fonction);
    $request->bindValue(":civilite", $civilite);
    $request->bindValue(":nom", $nom);
    $request->bindValue(":prenom", $prenom);
    $request->bindValue(":tel", $tel);
    $request->bindValue(":fax", $fax);
    $request->bindValue(":mail", $mail);
    $request->execute();
    $idContact = $this->Db->Pdo->lastInsertId();
    $requestLiaison = $this->Db->Pdo->prepare('INSERT INTO liaison_client_contact(liaison__client__id, liaison__contact__id) VALUES (:idClient, :idContact)');
    $requestLiaison->bindValue(':idClient', $idClient);
    $requestLiaison->bindValue(':idContact', $idContact);
    $requestLiaison->execute();
    return $idContact;
}

public function insertTotoro( $id , $fonction , $civilite, $nom , $prenom, $tel, $fax, $mail, $idClient)
{
    $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table."(contact__id, contact__fonction , contact__civ , contact__nom, contact__prenom , contact__telephone, contact__fax, contact__email )
     VALUES (:contact__id, :fonction, :civilite, :nom, :prenom, :tel, :fax, :mail)");
     $requestLiaison = $this->Db->Pdo->prepare('INSERT INTO liaison_client_contact(liaison__client__id, liaison__contact__id) VALUES (:idClient, :idContact)');
    
    $request->bindValue(":contact__id", $id);
    $request->bindValue(":fonction", $fonction);
    $request->bindValue(":civilite", $civilite);
    $request->bindValue(":nom", $nom);
    $request->bindValue(":prenom", $prenom);
    $request->bindValue(":tel", $tel);
    $request->bindValue(":fax", $fax);
    $request->bindValue(":mail", $mail);
    $request->execute();
    $idContact = $this->Db->Pdo->lastInsertId();
    $requestLiaison = $this->Db->Pdo->prepare('INSERT INTO liaison_client_contact(liaison__client__id, liaison__contact__id) VALUES (:idClient, :idContact)');
    $requestLiaison->bindValue(':idClient', $idClient);
    $requestLiaison->bindValue(':idContact', $idContact);
    $requestLiaison->execute();
    return $idContact;
}
}
	
