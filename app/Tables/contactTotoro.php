<?php

namespace App\Tables;
use App\Tables\Table;
use App\Totoro;
use PDO;

class ContactTotoro extends Table {

  public string $Table = 'contact';
  public Totoro $Db;

  public function __construct($db) {
    $this->Db = $db;
}

public function insertOne($fonction , $civilite, $nom , $prenom, $tel, $gsm , $fax, $mail, $idClient)
{
    $request = $this->Db->Pdo->prepare('INSERT INTO crm_contact (interet_contact , civ  , nom, prenom , telephone , gsm ,  fax, email)
     VALUES (:fonction , :civilite , :nom, :prenom, :tel, :gsm , :fax, :mail)');
    $request->bindValue(":fonction", $fonction);
    $request->bindValue(":civilite", $civilite);
    $request->bindValue(":nom", $nom);
    $request->bindValue(":prenom", $prenom);
    $request->bindValue(":tel", $tel);
    $request->bindValue(":gsm", $gsm);
    $request->bindValue(":fax", $fax);
    $request->bindValue(":mail", $mail);
    $request->execute();
    $idContact = $this->Db->Pdo->lastInsertId();
    $requestLiaison = $this->Db->Pdo->prepare('INSERT INTO crm_r_con_cli(id_client, id_contact) VALUES (:idClient, :idContact)');
    $requestLiaison->bindValue(':idClient', $idClient);
    $requestLiaison->bindValue(':idContact', $idContact);
    $requestLiaison->execute();

    return $idContact;
}

}
	
