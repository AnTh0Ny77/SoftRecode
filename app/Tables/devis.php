<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Devis extends Table {

  public string $Table = 'devis';
  public Database $Db;

  public function __construct($db) {
    $this->Db = $db;
}

public function insertOne($date , $user, $client , $livraison, $port, $contact, $comClient, $comInterne, $etat, $modele , $arrayOfObject , $contact_livraison){
 
    $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table."( devis__date_crea , devis__user__id, devis__client__id , devis__id_client_livraison,
     devis__port, devis__contact__id, devis__note_client, devis__note_interne, devis__etat , devis__modele , devis__contact_livraison)
     VALUES ( :devis__date_crea, :devis__user__id, :devis__client__id, :devis__id_client_livraison, :devis__port , :devis__contact__id, :devis__note_client,
      :devis__note_interne, :devis__etat , :devis__modele , :devis__id_contact_livraison)");

    $requestLigne =  $this->Db->Pdo->prepare('INSERT INTO  devisligne (devl__devis__id,devl__type,devl__modele,devl__designation,devl__etat,devl__mois_garantie,
    devl_quantite , devl__prix_barre, devl_puht,devl__note_client ,devl__note_interne ,devl__ordre ) VALUES (:devl__devis__id, :devl__type, :devl__modele, :devl__designation,
    :devl__etat, :devl__mois_garantie , :devl_quantite, :devl__prix_barre, :devl_puht , :devl__note_client , :devl__note_interne , :devl__ordre)');

    $requestGarantie =  $this->Db->Pdo->prepare('INSERT INTO  garantie ( devg__id__devl , devg__type, devg__prix , devg__ordre) VALUES 
    ( :devg__id__devl , :devg__type , :devg__prix, :devg__ordre )');

    $request->bindValue(":devis__date_crea", $date);
    $request->bindValue(":devis__user__id", $user);
    $request->bindValue(":devis__client__id", $client);
    $request->bindValue(":devis__id_client_livraison", $livraison);
    $request->bindValue(":devis__port", floatval($port));
    $request->bindValue(":devis__contact__id", $contact);
    $request->bindValue(":devis__note_client", $comClient);
    $request->bindValue(":devis__note_interne", $comInterne);
    $request->bindValue(":devis__etat", $etat);
    $request->bindValue(":devis__modele", $modele);
    $request->bindValue(":devis__id_contact_livraison", $contact_livraison);
    $request->execute();
    $idDevis = $this->Db->Pdo->lastInsertId();
    $count = 0 ;
    foreach ($arrayOfObject as $object){
        $count+= 1 ;
        $requestLigne->bindValue(":devl__devis__id", $idDevis);
        $requestLigne->bindValue(":devl__type", $object->prestation);
        $requestLigne->bindValue(":devl__modele", $object->designation);
        $requestLigne->bindValue(":devl__designation", $object->designation);
        $requestLigne->bindValue(":devl__etat", $object->etat);
        $requestLigne->bindValue(":devl__mois_garantie", $object->garantie);
        $requestLigne->bindValue(":devl_quantite", $object->quantite);
        $requestLigne->bindValue(":devl__prix_barre", floatval($object->prixBarre));
        $requestLigne->bindValue(":devl_puht", floatval($object->prix));
        $requestLigne->bindValue(":devl__note_client", $object->comClient);
        $requestLigne->bindValue(":devl__note_interne", $object->comInterne);
        $requestLigne->bindValue(":devl__ordre", $count);
        $requestLigne->execute();  
        $idLigne = $this->Db->Pdo->lastInsertId();
        $count2 = 0;  
        foreach($object->xtend as $xtend){
            $count2+= 1 ;
            $requestGarantie->bindValue(":devg__id__devl", $idLigne);
            $requestGarantie->bindValue("devg__type", $xtend[0]);
            $requestGarantie->bindValue("devg__prix", floatval($xtend[1]));
            $requestGarantie->bindValue("devg__ordre", $count2);
            $requestGarantie->execute();
        }
    } 
   return $idDevis;
}

public function getUserDevis($id){
  $request =$this->Db->Pdo->query("SELECT devis__id,  devis__date_crea , devis__user__id , devis__client__id, devis__etat , c.client__societe,  c.client__ville, c.client__cp , u.log_nec , k.keyword__lib
  FROM  devis JOIN client as c ON devis__client__id = c.client__id 
  JOIN utilisateur as u ON devis__user__id = u.id_utilisateur 
  JOIN keyword as k ON devis__etat = k.keyword__value
   WHERE  devis__user__id = " . $id ." ORDER BY  devis__date_crea DESC");
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}
public function getAll(){
  $request =$this->Db->Pdo->query("SELECT devis__id,  devis__date_crea , devis__user__id , devis__client__id, devis__etat , c.client__societe,  c.client__ville, c.client__cp , u.log_nec , k.keyword__lib
    FROM  devis JOIN client as c ON devis__client__id = c.client__id JOIN utilisateur as u ON devis__user__id = u.id_utilisateur JOIN keyword as k ON devis__etat = k.keyword__value    ORDER BY  devis__date_crea DESC LIMIT 200 ");
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function GetById($id){
  $request =$this->Db->Pdo->query("SELECT devis__id, devis__user__id , devis__date_crea, devis__client__id, devis__contact__id, devis__port, devis__note_client, devis__note_interne,  devis__id_client_livraison ,
    k.keyword__lib,
     t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe, c2.client__ville as client__livraison_ville, c2.client__cp as client__livraison_cp , c2.client__adr1 as client__livraison__adr1
    FROM devis
    LEFT JOIN contact as t ON  devis__contact__id = t.contact__id
    LEFT JOIN client as c ON devis__client__id = c.client__id
    LEFT JOIN client as c2 ON devis__id_client_livraison = c2.client__id
    LEFT JOIN keyword as k ON devis__etat = k.keyword__value
    WHERE devis__id = ". $id ."");
  $data = $request->fetch(PDO::FETCH_OBJ);
  return $data;
}

public function devisLigne($id){
  $request =$this->Db->Pdo->query("SELECT devl__id , devl__type, devl__modele, devl__designation , devl__etat, devl__mois_garantie, devl_quantite, devl__prix_barre, devl_puht,
  devl__note_client, devl__note_interne, devl__ordre
  FROM devisligne  WHERE devl__devis__id = ". $id ."");
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

public function xtenGarantie($id){
  $request =$this->Db->Pdo->query("SELECT devg__id, devg__type, devg__prix
  FROM garantie  WHERE devg__id__devl = ". $id ."");
  $data = $request->fetchAll(PDO::FETCH_ASSOC);
  return $data;
}

public function Modify( $id, $date , $user, $client , $livraison, $port, $contact, $comClient, $comInterne, $etat, $modele , $arrayOfObject ,  $contact_livraison){

  $delete = $this->Db->Pdo->prepare('DELETE  from devis WHERE devis__id =  :devis__id');
 
  $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table."(devis__id, devis__date_crea , devis__user__id, devis__client__id , devis__id_client_livraison,
   devis__port, devis__contact__id, devis__note_client, devis__note_interne, devis__etat , devis__modele ,  devis__contact_livraison)
   VALUES (:devis__id, :devis__date_crea, :devis__user__id, :devis__client__id, :devis__id_client_livraison, :devis__port , :devis__contact__id, :devis__note_client,
    :devis__note_interne, :devis__etat , :devis__modele , :devis__contact_livraison)");

  $requestLigne =  $this->Db->Pdo->prepare('INSERT INTO  devisligne (devl__devis__id,devl__type,devl__modele,devl__designation,devl__etat,devl__mois_garantie,
  devl_quantite , devl__prix_barre, devl_puht,devl__note_client ,devl__note_interne ,devl__ordre ) VALUES (:devl__devis__id, :devl__type, :devl__modele, :devl__designation,
  :devl__etat, :devl__mois_garantie , :devl_quantite, :devl__prix_barre, :devl_puht , :devl__note_client , :devl__note_interne , :devl__ordre)');

  $requestGarantie =  $this->Db->Pdo->prepare('INSERT INTO  garantie ( devg__id__devl , devg__type, devg__prix , devg__ordre) VALUES 
  ( :devg__id__devl , :devg__type , :devg__prix, :devg__ordre )');

  $delete->bindValue(":devis__id", $id);
  $delete->execute();
  $request->bindValue(":devis__id", $id);
  $request->bindValue(":devis__date_crea", $date);
  $request->bindValue(":devis__user__id", $user);
  $request->bindValue(":devis__client__id", $client);
  $request->bindValue(":devis__id_client_livraison", $livraison);
  $request->bindValue(":devis__port", floatval($port));
  $request->bindValue(":devis__contact__id", $contact);
  $request->bindValue(":devis__note_client", $comClient);
  $request->bindValue(":devis__note_interne", $comInterne);
  $request->bindValue(":devis__etat", $etat);
  $request->bindValue(":devis__modele", $modele);
  $request->bindValue(":devis__contact_livraison",$contact_livraison);
  $request->execute();
  $idDevis = $this->Db->Pdo->lastInsertId();
  $count = 0 ;
  foreach ($arrayOfObject as $object){
      $count+= 1 ;
      $requestLigne->bindValue(":devl__devis__id", $idDevis);
      $requestLigne->bindValue(":devl__type", $object->prestation);
      $requestLigne->bindValue(":devl__modele", $object->designation);
      $requestLigne->bindValue(":devl__designation", $object->designation);
      $requestLigne->bindValue(":devl__etat", $object->etat);
      $requestLigne->bindValue(":devl__mois_garantie", $object->garantie);
      $requestLigne->bindValue(":devl_quantite", $object->quantite);
      $requestLigne->bindValue(":devl__prix_barre", floatval($object->prixBarre));
      $requestLigne->bindValue(":devl_puht", floatval($object->prix));
      $requestLigne->bindValue(":devl__note_client", $object->comClient);
      $requestLigne->bindValue(":devl__note_interne", $object->comInterne);
      $requestLigne->bindValue(":devl__ordre", $count);
      $requestLigne->execute();  
      $idLigne = $this->Db->Pdo->lastInsertId();
      $count2 = 0;  
      foreach($object->xtend as $xtend){
          $count2+= 1 ;
          $requestGarantie->bindValue(":devg__id__devl", $idLigne);
          $requestGarantie->bindValue("devg__type", $xtend[0]);
          $requestGarantie->bindValue("devg__prix", floatval($xtend[1]));
          $requestGarantie->bindValue("devg__ordre", $count2);
          $requestGarantie->execute();
      }
  } 
 return $idDevis;

}

public function updateStatus($etat,$id){
  $update = $this->Db->Pdo->prepare('UPDATE devis SET devis__etat=? WHERE devis__id =?');
  $update->execute([$etat,$id]);
}

public function getFromStatus(){
  $request =$this->Db->Pdo->query("SELECT devis__id,  devis__date_crea , devis__user__id , devis__client__id, devis__etat , c.client__societe,  c.client__ville, c.client__cp , u.log_nec , k.keyword__lib
  FROM  devis JOIN client as c ON devis__client__id = c.client__id 
  JOIN utilisateur as u ON devis__user__id = u.id_utilisateur 
  JOIN keyword as k ON devis__etat = k.keyword__value
  WHERE devis__etat = 'VLD'     
  ORDER BY  devis__date_crea DESC LIMIT 200 ");
$data = $request->fetchAll(PDO::FETCH_OBJ);
return $data;
}

public function getFromStatusCMD(){
  $request =$this->Db->Pdo->query("SELECT devis__id,  devis__date_crea , devis__user__id , devis__client__id, devis__etat , c.client__societe,  c.client__ville, c.client__cp , u.log_nec , k.keyword__lib
  FROM  devis JOIN client as c ON devis__client__id = c.client__id 
  JOIN utilisateur as u ON devis__user__id = u.id_utilisateur 
  JOIN keyword as k ON devis__etat = k.keyword__value
  WHERE devis__etat = 'CMD'     
  ORDER BY  devis__date_crea DESC LIMIT 200 ");
$data = $request->fetchAll(PDO::FETCH_OBJ);
return $data;
}


public function getNotCMD(){
  $request =$this->Db->Pdo->query("SELECT devis__id,  devis__date_crea , devis__user__id , devis__client__id, devis__etat , c.client__societe,  c.client__ville, c.client__cp , u.log_nec , k.keyword__lib
  FROM  devis JOIN client as c ON devis__client__id = c.client__id 
  JOIN utilisateur as u ON devis__user__id = u.id_utilisateur 
  JOIN keyword as k ON devis__etat = k.keyword__value
  WHERE devis__etat <> 'CMD'     
  ORDER BY  devis__date_crea DESC LIMIT 200 ");
$data = $request->fetchAll(PDO::FETCH_OBJ);
return $data;
}

 
}