<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;
use stdClass;

class Cmd extends Table {

  public Database $Db;

  public function __construct($db) {
    $this->Db = $db;
  }


  public function GetById($id){
    $request =$this->Db->Pdo->query("SELECT
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__note_client as  devis__note_client ,
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    cmd__nom_devis, cmd__modele_devis , 
    cmd__date_cmd, cmd__date_envoi, cmd__code_cmd_client, cmd__tva, cmd__user__id_cmd, cmd__id_facture,
    cmd__modele_facture, cmd__id_facture , cmd__date_fact, 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    t2.contact__nom as nom__livraison , t2.contact__prenom as prenom__livraison ,
    t2.contact__email as mail__livraison , t2.contact__gsm as gsm__livraison , t2.contact__telephone as fixe__livraison, 
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    c2.client__adr2 as client__livraison__adr2 , 
    u.log_nec , u.user__email_devis as email ,
    k3.kw__info as tva_Taux , k3.kw__value as tva_value
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN contact as t2  ON  cmd__contact__id_livr = t2.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN keyword as k3 ON cmd__tva = k3.kw__value AND k3.kw__type = 'tva'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__id = ". $id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }


  




  public function getUserDevis($id){
    $request =$this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_envoi,
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 ,
    cmd__nom_devis,
    u.log_nec
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE  cmd__user__id_devis = " . $id ." ORDER BY  devis__date_crea DESC , c.client__societe ASC LIMIT 200");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function getAll(){
    $request =$this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    cmd__nom_devis,
    u.log_nec 
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function updateStatus($etat,$id){
    $update = $this->Db->Pdo->prepare(
    'UPDATE cmd
     SET cmd__etat=? 
     WHERE cmd__id =?');
    $update->execute([$etat,$id]);
  }

  public function updateComInterne($com,$id){
    $update = $this->Db->Pdo->prepare(
    'UPDATE cmd
     SET cmd__note_interne=? 
     WHERE cmd__id =?');
    $update->execute([$com,$id]);
  }

  public function updateComInterneLigne($com,$id){
    $update = $this->Db->Pdo->prepare(
    'UPDATE cmd_ligne
     SET cmdl__note_interne=? 
     WHERE cmdl__id =?');
    $update->execute([$com,$id]);
  }

  public function updateDate( $column , $date,  $id){
    $update = $this->Db->Pdo->prepare(
    'UPDATE cmd 
     SET ' . $column. ' = ? 
     WHERE cmd__id =?    
    ');
    $update->execute([ $date , $id ]);
  }

  public function updateAuthor($column , $author , $id){
    $update = $this->Db->Pdo->prepare(
    'UPDATE cmd 
     SET '. $column .' = ? 
     WHERE cmd__id = ? 
    ');
     $update->execute([ $author , $id ]);
  }


  public function updateGarantieToArchive($cmdId)
  {
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd 
       SET cmd__etat = "ARH"
       WHERE cmd__id = ? 
      ');
       $update->execute([$cmdId]);
  }

  //met a jour les info relative au transport ainsi que la date et l'etat ( saisie )
  public function updateTransport($trans , $poids , $paquet ,  $id , $imp , $date ){


    $check = $this->Db->Pdo->query(
    'SELECT cmd__client__id_fact , cmd__client__id_livr FROM cmd WHERE cmd__id = '.$id.' 
    ');

    $results = $check->fetch(PDO::FETCH_OBJ);

    if (empty($results->cmd__client__id_livr)) 
    {
     $transfert =  $this->Db->Pdo->prepare(
      'UPDATE cmd 
       SET  cmd__client__id_livr = '.$results->cmd__client__id_fact.' 
       WHERE cmd__id = '.$id.'
      ');
      $transfert->execute();
    }

    $data = 
    [
      $trans ,
      $poids, 
      $paquet , 
      $imp ,
      $date,
      $id , 

    ];
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd
       SET cmd__trans =? , cmd__trans_kg =? , cmd__trans_info =? , cmd__etat =? , cmd__date_envoi =?  

       WHERE cmd__id = ? ');
    
    $update->execute($data);

  }

  //met a jour un champs dans une ligne / prend une collones en second parametre
  public function updateLigne($qte , $column,  $id )
  {
    $data = 
    [
      $qte,
      $id ,
    ];

    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd_ligne 
      SET '. $column .' = ?
      WHERE cmdl__id = ? '
    );

    $update->execute($data);

  }

  //met a jour les quantité et le prix (facturation)
  public function updateLigneFTC($id, $qteCMD , $qteLVR, $qteFTC , $note_facture , $prix )
  {
    $data = 
    [
      $qteCMD,
      $qteLVR,
      $qteFTC,
      $prix,
      $note_facture,
      $id
    ];

    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd_ligne
       SET cmdl__qte_cmd =? ,cmdl__qte_livr=? , cmdl__qte_fact=? , cmdl__puht =? , cmdl__note_facture=?
       WHERE cmdl__id=? ');
    
    $update->execute($data);
  }


//retourne le devis lié à la ligne:
public function returnDevis($idCmdl)
{
  $getD = $this->Db->Pdo->query("SELECT cmdl__cmd__id FROM cmd_ligne WHERE cmdl__id = ".$idCmdl."");
  $id = $getD->fetch(PDO::FETCH_OBJ);


  $request =$this->Db->Pdo->query("SELECT
    cmd__id as devis__id FROM cmd
    WHERE cmd__id = ". $id->cmdl__cmd__id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;

}

 
//met a jours les extensions de garantie pour la cmd passée en parametre
  public function updateGarantie($mois, $prix , $comInterne , $qte,  $id , $ordre){
    $data = 
    [
      $mois, 
      floatval($prix) , 
      $comInterne , 
      $qte,
      $id , 
      $ordre
    ];

    $sql = 
   "UPDATE cmd_ligne
    SET  
    cmdl__garantie_option  =?,
    cmdl__garantie_puht = ?,
    cmdl__note_interne = ?,
    cmdl__qte_cmd = ?
    WHERE cmdl__cmd__id  = ?  
    AND  cmdl__ordre = ?";

    $update = $this->Db->Pdo->prepare($sql);
    $update->execute($data);
  }




//met a jour l'id client et l'id contact (facture)
  public function updateClientContact($idClient , $idContact , $id)
  {
    $data = 
    [
      $idClient ,
      $idContact ,
      $id
    ];
    $sql = 
    "UPDATE cmd
     SET 
     cmd__client__id_fact =?,
     cmd__contact__id_fact =?
     WHERE cmd__id =? ";

    $update = $this->Db->Pdo->prepare($sql);
    $update->execute($data);
  }






  //met a jour la quanbtité facturé dans une ligne de facture: 
  public function updateQuantiteFTC($cmd)
  {
    $request =$this->Db->Pdo->query("SELECT
    cmdl__id , cmdl__cmd__id, cmdl__qte_livr , cmdl__qte_fact
    FROM cmd_ligne 
    WHERE cmdl__cmd__id = ". $cmd ."");
    $arrayLigne = $request->fetchAll(PDO::FETCH_OBJ);

    foreach ($arrayLigne as $ligne) 
    {
        if ($ligne->cmdl__qte_fact == null) 
        {
          $data = 
          [
            $ligne->cmdl__qte_livr,
            $ligne->cmdl__id
          ];
          
          $updateFTC = 
            "UPDATE cmd_ligne
            SET cmdl__qte_fact =?
            WHERE cmdl__id =? ";
          
          $update = $this->Db->Pdo->prepare($updateFTC);
          $update->execute($data);
        }  
    }
  }

// si un ecart est constaté dans les quantité génère des reliquats automatiquement: 
public function classicReliquat($cmd)
{
  $lignes = $this->devisLigne($cmd);
 
  $NewLines = [];
  foreach ($lignes as $ligne) 
  {
    
    if ( intval($ligne->devl_quantite) > intval($ligne->cmdl__qte_fact)) 
    {
      $ligne->devl_quantite = intval($ligne->devl_quantite) - intval($ligne->cmdl__qte_fact);
      array_push($NewLines, $ligne);
    }
  }
 
  
  if (!empty($NewLines)) 
  {
   
    $reliquat = $this->GetById($cmd);

    $request = $this->Db->Pdo->prepare('INSERT INTO cmd ( cmd__date_cmd, cmd__client__id_fact,
      cmd__client__id_livr, cmd__contact__id_fact,  cmd__contact__id_livr,
      cmd__note_client, cmd__note_interne, cmd__code_cmd_client,
      cmd__etat, cmd__user__id_devis, cmd__user__id_cmd)
      VALUES (:cmd__date_cmd, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__contact__id_fact, :cmd__contact__id_livr,
      :cmd__note_client, :cmd__note_interne, :cmd__code_cmd_client, :cmd__etat, :cmd__user__id_devis, :cmd__user__id_cmd)');

      

    $code_cmd = 'RELIQUAT  cmd n°' . $reliquat->devis__id . "  " .  $reliquat->cmd__code_cmd_client;

    $request->bindValue(":cmd__date_cmd", $reliquat->cmd__date_cmd);
    $request->bindValue(":cmd__client__id_fact", $reliquat->client__id);
    $request->bindValue(":cmd__client__id_livr", $reliquat->devis__id_client_livraison);
    $request->bindValue(":cmd__contact__id_fact", $reliquat->devis__contact__id);
    $request->bindValue(":cmd__contact__id_livr", $reliquat->devis__contact_livraison);
    $request->bindValue(":cmd__note_client", $reliquat->devis__note_client);   
    $request->bindValue(":cmd__note_interne", $reliquat->devis__note_interne);
    $request->bindValue(":cmd__code_cmd_client", $code_cmd );
    $request->bindValue(":cmd__etat", 'CMD');
    $request->bindValue(":cmd__user__id_devis", $reliquat->devis__user__id );
    $request->bindValue(":cmd__user__id_cmd", $reliquat->cmd__user__id_cmd );

    
    $request->execute();

    
    
    $idReliquat = $this->Db->Pdo->lastInsertId();
    $count = 0 ;


    
    foreach ($NewLines as $lines ) 
    {
      $count += 1;
      $insertObject = new stdClass;
      $insertObject->idDevis = $idReliquat;
      $insertObject->prestation = $lines->devl__type;
      $insertObject->designation = $lines->devl__designation;
      $insertObject->etat = 'CMD';
      $insertObject->garantie = $lines->devl__mois_garantie;
      $insertObject->quantite = $lines->devl_quantite;
      $insertObject->prix = $lines->devl_puht;
      $insertObject->comClient = $lines->devl__note_client;
      $insertObject->idfmm = $lines->id__fmm;
      $insertObject->extension = $lines->cmdl__garantie_option;
      $insertObject->prixGarantie = $lines->cmdl__garantie_puht;

      $createLine = $this->insertLine($insertObject);
     
    
  }
  }
}



// creer un nouvel avoir: 
public function makeAvoir($facture)
{
  
  $request = $this->Db->Pdo->prepare('INSERT INTO cmd ( cmd__date_cmd, cmd__client__id_fact,
  cmd__client__id_livr, cmd__contact__id_fact,  cmd__contact__id_livr,
  cmd__note_client, cmd__note_interne, cmd__code_cmd_client,
  cmd__etat, cmd__user__id_devis, cmd__user__id_cmd)
  VALUES (:cmd__date_cmd, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__contact__id_fact, :cmd__contact__id_livr,
  :cmd__note_client, :cmd__note_interne, :cmd__code_cmd_client, :cmd__etat, :cmd__user__id_devis, :cmd__user__id_cmd)');

  $avoirId =  'Avoir Facture N°: '.$facture->cmd__id_facture . ' ' . $facture->cmd__code_cmd_client;


  $request->bindValue(":cmd__date_cmd", $facture->cmd__date_cmd);
  $request->bindValue(":cmd__client__id_fact", $facture->client__id);
  $request->bindValue(":cmd__client__id_livr", $facture->devis__id_client_livraison);
  $request->bindValue(":cmd__contact__id_fact", $facture->devis__contact__id);
  $request->bindValue(":cmd__contact__id_livr", $facture->devis__contact_livraison);
  $request->bindValue(":cmd__note_client", $facture->devis__note_client);   
  $request->bindValue(":cmd__note_interne", $facture->devis__note_interne);
  $request->bindValue(":cmd__code_cmd_client",  $avoirId );
  $request->bindValue(":cmd__etat", 'CMD');
  $request->bindValue(":cmd__user__id_devis", $facture->devis__user__id );
  $request->bindValue(":cmd__user__id_cmd", $facture->cmd__user__id_cmd );
  $request->execute();

  $idfacture = $this->Db->Pdo->lastInsertId();

  return $idfacture;
}


// creer un nouvel avoir: 2 eme param = garantie ou retour , 3eme = client : echange reliquat et co (id) , id du tech qui edite la fiche :
public function makeRetour($facture ,$type , $client , $user)
{
  $request = $this->Db->Pdo->prepare('INSERT INTO cmd ( cmd__date_cmd, cmd__client__id_fact,
  cmd__client__id_livr, cmd__contact__id_fact,  cmd__contact__id_livr,
  cmd__note_client, cmd__note_interne, cmd__code_cmd_client,
  cmd__etat, cmd__user__id_devis, cmd__user__id_cmd)
  VALUES (:cmd__date_cmd, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__contact__id_fact, :cmd__contact__id_livr,
  :cmd__note_client, :cmd__note_interne, :cmd__code_cmd_client, :cmd__etat, :cmd__user__id_devis, :cmd__user__id_cmd)');

  $avoirId =  $type . ' commande :  ' . $facture->devis__id;


  $request->bindValue(":cmd__date_cmd", $facture->cmd__date_cmd);
  $request->bindValue(":cmd__client__id_fact", $client);
  $request->bindValue(":cmd__client__id_livr", $facture->devis__id_client_livraison);
  $request->bindValue(":cmd__contact__id_fact", null);
  $request->bindValue(":cmd__contact__id_livr", $facture->devis__contact_livraison);
  $request->bindValue(":cmd__note_client", $facture->devis__note_client);   
  $request->bindValue(":cmd__note_interne", $facture->devis__note_interne);
  $request->bindValue(":cmd__code_cmd_client",  $avoirId );
  $request->bindValue(":cmd__etat", 'CMD');
  $request->bindValue(":cmd__user__id_devis", $facture->devis__user__id );
  $request->bindValue(":cmd__user__id_cmd", $user );
  $request->execute();
  $idfacture = $this->Db->Pdo->lastInsertId();
  return $idfacture;
}

//insère une ligne dans un devis :
public function insertLine($object){
  $requestLigne =  $this->Db->Pdo->prepare(
    'INSERT INTO  cmd_ligne (
     cmdl__cmd__id, cmdl__prestation,  cmdl__designation ,
     cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  ,  
     cmdl__puht , cmdl__note_facture  ,  cmdl__ordre , cmdl__id__fmm , cmdl__garantie_option , cmdl__garantie_puht , cmdl__qte_livr)
     VALUES (
     :devl__devis__id, :devl__type,  :devl__designation,
     :devl__etat, :devl__mois_garantie , :devl_quantite,  
     :devl_puht , :devl__note_client ,  :devl__ordre , :id__fmm , :cmdl__garantie_option , :cmdl__garantie_puht , :cmdl__qte_livr)');


    $verifOrdre = $this->Db->Pdo->query(
      'SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne ');

    $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
    
    
    $ordreMax = $ordreMax->maxOrdre + 1 ;

    $requestLigne->bindValue(":devl__devis__id", $object->idDevis);
    $requestLigne->bindValue(":devl__type", $object->prestation);
    $requestLigne->bindValue(":devl__designation", $object->designation);
    $requestLigne->bindValue(":devl__etat", $object->etat);
    $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
    $requestLigne->bindValue(":devl_quantite", $object->quantite);
    $requestLigne->bindValue(":devl_puht", floatval($object->prix));
    $requestLigne->bindValue(":devl__note_client", $object->comClient);
    $requestLigne->bindValue(":devl__ordre", $ordreMax);
    $requestLigne->bindValue(":id__fmm", $object->idfmm);
    $requestLigne->bindValue(":cmdl__garantie_option", $object->extension);
    $requestLigne->bindValue(":cmdl__garantie_puht", floatVal($object->prixGarantie));
    $requestLigne->bindValue(":cmdl__qte_livr", intval($object->quantite));
    $requestLigne->execute();  
    return $requestLigne;
}

//recupère les lignes liées à un devis:
public function devisLigne($id){
  $request =$this->Db->Pdo->query("SELECT
  cmdl__cmd__id,
  cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
  cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
  cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
  cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
  cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
  cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
  cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
  k.kw__lib , k.kw__value , 
  f.afmm__famille as famille,
  f.afmm__modele as modele,
  k2.kw__lib as prestaLib,
  k3.kw__info as groupe_famille,
  k3.kw__lib as famille__lib,
  a.am__marque as marque
  FROM cmd_ligne 
  LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
  LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
  LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
  LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
  LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
  WHERE cmdl__cmd__id = ". $id ."
  ORDER BY devl__ordre ");
 
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}


//recupère les lignes liées à un devis:
public function devisLigneFacturee($id){
  $request =$this->Db->Pdo->query("SELECT
  cmdl__cmd__id,
  cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
  cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
  cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
  cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
  cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
  cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
  cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
  k.kw__lib , k.kw__value , 
  f.afmm__famille as famille,
  f.afmm__modele as modele,
  k2.kw__lib as prestaLib,
  k3.kw__info as groupe_famille,
  k3.kw__lib as famille__lib,
  a.am__marque as marque
  FROM cmd_ligne 
  LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
  LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
  LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
  LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
  LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
  WHERE cmdl__cmd__id = ". $id ." AND cmdl__qte_fact > 0 
  ORDER BY devl__ordre ");
 
  $data = $request->fetchAll(PDO::FETCH_OBJ);
  return $data;
}

//recupere le numero de compte du plan comptable pour chaque ligne passée en parametre:
public function getCompta($ligne , $cmd)
{
 
    $arrayResponse = [];
    $request = $this->Db->Pdo->query("SELECT * FROM compta
    WHERE cpt__tva_kw = ".$cmd->tva_value." AND cpt__pres_kw = '".$ligne->devl__type."' ");
    
    $data = $request->fetch(PDO::FETCH_OBJ);
    
    array_push($arrayResponse , $data);

    if (!empty($ligne->cmdl__garantie_puht) && intval($ligne->cmdl__garantie_puht) > 0 ) 
    {
      $request = $this->Db->Pdo->query('SELECT * FROM compta
      WHERE cpt__tva_kw = '.$cmd->tva_value.'AND cpt__pres__kw = EXG ');
      $data = $request->fetch(PDO::FETCH_OBJ);
      array_push($arrayResponse , $data);
    }
    return $arrayResponse;
  
  
}


//recupère les lignes liées à un devis id_ligne:
public function devisLigneId($id){
  $request =$this->Db->Pdo->query("SELECT
  cmdl__cmd__id,
  cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
  cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
  cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
  cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
  cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
  cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
  cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
  k.kw__lib , k.kw__value , 
  f.afmm__famille as famille,
  f.afmm__modele as modele,
  k2.kw__lib as prestaLib,
  k3.kw__info as groupe_famille,
  k3.kw__lib as famille__lib,
  a.am__marque as marque
  FROM cmd_ligne 
  LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
  LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
  LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
  LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
  LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
  WHERE cmdl__id = ". $id ."
  ORDER BY devl__ordre ");
 
  $data = $request->fetch(PDO::FETCH_OBJ);
  return $data;
}

//attribut les lignes avoirées
public function makeAvoirLigne($id , $avoirId , $qte)
{
  $requestLigne =  $this->Db->Pdo->prepare(
    'INSERT INTO  cmd_ligne (
     cmdl__cmd__id, cmdl__prestation, cmdl__pn, cmdl__designation,
     cmdl__etat, cmdl__garantie_base, cmdl__qte_cmd,
     cmdl__puht, cmdl__note_client, cmdl__note_interne, cmdl__ordre, cmdl__id__fmm,
     cmdl__qte_fact, cmdl__prix_barre, cmdl__note_facture, cmdl__garantie_option, cmdl__garantie_puht)
     SELECT cmdl__cmd__id, cmdl__prestation, cmdl__pn, cmdl__designation,
     cmdl__etat, cmdl__garantie_base, cmdl__qte_cmd, 
     cmdl__puht, cmdl__note_client, cmdl__note_interne, cmdl__ordre, cmdl__id__fmm,
     cmdl__qte_fact, cmdl__prix_barre, cmdl__note_facture, cmdl__garantie_option, cmdl__garantie_puht
     FROM cmd_ligne
     WHERE cmdl__id = '.$id.'');
   $requestLigne->execute();
   $idLigne = $this->Db->Pdo->lastInsertId();

   $data = 
    [
      $avoirId,
      intval($qte),
      $idLigne
    ];
          
  $updateNewLines = 
        "UPDATE cmd_ligne
         SET cmdl__cmd__id =? , cmdl__qte_fact = ? 
         WHERE cmdl__id =? ";
          
  $update = $this->Db->Pdo->prepare($updateNewLines);
  $update->execute($data);
  return $idLigne;
}


//inverse les prix pour chaque ligne: 
public function reversePrice($idLigne)
{
  $ligne = $this->devisLigneId($idLigne);

  if (!empty($ligne->cmdl__garantie_puht) && floatval($ligne->cmdl__garantie_puht) > 0 )
  {
    $reverse = $ligne->cmdl__garantie_puht *-1;
    $data = [ $reverse , $idLigne];
    $updateNewLines = 
    "UPDATE cmd_ligne
     SET cmdl__garantie_puht =?
     WHERE cmdl__id =? ";
     $update = $this->Db->Pdo->prepare($updateNewLines);
     $update->execute($data);
  }

    $reversePrice = $ligne->devl_puht *-1;
    $dataPrice = [ $reversePrice , $idLigne];
    $updateNewPrice = 
    "UPDATE cmd_ligne
     SET cmdl__puht =?
     WHERE cmdl__id =? ";
     $updateReverse = $this->Db->Pdo->prepare($updateNewPrice);
     $updateReverse->execute($dataPrice);
}

  // met a jour le status de la commande et le munero de facture:
  public function commande2facture($cmd)
  {
    //recup le dernier numero de facture: 
    $lastFact= $this->Db->Pdo->query('SELECT MAX(cmd__id_facture) as lastFact from cmd ');
    $lastFTC = $lastFact->fetch(PDO::FETCH_OBJ);

    $newfact = $lastFTC->lastFact + 1;
    $data = 
    [
      $newfact
      ,
      $cmd
    ];

    $sql = 
    "UPDATE cmd
     SET 
     cmd__id_facture =? ,
     cmd__etat = 'VLD'
     WHERE cmd__id =? ";

    $update = $this->Db->Pdo->prepare($sql);
    $update->execute($data);

  }

 

  //recupère tous les status VLD
  public function getFromStatus(){
    $request =$this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id ,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    u.log_nec
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__etat = 'VLD'     
    ORDER BY  cmd__date_devis DESC , c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  //recupére tous les status cmd
  public function getFromStatusCMD(){
    $request =$this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__nom_devis, cmd__modele_devis , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    u.log_nec , u.user__email_devis as email
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__etat = 'CMD' OR cmd__etat = 'IMP'    
    ORDER BY  cmd__date_devis DESC , c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  //prend l'id facture le plus élevé:
  public function getMaxFacture()
  {
    $request = $this->Db->Pdo->query('SELECT MAX(cmd__id_facture) as cmd__id_facture
    FROM cmd');
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }

  //prend l'id facture le plus élevé:
  public function getMinFacture()
  {
    $request = $this->Db->Pdo->query('SELECT MIN(cmd__id_facture) as cmd__id_facture
    FROM cmd');
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }

  //recupere toute les lignes de cmd entre 2 id cmd 
  public function ligneXport($start, $end)
  {
    $request =$this->Db->Pdo->query("SELECT
      cmd__id
      FROM cmd 
      WHERE cmd__id_facture BETWEEN ".$start." AND ".$end."
      ORDER BY cmd__id_facture DESC ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  //recupere toutes les lignes du tableau d'id passé en parametre:
  public function exportFinal($array)
  {
    $response = [];
    foreach ($array as  $value)
    {
      $temp = $this->devisLigneFacturee($value->cmd__id);
      array_push($response , $temp);
    }
    return $response;
  }


  //prend le status en CHAR de 3 en parametre et renvoi tous les devis:
  public function getFromStatusAll($status){
    $request =$this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__nom_devis, cmd__modele_devis , cmd__modele_facture, cmd__id_facture , cmd__date_fact, 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    u.log_nec , u.user__email_devis as email
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__etat = '".$status."'    
    ORDER BY  cmd__id_facture DESC , c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  

  //recupere les status autre que CMD 
  public function getNotCMD(){
    $request =$this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat,
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    k.kw__lib,  cmd__date_envoi,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    u.log_nec
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__etat <> 'CMD'     
    ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  //crée un nouveau devis:
  public function insertOne(
    $date , $user, $client , $livraison, $contact, $comClient,
    $comInterne, $etat, $modele , $arrayOfObject , $contact_livraison , $titreDevis )
    {


      if ( preg_match('/style="width:[^\"]*"/',$comClient)) 
    {
      
      preg_match('/style="width:[^\"]*"/',$comClient ,$matches);
      $matches[0] = ' ' .  $matches[0];
      $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ' , $comClient  );
      $comClient = $chaine;
    }



    $request = $this->Db->Pdo->prepare(
       'INSERT INTO cmd (
        cmd__date_devis , cmd__user__id_devis, cmd__client__id_fact ,
        cmd__client__id_livr, cmd__contact__id_fact,
        cmd__note_client, cmd__note_interne,
        cmd__etat , cmd__modele_devis , cmd__contact__id_livr , cmd__nom_devis)
        VALUES ( 
        :devis__date_crea, :devis__user__id, :devis__client__id, 
        :devis__id_client_livraison, :devis__contact__id, 
        :devis__note_client, :devis__note_interne, :devis__etat ,
        :devis__modele , :devis__id_contact_livraison, :nom_devis)');


    $requestLigne =  $this->Db->Pdo->prepare(
       'INSERT INTO  cmd_ligne (
        cmdl__cmd__id, cmdl__prestation, cmdl__pn , cmdl__designation ,
        cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  , cmdl__prix_barre , 
        cmdl__puht , cmdl__note_client  , cmdl__note_interne  , cmdl__ordre , cmdl__id__fmm)
        VALUES (
        :devl__devis__id, :devl__type, :devl__modele, :devl__designation,
        :devl__etat, :devl__mois_garantie , :devl_quantite, :devl__prix_barre, 
        :devl_puht , :devl__note_client , :devl__note_interne , :devl__ordre , :id__fmm)');

    $requestGarantie =  $this->Db->Pdo->prepare(
       'INSERT INTO  cmd_garantie ( 
        cmdg__id__cmdl , cmdg__type , cmdg__prix  , cmdg__ordre )
        VALUES (
        :devg__id__devl , :devg__type , :devg__prix, :devg__ordre )');

    $request->bindValue(":devis__date_crea", $date);
    $request->bindValue(":devis__user__id", $user);
    $request->bindValue(":devis__client__id", $client);
    $request->bindValue(":devis__id_client_livraison", $livraison);
    $request->bindValue(":devis__contact__id", $contact);
    $request->bindValue(":devis__note_client", $comClient);
    $request->bindValue(":devis__note_interne", $comInterne);
    $request->bindValue(":devis__etat", $etat);
    $request->bindValue(":devis__modele", $modele);
    $request->bindValue(":devis__id_contact_livraison", $contact_livraison);
    $request->bindValue(":nom_devis", $titreDevis);
    $request->execute();
    $idDevis = $this->Db->Pdo->lastInsertId();
    $count = 0 ;
    foreach ($arrayOfObject as $object){
      
        $verify = $this->Db->Pdo->query('
        SELECT  afmm__famille FROM art_fmm WHERE afmm__id = '.  $object->id__fmm .'');
        $response  = $verify->fetch(PDO::FETCH_OBJ);
        $count+= 1 ;
        $requestLigne->bindValue(":devl__devis__id", $idDevis);
        $requestLigne->bindValue(":devl__type", $object->prestation);
        $requestLigne->bindValue(":devl__modele", $object->pn);
        $requestLigne->bindValue(":devl__designation", $object->designation);
        $requestLigne->bindValue(":id__fmm", $object->id__fmm);


        
        if ($response->afmm__famille == 'SER') {
          $requestLigne->bindValue(":devl__mois_garantie", intval( 0 ));
          $requestLigne->bindValue(":devl__etat",'NC.');
        }
        else {
          $requestLigne->bindValue(":devl__etat", $object->etat);
          $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
        }

        if ( preg_match('/style="width:[^\"]*"/', $object->comClient)) 
        { 
          preg_match('/style="width:[^\"]*"/', $object->comClient ,$matches);
          $matches[0] = ' ' .  $matches[0];
          $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ' , $object->comClient  );
          $object->comClient = $chaine;
        }
        
        $requestLigne->bindValue(":devl_quantite", $object->quantite);
        $requestLigne->bindValue(":devl__prix_barre", floatval($object->prixBarre));
        $requestLigne->bindValue(":devl_puht", floatval($object->prix));
        $requestLigne->bindValue(":devl__note_client", $object->comClient);
        $requestLigne->bindValue(":devl__note_interne", $object->comInterne);
        $requestLigne->bindValue(":devl__ordre", $object->id);
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




//efface et remplace le devis: 
public function modify(
    $id , $date , $user, $client , $livraison,  $contact, $comClient,
    $comInterne, $etat, $modele , $arrayOfObject , $contact_livraison , $titreDevis)
    {

    $delete = $this->Db->Pdo->prepare(
    'DELETE  from cmd
     WHERE cmd__id =  :cmd__id');

    if ( preg_match('/style="width:[^\"]*"/',$comClient)) 
    {

      preg_match('/style="width:[^\"]*"/',$comClient ,$matches);
      $matches[0] = ' ' .  $matches[0];
      $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ' , $comClient  );
      $comClient = $chaine;

    }


    $request = $this->Db->Pdo->prepare(
     'INSERT INTO cmd (
      cmd__id, cmd__date_devis , cmd__user__id_devis, cmd__client__id_fact ,
      cmd__client__id_livr,  cmd__contact__id_fact,
      cmd__note_client, cmd__note_interne,
      cmd__etat , cmd__modele_devis , cmd__contact__id_livr , cmd__nom_devis)
      VALUES ( 
      :cmd__id , :devis__date_crea, :devis__user__id, :devis__client__id, 
      :devis__id_client_livraison,  :devis__contact__id, 
      :devis__note_client, :devis__note_interne, :devis__etat ,
      :devis__modele , :devis__id_contact_livraison , :nomDevis)');

    $requestLigne =  $this->Db->Pdo->prepare(
     'INSERT INTO  cmd_ligne (
      cmdl__cmd__id, cmdl__prestation, cmdl__pn , cmdl__designation ,
      cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  , cmdl__prix_barre , 
      cmdl__puht , cmdl__note_client  , cmdl__note_interne  , cmdl__ordre , cmdl__id__fmm)
      VALUES (
      :devl__devis__id, :devl__type, :devl__modele, :devl__designation,
      :devl__etat, :devl__mois_garantie , :devl_quantite, :devl__prix_barre, 
      :devl_puht , :devl__note_client , :devl__note_interne , :devl__ordre , :id__fmm)');

    $requestGarantie =  $this->Db->Pdo->prepare(
     'INSERT INTO  cmd_garantie ( 
      cmdg__id__cmdl , cmdg__type , cmdg__prix  , cmdg__ordre )
      VALUES (
      :devg__id__devl , :devg__type , :devg__prix, :devg__ordre )');

    $delete->bindValue(":cmd__id", $id);
    $delete->execute();
    $request->bindValue(":cmd__id", $id);   
    $request->bindValue(":devis__date_crea", $date);
    $request->bindValue(":devis__user__id", $user);
    $request->bindValue(":devis__client__id", $client);
    $request->bindValue(":devis__id_client_livraison", $livraison);
    $request->bindValue(":devis__contact__id", $contact);
    $request->bindValue(":devis__note_client", $comClient);
    $request->bindValue(":devis__note_interne", $comInterne);
    $request->bindValue(":devis__etat", $etat);
    $request->bindValue(":devis__modele", $modele);
    $request->bindValue(":devis__id_contact_livraison",$contact_livraison);
    $request->bindValue(":nomDevis", $titreDevis);
    $request->execute();
    $idDevis = $this->Db->Pdo->lastInsertId();
    $count = 0 ;
    foreach ($arrayOfObject as $object){
        $verify = $this->Db->Pdo->query('
        SELECT  afmm__famille FROM art_fmm WHERE afmm__id = '.  $object->id__fmm .'');
        $respnse =  $verify->fetch(PDO::FETCH_OBJ);
        
        $count+= 1 ;
        $requestLigne->bindValue(":devl__devis__id", $idDevis);
        $requestLigne->bindValue(":devl__type", $object->prestation);
        $requestLigne->bindValue(":devl__modele", $object->pn);
        $requestLigne->bindValue(":id__fmm", $object->id__fmm);
        $requestLigne->bindValue(":devl__designation", $object->designation);
        
        if ($respnse->afmm__famille == 'SER') {
          $requestLigne->bindValue(":devl__mois_garantie", intval( 0 ));
          $requestLigne->bindValue(":devl__etat",'NC.');
        }
        else {
          $requestLigne->bindValue(":devl__etat", $object->etat);
          $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
        }


        if ( preg_match('/style="width:[^\"]*"/', $object->comClient)) 
        {
          
          preg_match('/style="width:[^\"]*"/', $object->comClient ,$matches);
          $matches[0] = ' ' .  $matches[0];
          $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ' , $object->comClient  );
          $object->comClient = $chaine;
    
        }



        $requestLigne->bindValue(":devl_quantite", $object->quantite);
        $requestLigne->bindValue(":devl__prix_barre", floatval($object->prixBarre));
        $requestLigne->bindValue(":devl_puht", floatval($object->prix));
        $requestLigne->bindValue(":devl__note_client", $object->comClient);
        $requestLigne->bindValue(":devl__note_interne", $object->comInterne);
        $requestLigne->bindValue(":devl__ordre", $object->id);
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

    
    


    public function devisLigneUnit($id){
      $request =$this->Db->Pdo->query("SELECT
      cmdl__cmd__id,
      cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
      cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
      cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
      cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
      cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
      cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
      cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__note_facture,
      k.kw__lib , k.kw__value , 
      f.afmm__famille as famille,
      f.afmm__modele as modele,
      k2.kw__lib as prestaLib,
      k3.kw__info as groupe_famille,
      k3.kw__lib as famille__lib,
      a.am__marque as marque
      FROM cmd_ligne 
      LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
      LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
      LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
      LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
      LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
      WHERE cmdl__id = ". $id ."
      ORDER BY devl__ordre ");
     
      $data = $request->fetch(PDO::FETCH_OBJ);
      return $data;
    }

    public function xtenGarantie($id){
      $request =$this->Db->Pdo->query("SELECT 
      cmdg__id as devg__id,  LPAD(cmdg__type ,2,0)  as  devg__type, cmdg__prix as  devg__prix
      FROM cmd_garantie  
      WHERE cmdg__id__cmdl = ". $id ."");
      $data = $request->fetchAll(PDO::FETCH_ASSOC);
      return $data;
    }

    public function magicRequestStatus($string , $status){

      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

      $operateur = 'AND ';

      $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi, cmd__modele_facture, cmd__id_facture , cmd__date_fact, 
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

      if ($mode_filtre) {
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
       $request .= "AND ( cmd__etat = '".$status."' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      else {
        $request .= "AND ( cmd__etat = '".$status."' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }


    public function magicRequest($string){

      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

      $operateur = 'AND ';

      $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi,
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

      if ($mode_filtre) {
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
        $request .= "ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      else {
        $request.=  "ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }




    public function magicRequestUserCMD($string , $user){

      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

      $operateur = 'AND ';

      $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      k.kw__lib,  cmd__date_envoi,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,  cmd__date_cmd,  cmd__date_envoi,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

      if ($mode_filtre) {
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
        $request .= " AND  ( cmd__etat = 'CMD' )  AND cmd__user__id_devis = '". $user. "'  ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      else {
        $request.=  " AND   ( cmd__etat = 'CMD' )  AND   cmd__user__id_devis = '". $user. "' ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }





    public function magicRequestUser($string , $user){

      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

      $operateur = 'AND ';

      $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      k.kw__lib,  cmd__date_envoi,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

      if ($mode_filtre) 
      {
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
        $request .= "AND ( cmd__user__id_devis = '".$user."' ) ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      else 
      {
        $request.=  "AND ( cmd__user__id_devis = '".$user."' ) ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }








    public function magicRequestCMD($string){

      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

      $operateur = 'AND ';

      $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi,
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

      if ($mode_filtre) {
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
       $request .= "AND ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      else {
        $request.=   "AND ( cmd__etat = 'CMD' OR cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }


    public function magicRequestFunnyBunny($string , $session  )
    {

      if (!empty($string)) {
        
      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

      $operateur = 'AND ';

      $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi,
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

      if ($mode_filtre) {
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
        if ($session == 'ALL' ) 
        {
          $request .= "AND ( cmd__etat = 'CMD' OR cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
        elseif ($session == 'FT') 
        {
          $request .= "AND ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
        elseif ($session == 'BL') 
        {
          $request .= "AND ( cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
      }
      else 
      {
        if ($session == 'ALL' ) 
        {
          $request .= "AND ( cmd__etat = 'CMD' OR cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
        elseif ($session == 'FT') 
        {
          $request .= "AND ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
        elseif ($session == 'BL') 
        {
          $request .= "AND ( cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
      }

      else{
        $request = "SELECT 
        cmd__id as devis__id ,
        cmd__user__id_devis as devis__user__id ,
        cmd__date_devis as devis__date_crea, 
        LPAD(cmd__client__id_fact ,6,0)   as client__id, 
        cmd__contact__id_fact  as  devis__contact__id,
        cmd__etat as devis__etat, 
        cmd__note_client as  devis__note_client , 
        cmd__note_interne as devis__note_interne,
        cmd__client__id_livr as devis__id_client_livraison ,
        cmd__contact__id_livr as  devis__contact_livraison , 
        cmd__date_cmd,  cmd__date_envoi,
        cmd__nom_devis, cmd__modele_devis , 
        k.kw__lib,
        t.contact__nom, t.contact__prenom, t.contact__email,
        c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
        c2.client__societe as client__livraison_societe,
        c2.client__ville as client__livraison_ville,
        c2.client__cp as client__livraison_cp , 
        c2.client__adr1 as client__livraison__adr1 , 
        u.log_nec , u.user__email_devis as email
        FROM cmd
        LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
        LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
        LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
        LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
        LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur ";

        if ($session == 'ALL' ) 
        {
          $request .= "WHERE  ( cmd__etat = 'CMD' OR cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
        elseif ($session == 'FT') 
        {
          $request .= "WHERE ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
        elseif ($session == 'BL') 
        {
          $request .= "WHERE ( cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }

      
        $send = $this->Db->Pdo->query($request);
        $data = $send->fetchAll(PDO::FETCH_OBJ);
        return $data;

      }

    }




    



}