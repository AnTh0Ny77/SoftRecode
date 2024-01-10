<?php

namespace App\Api;

use DateTime;
use App\Database;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
use App\Methods\Devis_functions;
use App\Tables\Cmd;
use App\Api\ResponseHandler;
use Pdo;


class ApiPlanning
{

    public static  function index($method)
    {
        $responseHandler = new ResponseHandler;
        switch ($method) {
            case 'GET':
                return self::get();
                break;
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

    public static function get(){
        $responseHandler = new ResponseHandler;
        if (!empty($_GET['user'])) {
            $list_time = self::getUserTimes($_GET['user']);
            return $responseHandler->handleJsonResponse([
                'data' =>  $list_time 
            ], 200, 'OK');
        }

        $updateValidation = self::updateValidation();

        $list_time = self::getTimes();


        return $responseHandler->handleJsonResponse([
            'data' =>  $list_time 
        ], 200, 'OK');
    }

    public static function post(){

        $responseHandler = new ResponseHandler;
        $body = json_decode(file_get_contents('php://input'), true);
        if (!empty($body['cadre']) and !empty($body['abs__id'])) {
            $data = [
                'ANL' ,
                $body['cadre'] , 
                $body['motif']
            ]; 
            self::refuseAbs($data,$body['abs__id']);
            return $responseHandler->handleJsonResponse([
                'data' =>  true
            ], 200, 'OK');
        }

        if (!empty($body['annul_user_id']) and !empty($body['annul_abs_id'])) {
            $data = [
                'ANL' ,
                $body['annul_user_id'] , 
                'Annulation par le demandeur'
            ]; 
            self::refuseAbs($data,$body['annul_abs_id']);
            return $responseHandler->handleJsonResponse([
                'data' =>  true
            ], 200, 'OK');
        }
        
        $insert = self::addOne($body);
        return $responseHandler->handleJsonResponse([
            'data' =>  true
        ], 200, 'OK');
    }

    public static function updateValidation(){

        $Database = new Database('devis');
        $Database->DbConnect();

        $thresholdTime = date('Y-m-d H:i:s', strtotime('-72 hours'));

        $sql = "UPDATE time_out SET to__abs_etat = 'VLD' WHERE to__abs_dt <= :thresholdTime AND to__abs_etat = 'DEM' ";

        $stmt = $Database->Pdo->prepare($sql);

        $stmt->bindParam(':thresholdTime', $thresholdTime, PDO::PARAM_STR);
        
        $stmt->execute();
    }


    public static function addOne($body){
        $Database = new Database('devis');
        $Database->DbConnect();
        $request = $Database->Pdo->prepare("INSERT INTO time_out (
            to__user, to__out, to__in, to__motif, to__info, to__abs_user, to__abs_dt, to__abs_etat
            ) VALUES (
            :to__user, :to__out, :to__in, :to__motif, :to__info, :to__abs_user, :to__abs_dt, :to__abs_etat)");
        $request->bindParam(":to__user", $body['to__user']);
        $request->bindParam(":to__out", $body['to__out']);
        $request->bindParam(":to__in", $body['to__in']);
        $request->bindParam(":to__motif", $body['to__motif']);
        $request->bindParam(":to__info", $body['to__info']);
        $request->bindParam(":to__abs_user", $body['to__abs_user']);
        $request->bindParam(":to__abs_dt", $body['to__abs_dt']);
        $request->bindParam(":to__abs_etat", $body['to__abs_etat']);
        $request->execute();
        return true;
    }
    

    static function getTimes(){
        $Database = new Database('devis');
        $Database->DbConnect();
        $request = $Database->Pdo->query("SELECT DISTINCT  t.* , u.prenom , u.nom
        FROM time_out as t
        LEFT JOIN utilisateur as u ON u.id_utilisateur  = t.to__user
        LIMIT 20000");
        $data = $request->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    static function getUserTimes($user){
        $Database = new Database('devis');
        $Database->DbConnect();
        $request = $Database->Pdo->query("SELECT DISTINCT  t.* , u.prenom , u.nom
        FROM time_out as t
        LEFT JOIN utilisateur as u ON u.id_utilisateur  = t.to__user
        WHERE t.to__user = ".$user." LIMIT 20000");
        $data = $request->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    static function refuseAbs( $data, $clause ){
        $Database = new Database('devis');
        $Database->DbConnect();
        $request = $Database->Pdo->prepare
        ('UPDATE time_out
        SET to__abs_etat = ? , to__abs_veto_user = ? , to__abs_veto_motif = ? 
        WHERE to__id = ?');
        $update->execute([$data, $clause]);
    }

    static function traiterDate($dateSQL){
        // Convertir la date SQL en objet DateTime
        $date = new DateTime($dateSQL);
        // Vérifier si l'heure est plus tardive que 9h du matin
        if ($date->format('H:i:s') < '09:00:00') {
            // Supprimer la précision heure minute seconde
            $date->setTime(0, 0, 0);
            // Retourner la nouvelle chaîne
            return $date->format('Y-m-d');
        } else {
            // Si l'heure n'est pas plus tardive que 9h du matin, retourner la date originale
            return $dateSQL;
        }
    }
}