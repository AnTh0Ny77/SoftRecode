<?php


namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Methods\Pdfunctions;
use PDO;


class Devis extends Table {
  
  public Database $Db;
  
  public function __construct($db) {
    $this->Db = $db;
  }



  public function createDevis($date, $user, $clientF, $clientL, $contactF, $contactL, $noteC, $noteI, $model, $tva, $nom ,$code)
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO cmd 
    (cmd__date_devis, 
    cmd__user__id_devis, 
    cmd__client__id_fact, 
    cmd__client__id_livr, 
    cmd__contact__id_fact, 
    cmd__contact__id_livr, 
    cmd__code_cmd_client, 
    cmd__note_client,
    cmd__note_interne,
    cmd__etat,
    cmd__modele_devis,
    cmd__tva,
    cmd__nom_devis)
    VALUES (:cmd__date_devis, 
    :cmd__user__id_devis, 
    :cmd__client__id_fact, 
    :cmd__client__id_livr, 
    :cmd__contact__id_fact, 
    :cmd__contact__id_livr, 
    :cmd__code_cmd_client, 
    :cmd__note_client,
    :cmd__note_interne,
    :cmd__etat,
    :cmd__modele_devis,
    :cmd__tva,
    :cmd__nom_devis)');
    $request->bindValue(":cmd__date_devis", $date);
    $request->bindValue(":cmd__user__id_devis", $user);
    $request->bindValue(":cmd__client__id_fact", $clientF);
    $request->bindValue(":cmd__client__id_livr", $clientL);
    $request->bindValue(":cmd__contact__id_fact", $contactF);
    $request->bindValue(":cmd__contact__id_livr", $contactL);
    $request->bindValue(":cmd__note_client", $noteC);
    $request->bindValue(":cmd__note_interne", $noteI);
    $request->bindValue(":cmd__code_cmd_client", $code);
    $request->bindValue(":cmd__modele_devis", $model);
    $request->bindValue(":cmd__tva", $tva);
    $request->bindValue(":cmd__nom_devis", $nom);
    $request->bindValue(":cmd__etat", 'PBL');
    $request->execute();
    $id = $this->Db->Pdo->lastInsertId();
    return $id;
  }


  public function insertLine($ordre , $idCmd , $prestation, $fmm , $designation , $etat , $garantie , $qte , $prixBarre , $puht , $noteC , $noteI  )
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO cmd_ligne 
    (cmdl__ordre, 
    cmdl__cmd__id, 
    cmdl__prestation, 
    cmdl__id__fmm, 
    cmdl__designation, 
    cmdl__etat, 
    cmdl__garantie_base, 
    cmdl__qte_cmd,
    cmdl__prix_barre,
    cmdl__puht,
    cmdl__note_client,
    cmdl__note_interne)
    VALUES (:cmdl__ordre, 
    :cmdl__cmd__id, 
    :cmdl__prestation, 
    :cmdl__id__fmm, 
    :cmdl__designation, 
    :cmdl__etat, 
    :cmdl__garantie_base, 
    :cmdl__qte_cmd,
    :cmdl__prix_barre,
    :cmdl__puht,
    :cmdl__note_client,
    :cmdl__note_interne)');
    $request->bindValue(":cmdl__ordre", $ordre);
    $request->bindValue(":cmdl__cmd__id", $idCmd);
    $request->bindValue(":cmdl__prestation", $prestation);
    $request->bindValue(":cmdl__id__fmm", $fmm);
    $request->bindValue(":cmdl__designation", $designation);
    $request->bindValue(":cmdl__etat", $etat);
    $request->bindValue(":cmdl__garantie_base",  $garantie);
    $request->bindValue(":cmdl__qte_cmd",$qte);
    $request->bindValue(":cmdl__prix_barre", $prixBarre);
    $request->bindValue(":cmdl__puht", $puht);
    $request->bindValue(":cmdl__note_client", $noteC);
    $request->bindValue(":cmdl__note_interne", $noteI);
    $request->execute();
    $id = $this->Db->Pdo->lastInsertId();
    return $id;

  }


}