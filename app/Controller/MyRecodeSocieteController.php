<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Keyword;
use App\Tables\User;
use DateTime;
use App\Tables\Tickets;
use App\Apiservice\ApiTest;


class MyRecodeSocieteController extends BasicController {
    public static function displayList(){
        self::init();
        self::security();
        $Api = new ApiTest();
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

        $list = $Api->PostListClient($token,false)['data'];
       
        return self::$twig->render(
            'display_societe_myrecode_list.html.twig',[
                'user' => $_SESSION['user'] , 
                'list' => $list
            ]
        );
    }
    
}