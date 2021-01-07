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

public function insertSociete($client_sossuke)
{
   
    $request = $this->Db->Pdo->prepare('INSERT INTO client 
    (id_client , famille  , nsoc , adr1 , adr2 , cp ,  ville , date_crea , tel , code_tva ,  id_vendeur , tva  )
     VALUES (:id_client, :famille, :nsoc, :adr1, :adr2, :cp, :ville, :date_crea, :tel, :code_tva, :id_vendeur, :tva )');

    $request->bindValue(":id_client", $client_sossuke->client__id);
    $request->bindValue(":famille", 1 );
    $request->bindValue(":nsoc", $client_sossuke->client__societe);
    $request->bindValue(":adr1", $client_sossuke->client__adr1);
    $request->bindValue(":adr2", $client_sossuke->client__adr2);
    $request->bindValue(":cp", $client_sossuke->client__cp);
    $request->bindValue(":ville", $client_sossuke->client__ville);
    $request->bindValue(":date_crea", $client_sossuke->client__date_crea);
    $request->bindValue(":tel", $client_sossuke->client__tel);
    $request->bindValue(":code_tva", $client_sossuke->client__tva);
    $request->bindValue(":id_vendeur",$client_sossuke->client__id_vendeur);
    $request->bindValue(":tva", $client_sossuke->client__tva_intracom);

    $request->execute();
    $idSociete = $this->Db->Pdo->lastInsertId();
    return $idSociete;
}

}
	
