<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Abonnement extends Table 
{ 

    public Database $Db;

    public function __construct($db) 
    {
        $this->Db = $db;
    }


    public function createOne($cmd , $client , $actif , $auto , $presta , $note , $mois )
    {
        $request = $this->Db->Pdo->prepare('INSERT INTO abonnement ( ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_auto,  ab__presta,
        ab__note, ab__mois_engagement)
        VALUES (:ab__cmd__id, :ab__client__id_fact, :ab__actif, :ab__fact_auto, :ab__presta,
        :ab__note, :ab__mois_engagement)');

        $request->bindValue(":ab__cmd__id", $cmd);
        $request->bindValue(":ab__client__id_fact", $client);
        $request->bindValue(":ab__actif", $actif);
        $request->bindValue(":ab__fact_auto", $auto);
        $request->bindValue(":ab__presta",$presta);
        $request->bindValue(":ab__note", $note);   
        $request->bindValue(":ab__mois_engagement", $mois);

        $request->execute();

        $idABN = $this->Db->Pdo->lastInsertId();

        return $idABN;
    }

    public function UpdateAbn($cmd , $actif , $auto , $presta , $note , $mois)
    {
        $arrayRequest = [ $cmd  , $actif , $auto , $presta , $note , $mois , $cmd];

        $request = $this->Db->Pdo->prepare('UPDATE abonnement
        SET ab__cmd__id = ?,
            ab__actif = ?,
            ab__fact_auto = ?,
            ab__presta = ?,
            ab__note = ?,
            ab__mois_engagement= ?
        WHERE ab__cmd__id = ? ');

        $request->execute($arrayRequest);
        $idABN = $this->Db->Pdo->lastInsertId();
        return $idABN;
    }

    public function getById($id)
    {
        $request =$this->Db->Pdo->query("SELECT  ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_auto,  ab__presta , ab__mois_engagement , ab__note
        FROM abonnement
        WHERE ab__cmd__id = ".$id."
        ORDER BY  ab__cmd__id DESC LIMIT 200 ");
        $data = $request->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    public function getAll()
    {
        $request =$this->Db->Pdo->query("SELECT  ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_auto,  ab__presta , ab__mois_engagement
        FROM abonnement
        ORDER BY  ab__cmd__id DESC LIMIT 200 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function getLigne($id)
    {
        $request =$this->Db->Pdo->query("SELECT 
        abl__cmd__id , abl__ligne , abl__dt_debut , abl__actif, abl__id__fmm, 
        abl__designation, abl__sn, abl__type_repair, abl__prix_mois, abl__note_interne,
        f.afmm__famille as famille,
        f.afmm__modele as modele,
        k.kw__lib as famille__lib,
        a.am__marque as marque
        FROM abonnement_ligne
        LEFT JOIN art_fmm as f ON afmm__id = abl__id__fmm
        LEFT JOIN keyword as k ON f.afmm__famille = k.kw__value AND k.kw__type = 'famil'
        LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
        WHERE abl__cmd__id = ".$id."
        ORDER BY  abl__ligne ASC LIMIT 200 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function getLigneActives($id)
    {
        $request =$this->Db->Pdo->query("SELECT 
        abl__cmd__id , abl__ligne , abl__dt_debut , abl__actif, abl__id__fmm, 
        abl__designation, abl__sn, abl__type_repair, abl__prix_mois, abl__note_interne,
        f.afmm__famille as famille,
        f.afmm__modele as modele,
        k.kw__lib as famille__lib,
        a.am__marque as marque
        FROM abonnement_ligne
        LEFT JOIN art_fmm as f ON afmm__id = abl__id__fmm
        LEFT JOIN keyword as k ON f.afmm__famille = k.kw__value AND k.kw__type = 'famil'
        LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
        WHERE abl__cmd__id = ".$id." AND abl__actif = 1 
        ORDER BY  abl__ligne ASC LIMIT 200 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    

    public function getOneLigne($id , $num)
    {
        $request =$this->Db->Pdo->query("SELECT 
        abl__cmd__id , abl__ligne , abl__dt_debut , abl__actif, abl__id__fmm, 
        abl__designation, abl__sn, abl__type_repair, abl__prix_mois, abl__note_interne,
        f.afmm__famille as famille,
        f.afmm__modele as modele,
        k.kw__lib as famille__lib,
        a.am__marque as marque
        FROM abonnement_ligne
        LEFT JOIN art_fmm as f ON afmm__id = abl__id__fmm
        LEFT JOIN keyword as k ON f.afmm__famille = k.kw__value AND k.kw__type = 'famil'
        LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
        WHERE abl__cmd__id = ".$id." AND abl__ligne = ".$num."
        ");
        $data = $request->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    public function returnMax($cmd)
    {
        $verifOrdre = $this->Db->Pdo->query(
            'SELECT MAX(abl__ligne) as ligne from abonnement_ligne');
        $response  = $verifOrdre->fetch(PDO::FETCH_OBJ);
        return $response;
    }

    public function insertMachine($idCmd , $numeroLigne , $datedeDebut , $idFmm , $designation , $sn , $type , $prix , $note)
    {
        $request = $this->Db->Pdo->prepare('INSERT INTO abonnement_ligne ( abl__cmd__id , abl__ligne,
        abl__dt_debut, abl__actif,  abl__id__fmm,
        abl__designation, abl__sn , abl__type_repair , abl__prix_mois , abl__note_interne)
        VALUES (:abl__cmd__id, :abl__ligne, :abl__dt_debut, :abl__actif, :abl__id__fmm,
        :abl__designation, :abl__sn , :abl__type_repair , :abl__prix_mois , :abl__note_interne)');

        $request->bindValue(":abl__cmd__id", $idCmd);
        $request->bindValue(":abl__ligne", $numeroLigne);
        $request->bindValue(":abl__dt_debut", $datedeDebut);
        $request->bindValue(":abl__actif", 1 );
        $request->bindValue(":abl__id__fmm",$idFmm);
        $request->bindValue(":abl__designation", $designation);   
        $request->bindValue(":abl__sn", $sn);
        $request->bindValue(":abl__type_repair", $type);
        $request->bindValue(":abl__prix_mois", $prix);
        $request->bindValue(":abl__note_interne",  $note);

        $request->execute();

        $idABN = $this->Db->Pdo->lastInsertId();

        return $idABN;
    }

    public function UpdateMachine($idCmd , $numeroLigne , $datedeDebut , $actif ,   $idFmm , $designation , $sn , $type , $prix , $note)
    {
        $arrayRequest = [ $idCmd , $numeroLigne , $datedeDebut , $actif ,   $idFmm , $designation , $sn , $type , $prix , $note , $idCmd , $numeroLigne];

        $request = $this->Db->Pdo->prepare('UPDATE abonnement_ligne
        SET abl__cmd__id = ?,
            abl__ligne = ?,
            abl__dt_debut = ?,
            abl__actif = ?,
            abl__id__fmm = ?, 
            abl__designation = ?, 
            abl__sn = ?,
            abl__type_repair = ?, 
            abl__prix_mois = ?, 
            abl__note_interne = ?
        WHERE abl__cmd__id = ? 
        AND abl__ligne = ?');

        $request->execute($arrayRequest);
        $idABN = $this->Db->Pdo->lastInsertId();
        return $idABN;
    }


    public function getActifAndFacturable()
    {
        $request =$this->Db->Pdo->query("SELECT  ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_auto,  ab__presta , ab__mois_engagement , k.kw__lib as prestaLib
        FROM abonnement
        LEFT JOIN keyword as k ON ab__presta = k.kw__value AND k.kw__type = 'pres'
        WHERE ab__actif = 1 AND ab__fact_auto = 1 
        ORDER BY  ab__cmd__id DESC LIMIT 200 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }


    public function getLigneFacturableAuto($idAbn , $dateDebut)
    {
        $request =$this->Db->Pdo->query("SELECT  *
        FROM abonnement_ligne
        WHERE abl__actif = 1 
        AND abl__dt_debut < '".$dateDebut."'
        AND abl__cmd__id = '".$idAbn."'
        ORDER BY  abl__ligne DESC LIMIT 500");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function getLigneFacturableAutoBetween2Dates($idAbn , $dateDebut, $dateFin)
    {
        $request =$this->Db->Pdo->query("SELECT  *
        FROM abonnement_ligne
        WHERE abl__actif = 1 
        AND abl__dt_debut > '".$dateDebut."' AND abl__dt_debut < '".$dateFin."'
        AND abl__cmd__id = '".$idAbn."'
        ORDER BY  abl__ligne DESC LIMIT 500");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function DiffDate($dateDebut, $dateFin)
    {
        $request =$this->Db->Pdo->query("SELECT DATEDIFF('".$dateDebut."', '".$dateFin."') as JourDiff");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

   
}