<?php

namespace App\Api;

require_once  '././vendor/autoload.php';
use DateTime;
use App\Database;
use App\Tables\Cmd;
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

        $test = self::checkBody($body);
        if ($test != false ) {
            return $responseHandler->handleJsonResponse([
                'msg' => $test
            ], 401, 'bad request');
        }

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

        return false;
        
    }

}