<?php

namespace App\Api;

require_once  '././vendor/autoload.php';
use DateTime;
use App\Database;
use App\Tables\Cmd;
use App\Tables\Article;
use App\Tables\Client;
use App\Api\ResponseHandler;

class ApiCommandeTransfert{

    public static  function index($method)
    {
        $responseHandler = new ResponseHandler;
        switch ($method) {
            case 'POST':
                return self::post();
                break;
            default:
                return $responseHandler->handleJsonResponse([
                    'msg' =>  'Aucune opération n est prévue avec cette méthode'
                ], 404, 'Unknow');
                break;
        }
    }

    public static function post(){

        $responseHandler = new ResponseHandler;
        $Database = new Database('devis');
        $Database->DbConnect();
        $Cmd = new Cmd($Database);
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ], 401, 'bad request');
        } 
        if (empty($body['secret'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');
        } elseif (!empty($body['secret']) and $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');
        }

        return $responseHandler->handleJsonResponse([
            'msg' => 'debug'
        ], 999, 'debug');
        die();
        
        $test = self::checkBody($body);
        
        if ($test != false ) {
            return $responseHandler->handleJsonResponse([
                'msg' => $test
            ], 401, 'bad request');
        }
        
        $cmd__id = self::insertCmd($Database ,$body );
       
        $index = 1 ;
        foreach ($body['ligne'] as $value) {
            $temp = self::transformLigne($value,$cmd__id,$index ,$Database);
            $cmdl__id = self::insertLigne($Database , $temp , $index );
            $index ++ ;
            if (!empty($temp['cmdl__garantie_option'])) {
                self::insertgarantie($Database,$cmdl__id,$temp);
            }
        }
        return $responseHandler->handleJsonResponse([
            'data' => $cmd__id ,
        ], 200, $cmd__id);
    }

    public static function checkBody($body){

        if (empty($body['scm__user_id'])) {
           return 'scm__user_id est manquant ';
        }

        if (empty($body['scm__prix_port'])) {
            return 'scm__prix_port est manquant ';
        }

        if (empty($body['scm__client_id_livr'])) {
            return 'scm__client_id_livr est manquant ';
        }

        if (empty($body['scm__client_id_fact'])) {
            return 'scm__client_id_fact est manquant ';
        }

        if (empty($body['ligne'])) {
            return 'ligne est manquant ';
        }

        if (!is_array($body['ligne']) ) {
            return 'ligne n est pas un tableau ';
        }

        foreach ($body['ligne'] as $value) {
            $temp = self::checkLigneCmd($value);
            if ($temp != false) {
                return $temp;
            }
        }

        return false;
    }

    public static function checkLigneCmd($ligne){

        if (empty($ligne['scl__ref_id'])) {
            return 'Ref id absente';
        }
        if (empty($ligne['scl__prix_unit'])) {
            return 'scl__prix_unit';
        }
        if (empty($ligne['scl__qte'])) {
            return 'scl__qte';
        }
        if (empty($ligne['sar__description'])) {
            return 'sar__description';
        }
        if (empty($ligne['sav__etat'])) {
            return 'sav__etat';
        }
        if (empty($ligne['sav__gar_std'])) {
            return 'sav__gar_std';
        }
        if (empty($ligne['sar__ref_constructeur'])) {
            return 'sar__ref_constructeur';
        }
        if (empty($ligne['sar__ref_constructeur'])) {
            return 'sar__ref_constructeur';
        }

        return false;
    }

    public static  function transformLigne($ligne , $cmd__id , $index , $database ){

        $Article = new Article($database);
        $pn = $Article->get_pn_long($ligne['sar__ref_constructeur']);
        if ($pn['apn__famille'] != 'ACC') {
            $id_fmm = $Article->return_id_fmm_for_myrecode($pn['apn__pn']);
            if (empty($id_fmm)) {
                $id_fmm = 101 ;
            }else{
                $id_fmm = $id_fmm['id__fmm']  ;
            }
            
        }else { 
            $id_fmm = 101 ;
        }

        $results = [];
        $results['cmdl__cmd__id'] = $cmd__id;
        $results['cmdl__ordre'] = $index;
        $results['cmdl__prestation'] = 'VTE';
        $results['cmdl__id__fmm'] = $id_fmm; 
        $results['cmdl__pn'] = $pn['apn__pn'];
        $results['cmdl__designation'] = $ligne['sar__description'];
        $results['cmdl__etat'] = $ligne['sav__etat'];
        $results['cmdl__garantie_base'] = $ligne['sav__gar_std'];
        $results['cmdl__qte_cmd'] = $ligne['scl__qte'];
        $results['cmdl__puht'] = $ligne['scl__prix_unit'];

        if (!empty($ligne['scl__gar_mois'])) {
            $results['cmdl__garantie_option'] = $ligne['scl__gar_mois'];
            $results['cmdl__garantie_puht'] = $ligne['scl__gar_prix'];
        }else {
            $results['cmdl__garantie_option'] = null;
            $results['cmdl__garantie_puht'] = null;
        }
      
        return $results;

    }

    public static function insertgarantie($Db , $cmdl__id , $ligne){
        $request = $Db->Pdo->prepare('INSERT INTO cmd_garantie 
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

            $request->bindValue(":cmdg__ordre", 1);
            $request->bindValue(":cmdg__id__cmdl", $cmdl__id);
            $request->bindValue(":cmdg__type", $ligne['cmdl__garantie_option']);
            $request->bindValue(":cmdg__prix", $ligne['cmdl__garantie_puht']);
            $request->bindValue(":cmdg__prix_barre",null);
            $request->execute();
        $id = $Db->Pdo->lastInsertId();
        return $id;
    }


    public static function insertLigne( $Db , $ligne , $index){
        $request = $Db->Pdo->prepare('INSERT INTO cmd_ligne 
        (cmdl__ordre, cmdl__cmd__id,  cmdl__prestation, cmdl__id__fmm, cmdl__designation, cmdl__etat, cmdl__garantie_base, cmdl__qte_cmd, cmdl__puht, cmdl__pn)
        VALUES (:cmdl__ordre, 
        :cmdl__cmd__id, 
        :cmdl__prestation, 
        :cmdl__id__fmm, 
        :cmdl__designation, 
        :cmdl__etat, 
        :cmdl__garantie_base, 
        :cmdl__qte_cmd,
        :cmdl__puht ,
        :cmdl__pn )');
        $request->bindValue(":cmdl__ordre", $index);
        $request->bindValue(":cmdl__cmd__id",  $ligne['cmdl__cmd__id']);
        $request->bindValue(":cmdl__prestation", $ligne['cmdl__prestation']);
        $request->bindValue(":cmdl__id__fmm", $ligne['cmdl__id__fmm']);
        $request->bindValue(":cmdl__designation", $ligne['cmdl__designation']);
        $request->bindValue(":cmdl__etat", $ligne['cmdl__etat']);
        $request->bindValue(":cmdl__garantie_base",  $ligne['cmdl__garantie_base']);
        $request->bindValue(":cmdl__qte_cmd",$ligne['cmdl__qte_cmd']);
        $request->bindValue(":cmdl__puht", $ligne['cmdl__puht']);
        $request->bindValue(":cmdl__pn", $ligne['cmdl__pn']);
        $request->execute();
        $id = $Db->Pdo->lastInsertId();
        return $id;
    }

    public static function insertCmd($Db , $body ){
        $Client = new Client($Db );
        
        $date = date("Y-m-d H:i:s");
        $client = $Client->getOne($body['scm__client_id_fact']);
        $request = $Db->Pdo->prepare('INSERT INTO cmd 
        (cmd__date_devis,   cmd__user__id_devis,    cmd__client__id_fact,   cmd__client__id_livr,     cmd__etat,  cmd__modele_devis,  cmd__tva,   cmd__nom_devis)
        VALUES (:cmd__date_devis, :cmd__user__id_devis, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__etat, :cmd__modele_devis, :cmd__tva, :cmd__nom_devis)');
        $request->bindValue(":cmd__date_devis", $date);
        $request->bindValue(":cmd__user__id_devis", $body['scm__user_id']);
        $request->bindValue(":cmd__client__id_fact", $body['scm__client_id_fact']);
        $request->bindValue(":cmd__client__id_livr", $body['scm__client_id_livr']);
        $request->bindValue(":cmd__modele_devis","STT");
        $request->bindValue(":cmd__tva", $client->client__tva);
        $request->bindValue(":cmd__nom_devis", "Commande MyRecode");
        $request->bindValue(":cmd__etat", 'ATN');
        $request->execute();
        $id = $Db->Pdo->lastInsertId();
        return $id;
    }
}