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


    public function createOne($cmd , $client , $actif , $auto , $presta , $note , $mois  )
    {
    //     $BigAbn= $this->Db->Pdo->query('SELECT MAX(ab__contrat) as lastFact from ab__contrat');
    //     $BigAbn = $BigAbn->fetch(PDO::FETCH_OBJ);

    //     $newAbn = $BigAbn->lastFact + 1;

        $request = $this->Db->Pdo->prepare('INSERT INTO abonnement ( ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_periode,  ab__presta,
        ab__note, ab__mois_engagement )
        VALUES (:ab__cmd__id, :ab__client__id_fact, :ab__actif, :ab__fact_periode, :ab__presta,
        :ab__note, :ab__mois_engagement)');

        $request->bindValue(":ab__cmd__id", $cmd);
        $request->bindValue(":ab__client__id_fact", $client);
        $request->bindValue(":ab__actif", $actif);
        $request->bindValue(":ab__fact_periode", $auto);
        $request->bindValue(":ab__presta",$presta);
        $request->bindValue(":ab__note", $note);   
        $request->bindValue(":ab__mois_engagement", $mois);
       

        $request->execute();

        $idABN = $this->Db->Pdo->lastInsertId();

        return $idABN;
    }

    public function UpdateAbn($cmd , $actif , $presta , $note , $mois)
    {
        $arrayRequest = [ $cmd  , $actif , $presta , $note , $mois , $cmd];

        $request = $this->Db->Pdo->prepare('UPDATE abonnement
        SET ab__cmd__id = ?,
            ab__actif = ?,
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
        ab__actif, ab__fact_periode,  ab__presta , ab__mois_engagement , ab__note , ab__date_anniv ,
        k.kw__lib as prestaionAbn
        FROM abonnement
        LEFT JOIN keyword as k ON ab__presta = k.Kw__value AND k.kw__type = 'abt'
        WHERE ab__cmd__id = ".$id."
        ORDER BY  ab__cmd__id DESC LIMIT 200 ");
        $data = $request->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    public function getAll()
    {
        $request =$this->Db->Pdo->query("SELECT  ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_periode,  ab__presta , ab__mois_engagement
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
        a.am__marque as marque ,
        k2.kw__info as info_presta
        FROM abonnement_ligne
        LEFT JOIN art_fmm as f ON afmm__id = abl__id__fmm
        LEFT JOIN keyword as k ON f.afmm__famille = k.kw__value AND k.kw__type = 'famil'
        LEFT JOIN keyword as k2 ON k2.kw__value = abl__type_repair AND  ( k2.kw__type = 'abl' OR k2.kw__type = 'abm' OR k2.kw__type = 'abt')
        LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
        WHERE abl__cmd__id = ".$id."
        ORDER BY  abl__ligne ASC LIMIT 500");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function getLigneActives($id){

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
        ORDER BY  abl__ligne ASC LIMIT 500 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;

    }

    
    public function getOneLigne($id , $num){
        
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

    public function verify_sn($sn , $cmd_id)
    {
        $request = $this->Db->Pdo->query("SELECT 
        abl__sn
        FROM abonnement_ligne
        WHERE  ( abl__sn = '".$sn."'  )  AND ( abl__cmd__id = '". $cmd_id."' ) 
        ");
        $data = $request->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    public function returnMax($cmd)
    {
        $verifOrdre = $this->Db->Pdo->query(
            'SELECT MAX(abl__ligne) as ligne from abonnement_ligne WHERE abl__cmd__id = '.$cmd.'');
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
        ab__actif, ab__fact_periode,  ab__presta , ab__mois_engagement , k.kw__info  as prestaLib , ab__note
        FROM abonnement
        LEFT JOIN keyword as k ON ab__presta = k.kw__value AND k.kw__type = 'abt'
        WHERE ab__actif = 1 
        ORDER BY  ab__cmd__id DESC LIMIT 200 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    



    public function getActif()
    {
        $request =$this->Db->Pdo->query("SELECT  ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_periode,  ab__presta , ab__mois_engagement , k.kw__lib as prestaLib
        FROM abonnement
        LEFT JOIN keyword as k ON ab__presta = k.kw__value AND k.kw__type = 'pres'
        WHERE ab__actif = 1 
        ORDER BY  ab__cmd__id DESC LIMIT 200 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function get_actif_for_fact($mois)
    {

        $request =$this->Db->Pdo->query("SELECT  ab__cmd__id, ab__client__id_fact, 
        ab__actif, ab__fact_periode,  ab__presta , ab__mois_engagement , k.kw__lib as prestaLib , ab__note , MONTH(ab__date_anniv)  as ab__date_anniv
        FROM abonnement
        LEFT JOIN keyword as k ON ab__presta = k.kw__value AND k.kw__type = 'pres'
        WHERE (ab__actif = 1) AND ( ab__date_anniv IS NOT NULL )
        ORDER BY  ab__cmd__id DESC LIMIT 2000 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);

        $array_response = [];

        foreach ($data  as $abn) 
        {
                //nombre de fatcuration sur 24 mois en rapport avec la période ( ex : 4 fois trimestres )
                $nombre_itteration =  18 / $abn->ab__fact_periode  ;
                $sql_condition ='';
               
                //tant que i est plus petit que ou égal au nombre de facturation prévues : 
                for ($i= 1 ; $i <= $nombre_itteration ; $i++) 
                {     
                        //represente le nombre de mois d'ecart entre les facturation ex : 
                        // trimestre 2 ème iteration i = 2 -  2 X 3 = 6 : 
                        $multiple_fact_periode  = ($abn->ab__fact_periode * $i )  +  $abn->ab__date_anniv ;
                        
                        //si l'ecart en mois + le mois courant est plus petit ou égal à 12 ( moins d'une année ) 
                        if ($multiple_fact_periode   < 12 ) 
                        {
                            // echo($abn->ab__date_anniv . ' anniversaire <br> ');
                            // echo($abn->ab__fact_periode . ' fact tous les  <br>');
                            // echo($multiple_fact_periode   . '   mois de prélevemnt  <br> ');
                            // echo($mois . ' mois actuell <br> ');
                          
                            // si  le mois anniv + lecart en mois entre les echeance est egal au mois demandé :   
                            $sql_condition .='OR '. $multiple_fact_periode .' =   '.$mois.' ';
                        } 
                        //a l'inverse on depasse le mois 12 donc on cherche l'année suivante en aditionnant le mois recherché a 12 pour mois: 13-14 etc:  
                        else 
                        { 
                            $mois_sup =  12 + $mois ;
                            // echo($abn->ab__date_anniv . ' anniversaire <br> ');
                            // echo($abn->ab__fact_periode . ' fact tous les  <br>');
                            // echo($multiple_fact_periode   . '   mois de prélevemnt  <br> ');
                            // echo($mois_sup . ' mois actuell <br> ');
                            $sql_condition .='OR  '. $multiple_fact_periode .' =   '.$mois_sup.' ';
                        }      
                }
            //    echo('<br><br>');
               
                $request_abn = $this->Db->Pdo->query("SELECT  ab__cmd__id, ab__client__id_fact, 
                ab__actif, ab__fact_periode,  ab__presta , ab__date_anniv, ab__mois_engagement , k.kw__lib as prestaLib , ab__note
                FROM abonnement
                LEFT JOIN keyword as k ON ab__presta = k.kw__value AND k.kw__type = 'pres'
                WHERE (ab__actif = 1) AND ( MONTH(ab__date_anniv)  = "  . $mois  ."    " . $sql_condition .")
                AND ab__cmd__id = ".$abn->ab__cmd__id."
                ORDER BY  ab__cmd__id DESC LIMIT 2000 ");
                $data = $request_abn->fetch(PDO::FETCH_OBJ);
                
                if (!empty($data)) 
                {
                    $data->ab__note = ' Echeance : ' . $data->ab__date_anniv .' Facturé tous les : ' . $data->ab__fact_periode . ' Mois en cours : ' . $mois ;
                    array_push($array_response,$data );
                }    
        }
        // die();
       return $array_response;
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