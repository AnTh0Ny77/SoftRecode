<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Command extends Table {

  public string $Table = 'commande';
  public Database $Db;

  public function __construct($db) {
    $this->Db = $db;
}


public function insertOne($date ,$devis, $user, $client , $livraison, $port, $contact, $comClient, $comInterne, $etat, $arrayOfObject){
 
    $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table.
    "(cmd__date_crea, cmd__devis__id, cmd__user__id, cmd__client__id, cmd__contact__id, cmd__client__id_livraison, cmd__port , cmd__note_client, cmd__note_interne, cmd__etat)
     VALUES (:date, :devis__id, :user__id, :client__id, :contact__id, :livraison__id, :port, :note_client , :note_interne, :cmd__etat)");

    $requestLigne =  $this->Db->Pdo->prepare('INSERT INTO  commandeligne 
    ( cmdligne__cmd__id, cmdligne__type, cmdligne__model, cmdligne__designation, cmdligne__etat, cmdligne__mois_garantie, cmdligne__quantite, cmdligne__mois_extension, cmdligne__prix_extension,
     cmdligne_puht, cmdligne__note_client, cmdligne__note_interne, cmdligne__ordre)
     VALUES (:cmd__id, :ligne__type, :ligne__model, :ligne__designation, :ligne__etat, :mois__garantie, :ligne__quantite, :mois__extensions, :prix__extension, :ligne__puht, :ligne__note_client,
     :ligne__note_interne, :ligne__ordre)');

    $request->bindValue(":date", $date);
    $request->bindValue(":devis__id", $devis);
    $request->bindValue(":user__id", $user);
    $request->bindValue(":client__id", $client);
    $request->bindValue(":livraison__id", $livraison);
    $request->bindValue(":port", floatval($port));
    $request->bindValue(":contact__id", $contact);
    $request->bindValue(":note_client", $comClient);
    $request->bindValue(":note_interne", $comInterne);
    $request->bindValue(":cmd__etat", $etat);
    $request->execute();
    $idCMD = $this->Db->Pdo->lastInsertId();
    foreach ($arrayOfObject as $object){
        $requestLigne->bindValue(":cmd__id", $idCMD);
        $requestLigne->bindValue(":ligne__type", $object->devl__type);
        $requestLigne->bindValue(":ligne__model", $object->devl__designation);
        $requestLigne->bindValue(":ligne__designation", $object->devl__designation);
        $requestLigne->bindValue(":ligne__etat", $object->devl__etat);
        $requestLigne->bindValue(":mois__garantie", $object->devl__mois_garantie);
        $requestLigne->bindValue(":ligne__quantite", $object->devl_quantite);
        $requestLigne->bindValue(":mois__extensions", intval($object->devl__prix_barre[0]));
        $requestLigne->bindValue(":prix__extension", floatval($object->devl__prix_barre[1]));
        $requestLigne->bindValue(":ligne__puht", floatval($object->devl_puht));
        $requestLigne->bindValue(":ligne__note_client", $object->devl__note_client);
        $requestLigne->bindValue(":ligne__note_interne", $object->devl__note_interne);
        $requestLigne->bindValue(":ligne__ordre", $object->devl__ordre);
        $requestLigne->execute();    
    } 
   return $idCMD;
}


public function getAll(){
  $request =$this->Db->Pdo->query("SELECT
   cmd__id, cmd__date_crea,  cmd__devis__id , cmd__user__id , cmd__client__id, cmd__contact__id , cmd__client__id_livraison,
   cmd__port, cmd__note_interne , cmd__etat , k.keyword__lib , u.log_nec , c.client__societe, c.client__ville, c.client__cp,
   c.client__adr1
    FROM  commande
    JOIN client as c ON cmd__client__id = c.client__id 
    JOIN client as c2 ON cmd__client__id = c2.client__id
    JOIN utilisateur as u ON cmd__user__id = u.id_utilisateur 
    JOIN keyword as k ON cmd__etat = k.keyword__value   
    ORDER BY cmd__date_crea ASC LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }
}