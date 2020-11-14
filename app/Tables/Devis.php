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


  public function insertLine( $idCmd , $prestation, $fmm , $designation , $etat , $garantie , $qte , $prixBarre , $puht , $noteC , $noteI  )
  {
    $verifOrdre = $this->Db->Pdo->query('SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne WHERE cmdl__cmd__id = '.$idCmd.' ');

    $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
    $ordreMax = $ordreMax->maxOrdre + 1 ;

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
    $request->bindValue(":cmdl__ordre", $ordreMax);
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


  public function insertGaranties( $idCmdl , $type, $prix , $promo  )
  {
    $verifOrdre = $this->Db->Pdo->query('SELECT MAX(cmdg__ordre) as maxOrdre from cmd_garantie WHERE cmdg__id__cmdl = '.$idCmdl.' ');

    $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
    $ordreMax = $ordreMax->maxOrdre + 1 ;

    $request = $this->Db->Pdo->prepare('INSERT INTO cmd_garantie 
    (cmdg__ordre, 
    cmdg__id__cmdl, 
    cmdg__type, 
    cmdg__prix, 
    cmdg__prix_barre
    )
    VALUES (:cmdg__ordre, 
    :cmdg__id__cmdl, 
    :cmdg__type, 
    :cmdg__prix, 
    :cmdg__prix_barre)');

    $request->bindValue(":cmdg__ordre", $ordreMax);
    $request->bindValue(":cmdg__id__cmdl", $idCmdl);
    $request->bindValue(":cmdg__type", $type);
    $request->bindValue(":cmdg__prix", $prix);
    $request->bindValue(":cmdg__prix_barre", $promo);
    $request->execute();
    $id = $this->Db->Pdo->lastInsertId();
    return $id;

  }

  public function selectGaranties($idCmdl)
  {
    $list = $this->Db->Pdo->query('SELECT * from cmd_garantie WHERE cmdg__id__cmdl = '.$idCmdl.' ');
    $response = $list->fetchAll(PDO::FETCH_OBJ);
    return $response;
  }

  public function selecOneLine($idCmdl)
  {
    $request = $this->Db->Pdo->query('SELECT * from cmd_ligne WHERE cmdl__id = '.$idCmdl.' ');
    $response = $request->fetch(PDO::FETCH_OBJ);
    return $response;
  }

  public function deleteGarantie($id)
  {
    $request = $this->Db->Pdo->prepare("DELETE FROM cmd_garantie 
    WHERE cmdg__id__cmdl = '".$id."'");
    $request->execute();
  }


  public function upanDonwn($option, $idCmd , $idLigne,$ordre)
  {

    if ($option == 'down') 
    {
      $list = $this->Db->Pdo->query("SELECT *  from cmd_ligne WHERE cmdl__cmd__id = '".$idCmd."' AND cmdl__ordre > '".$ordre."' ORDER BY cmdl__ordre LIMIT 1 ");
    }
    else 
    {
      $list = $this->Db->Pdo->query("SELECT *  from cmd_ligne WHERE cmdl__cmd__id = '".$idCmd."' AND cmdl__ordre < '".$ordre."' ORDER BY cmdl__ordre DESC LIMIT 1 ");
    }
    
    $response = $list->fetch(PDO::FETCH_OBJ);
    
   

    if (!empty($response)) 
    {

      $ordreFirst = $ordre;
      $ordreSecond = $response->cmdl__ordre;

      $update2 = $this->Db->Pdo->prepare(
        'UPDATE cmd_ligne 
         SET cmdl__ordre=? 
         WHERE cmdl__id =?');
      $update2->execute([ $ordreSecond , $idLigne]);


      $update = $this->Db->Pdo->prepare(
        'UPDATE cmd_ligne 
         SET cmdl__ordre=? 
         WHERE cmdl__id =?');
      $update->execute([$ordreFirst ,$response->cmdl__id]);

      return true;
    }

    else
    {
      return false ;
    }
  }

  public function deleteLine($lineId)
  {
    $request = $this->Db->Pdo->prepare("DELETE FROM cmd_ligne 
    WHERE cmdl__id = '".$lineId."'");
    $request->execute();
  }


}