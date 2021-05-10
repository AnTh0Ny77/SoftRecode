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

public function updateAll( string $table, $data, string $column, string $condition, string $clause )
{
	$update = $this->Db->Pdo->prepare
		('UPDATE  '.$table.'
		SET '. $column .' = ? 
		WHERE '.$condition.' = ?');
			
	$update->execute([$data, $clause]);

}

public function totoro_delete_contact_facturation_and_update($mail, $id_client)
{
	
	$request =$this->Db->Pdo->query("SELECT * 
	FROM crm_contact as c
	INNER JOIN crm_r_con_cli AS l ON c.id_contact = l.id_contact 
	WHERE l.id_client =".$id_client." AND  ( c.interet_contact = 'FAC' )");
	$contact = $request->fetch(PDO::FETCH_OBJ);

	if (!empty($mail) && filter_var($mail, FILTER_VALIDATE_EMAIL)) 
	{
		
		if (!empty($contact)) 
		{
			$request = "DELETE FROM crm_contact
			WHERE id_contact = '".$contact->id_contact."' ";
			$update = $this->Db->Pdo->prepare($request);
			$update->execute();
		}
		$request = $this->Db->Pdo->prepare('INSERT INTO crm_contact (interet_contact , civ  , nom, prenom , telephone , gsm ,  fax, email)
		 VALUES (:fonction , :civilite , :nom, :prenom, :tel, :gsm , :fax, :mail)');
		$request->bindValue(":fonction", 'FAC');
		$request->bindValue(":civilite", 'Mlle');
		$request->bindValue(":nom", 'Automatique');
		$request->bindValue(":prenom", 'Facturation');
		$request->bindValue(":tel", '');
		$request->bindValue(":gsm",'');
		$request->bindValue(":fax", '');
		$request->bindValue(":mail", $mail);
		$request->execute();
		$idContact = $this->Db->Pdo->lastInsertId();
		$requestLiaison = $this->Db->Pdo->prepare('INSERT INTO crm_r_con_cli(id_client, id_contact) VALUES (:idClient, :idContact)');
		$requestLiaison->bindValue(':idClient', $id_client);
		$requestLiaison->bindValue(':idContact', $idContact);
		$requestLiaison->execute();
		return $idContact;


	}
	else
	{

		if (!empty($contact)) 
		{
			$request = "DELETE FROM crm_contact
			WHERE id_contact = '".$contact->id_contact."' ";
			$update = $this->Db->Pdo->prepare($request);
			$update->execute();

			$request = "DELETE FROM crm_r_con_cli
			WHERE id_contact = '".$contact->id_contact."' ";
			$update = $this->Db->Pdo->prepare($request);
			$update->execute();
		}
	}
}

}
	
