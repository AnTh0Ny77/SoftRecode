<?php

namespace App\Controller;

require_once  '././vendor/autoload.php';

use App\Controller\BasicController;
use App\Api\ResponseHandler;
use App\Tables\Keyword;
use App\Tables\User;
use DateTime;
use App\Tables\Tickets;
use App\Apiservice\ApiTest;
use App\Tables\Article;
use App\Database;
use App\Tables\Cmd;
use App\Tables\Abonnement;


class MatMyRecodeController extends BasicController {

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
        self::init();
        self::security();
        $Database = new Database('devis');
        $Database->DbConnect();
        $Cmd = new Cmd($Database);
        $Api = new ApiTest();
        $Abonnement = new Abonnement($Database);
        $token = self::getToken();

        if (!empty($_POST['abn__id'])){

            $abn = $Abonnement->getById($_POST['abn__id']);
            $lignes = $Abonnement->getLigne($abn->ab__cmd__id);
            $myRecodeClient = $Api->getClient($token , ['cli__id' =>  $abn->ab__client__id_fact]);
            if (empty($myRecodeClient['data'])) {
                # code...
            }

            foreach ($lignes as $key => $value) {
               //incorporre les machines 
                //boucle chaque machine : sn ? oui maj sinon post
            }
            

        }else{
            return false ;
        }
        
    }

    public static function getToken(){
        $token  = null;
        if (empty($_SESSION['user']->refresh_token)) {
            $token = $Api->login($_SESSION['user']->email , 'test');
            if ($token['code'] != 200) {
                echo 'Connexion LOGIN à L API IMPOSSIBLE';
                die();
            }
            $_SESSION['user']->refresh_token = $token['data']['refresh_token'] ; 
            $token =  $token['data']['token'];
        }else{
            $refresh = $Api->refresh($_SESSION['user']->refresh_token);
            if ( $refresh['code'] != 200) {
                echo 'Rafraichissemnt de jeton API IMPOSSIBLE';
                die();
            }
            $token =  $refresh['token']['token'];
        }
        return $token ;
    }
}