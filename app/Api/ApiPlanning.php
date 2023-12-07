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
        $list_time = self::getTimes();
        return $responseHandler->handleJsonResponse([
            'data' =>  $list_time 
        ], 200, 'OK');
    }

    public static function post(){
        $responseHandler = new ResponseHandler;
        $body = json_decode(file_get_contents('php://input'), true);
        
        $insert = self::addOne($body);
       
        return $responseHandler->handleJsonResponse([
            'data' =>  true
        ], 200, 'OK');

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