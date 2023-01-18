<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Keyword;
use App\Database;
use App\Tables\Client;
use App\Tables\User;
use DateTime;
use App\Tables\Tickets;
use App\Apiservice\ApiTest;


class UserMyRecodeController extends BasicController {

    //verifie la relation entre un utilisateur et une société selectionnée : path : /myRecodeverifyUser
    public static function VerifyUser(){
        self::init();
        self::security();
        $Api = new ApiTest();
        $alert = false ;
        $value = false ;
        //partie gestion du token /////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////
        if (empty($_SESSION['user']->refresh_token)) {
            $token = $Api->login($_SESSION['user']->email, 'test');
            if ($token['code'] != 200) {echo 'Connexion LOGIN à L API IMPOSSIBLE';die();}
            $_SESSION['user']->refresh_token = $token['data']['refresh_token'];
            $token =  $token['data']['token'];
        } else {
            $refresh = $Api->refresh($_SESSION['user']->refresh_token);
            if ($refresh['code'] != 200) { echo 'Rafraichissemnt de jeton API IMPOSSIBLE';die();}
            $token =  $refresh['token']['token'];
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $Client = new Client(self::$Db);
        //si une vérification à été envoyé: 
        if (!empty($_GET['user__mail'])) {
            if (filter_var($_GET['user__mail'], FILTER_VALIDATE_EMAIL)) {
                $response = $Api->createGet('/verifUser', $token, ['user__mail' => $_GET['user__mail']]);
                if (empty($response['data'])) {
                    var_dump($response);
                    die();
                }else{
                    $_SESSION['userMyRecode'] = $response['data'];
                    header('location updateUserMyRecode');
                    die();
                }
             
            }else {
                $alert = 'E-mail est invalide';
                $value = $_GET['user__mail'];
            }
        }

        return self::$twig->render(
            'verify_user_myrecode.html.twig',[
                'user' => $_SESSION['user'], 
                'alert' => $alert  , 
                'value' => $value
            ]
        );
    }

    public static function updateUserMyrecode(){
        self::init();
        self::security();
        $Api = new ApiTest();
        $alert = false;
        $value = false;
        //partie gestion du token /////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////
        if (empty($_SESSION['user']->refresh_token)) {
            $token = $Api->login($_SESSION['user']->email, 'test');
            if ($token['code'] != 200) {
                echo 'Connexion LOGIN à L API IMPOSSIBLE';
                die();
            }
            $_SESSION['user']->refresh_token = $token['data']['refresh_token'];
            $token =  $token['data']['token'];
        } else {
            $refresh = $Api->refresh($_SESSION['user']->refresh_token);
            if ($refresh['code'] != 200) {
                echo 'Rafraichissemnt de jeton API IMPOSSIBLE';
                die();
            }
            $token =  $refresh['token']['token'];
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $Client = new Client(self::$Db);
        //si une vérification à été envoyé: 
        if (empty($_SESSION['userMyRecode'])) {
           
        }

        return self::$twig->render(
            'update_user_my_recode.html.twig',
            [
                'user' => $_SESSION['user'],
                'alert' => $alert
            ]
        );
    }

    //verifie la relation entre un utilisateur et une société selectionnée : path : /myRecodeUserRelation
    public static function postUserAndRelation(){
        self::init();
        self::security();
        $Api = new ApiTest();

        return self::$twig->render(
            'user_myrecode.html.twig',[
                'user' => $_SESSION['user'],
               
            ]
        );
    }

    //creer une nouvelle relation: path : /myrecoderelation
    public static function PostNewRelation(){
        self::init();
        self::security();
        $Api = new ApiTest();

        return self::$twig->render(
            'user_myrecode.html.twig',[
                'user' => $_SESSION['user'],
               
            ]
        );
    }


}