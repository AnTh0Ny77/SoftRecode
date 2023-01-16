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
        $Client = new Client(self::$Db);
        $clientList = $Client->get_client_devis();
        
        return self::$twig->render(
            'verify_user_myrecode.html.twig',[
                'user' => $_SESSION['user'],
                'clientList' => $clientList
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