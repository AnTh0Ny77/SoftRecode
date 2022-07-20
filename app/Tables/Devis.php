<?php


namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Tables\General;
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

  public function get_line_by_id($id)
  {
    $request =$this->Db->Pdo->query("SELECT
    cmdl__cmd__id,
    cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
    cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
    cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
    cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
    cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
    cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
    cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
    cmdl__etat_masque, cmdl__image, cmdl__actif ,
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
    ");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }

  public function create_daugther_line($mother_line , $select_modele, $designation , $quantite , $commentaire)
  {
    $verifOrdre = $this->Db->Pdo->query('SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne WHERE cmdl__cmd__id = '.$mother_line->cmdl__cmd__id.' ');
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
    cmdl__puht,
    cmdl__note_interne,
    cmdl__sous_ref)
    VALUES (:cmdl__ordre, 
    :cmdl__cmd__id, 
    :cmdl__prestation, 
    :cmdl__id__fmm, 
    :cmdl__designation, 
    :cmdl__etat, 
    :cmdl__garantie_base, 
    :cmdl__qte_cmd,
    :cmdl__puht,
    :cmdl__note_interne,
    :cmdl__sous_ref)');
    $request->bindValue(":cmdl__ordre", $ordreMax);
    $request->bindValue(":cmdl__cmd__id", $mother_line->cmdl__cmd__id);
    $request->bindValue(":cmdl__prestation", $mother_line->devl__type);
    $request->bindValue(":cmdl__id__fmm", $select_modele);
    $request->bindValue(":cmdl__designation", $designation);
    $request->bindValue(":cmdl__etat", $mother_line->devl__etat);
    $request->bindValue(":cmdl__garantie_base",  $mother_line->devl__mois_garantie);
    $request->bindValue(":cmdl__qte_cmd",$quantite);
    $request->bindValue(":cmdl__puht", 00);
    $request->bindValue(":cmdl__note_interne", $commentaire);
    $request->bindValue(":cmdl__sous_ref",$mother_line->devl__id);
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
      $list = $this->Db->Pdo->query("SELECT *  from cmd_ligne WHERE cmdl__cmd__id = '".$idCmd."' AND cmdl__ordre > '".$ordre."' AND cmdl__sous_ref IS NULL  ORDER BY cmdl__ordre LIMIT 1 ");
    }
    else 
    {
      $list = $this->Db->Pdo->query("SELECT *  from cmd_ligne WHERE cmdl__cmd__id = '".$idCmd."' AND cmdl__ordre < '".$ordre."' AND cmdl__sous_ref IS NULL  ORDER BY cmdl__ordre DESC LIMIT 1 ");
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

  public function set_order($idCmd){
    $list = $this->Db->Pdo->query("SELECT cmdl__ordre ,   cmdl__id
    from cmd_ligne 
    WHERE cmdl__cmd__id = '" . $idCmd . "' AND cmdl__sous_ref IS NULL ORDER BY cmdl__ordre ");
    $list = $list->fetchAll(PDO::FETCH_OBJ);

    if (!empty($list)) {
		$i = 0 ;
		foreach($list as $line){
			$i ++;
				$update = $this->Db->Pdo->prepare('UPDATE cmd_ligne 
					SET cmdl__ordre=? 
					WHERE cmdl__id =?');
				$update->execute([$i, $line->cmdl__id]);
		}
		return true;
    }
	return false;
  }

  public function new_order($idCmd , $idLine , $index){
		$General = new General($this->Db);

		$lineIndex  =$this->Db->Pdo->query("SELECT cmdl__ordre ,   cmdl__id
		from cmd_ligne 
		WHERE  cmdl__id = '" . $idLine . "' AND cmdl__sous_ref IS NULL");
		$lineIndex = $lineIndex->fetch(PDO::FETCH_OBJ);
		$General->updateAll('cmd_ligne', intval($index), 'cmdl__ordre', 'cmdl__id', $lineIndex->cmdl__id);
	
		if ($lineIndex->cmdl__ordre > $index ) {
			$up = $this->Db->Pdo->query("SELECT cmdl__ordre ,   cmdl__id
			from cmd_ligne 
			WHERE cmdl__id <> '" . $lineIndex->cmdl__id . "'  AND   cmdl__ordre < '" . $index . "' AND  cmdl__cmd__id = '" . $idCmd . "' AND cmdl__sous_ref IS NULL ORDER BY cmdl__ordre ");
			$up = $up->fetchAll(PDO::FETCH_OBJ);
			foreach ($up as $line) {
					$General->updateAll('cmd_ligne', intval($line->cmdl__id + 1), 'cmdl__ordre', 'cmdl__id', $line->cmdl__id);
			}
		}elseif($lineIndex->cmdl__ordre < $index){
			$down = $this->Db->Pdo->query("SELECT cmdl__ordre ,   cmdl__id
			from cmd_ligne 
			WHERE  cmdl__id <> '" . $lineIndex->cmdl__id . "'  AND cmdl__ordre > '" . $index . "' AND  cmdl__cmd__id = '" . $idCmd . "' AND cmdl__sous_ref IS NULL ORDER BY cmdl__ordre ");
			$down = $down->fetchAll(PDO::FETCH_OBJ);
			foreach ($down as $line) {
					$General->updateAll('cmd_ligne',intval($line->cmdl__id -  1), 'cmdl__ordre', 'cmdl__id', $line->cmdl__id);
			}
		}

		
  }


  public function deleteLine($lineId)
  {
    $request = $this->Db->Pdo->prepare("DELETE FROM cmd_ligne 
    WHERE cmdl__id = '".$lineId."' OR cmdl__sous_ref = '".$lineId."'");
    $request->execute();
  }

  public function getRemise($idCmd)
  {
    $array = $this->Db->Pdo->query("SELECT cmdl__prix_barre  from cmd_ligne WHERE cmdl__cmd__id = ". $idCmd ."");
    $response = $array->fetchAll(PDO::FETCH_OBJ);
    return $response;
  }


}