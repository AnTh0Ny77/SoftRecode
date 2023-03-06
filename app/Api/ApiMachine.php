<?php

namespace App\Api;

require_once  '././vendor/autoload.php';

use App\Api\ResponseHandler;
use DateTime;
use App\Database;
use App\ApiService\ApiTest;

class ApiMachine{

    public static  function index($method){
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
        $Api = new ApiTest();
        //API MYRECODE CONNECTION/////////
        if (empty($_SESSION['user']->refresh_token)) { $token = $Api->login($_SESSION['user']->email , 'test');if ($token['code'] != 200) {echo 'Connexion LOGIN à L API IMPOSSIBLE';die();}$_SESSION['user']->refresh_token = $token['data']['refresh_token'] ; $token =  $token['data']['token'];}else{$refresh = $Api->refresh($_SESSION['user']->refresh_token);if ( $refresh['code'] != 200) {echo 'Rafraichissemnt de jeton API IMPOSSIBLE';die();}$token =  $refresh['token']['token'];}
        //////////INIT VARIABLE///////////
        $Database = new Database('devis');
        $responseHandler = new ResponseHandler;
        $Database->DbConnect();
        $Abonnement = new App\Tables\Abonnement($Database);
        $machine = [];
        //post une machine dans sossuke///
        $new_machine_sossuke = $Abonnement->insertMachine($machine);
        //post une machine dans MyRecode//
        $new_machine_myrecode = $Api->postMachine($token,$machine);
        //desactive dans les 2 systèmes///
        //reactive dans les 2 systèmes////
    }

}