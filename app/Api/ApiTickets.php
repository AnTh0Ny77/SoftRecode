<?php

namespace App\Api;

require_once  '././vendor/autoload.php';
use App\Api\ResponseHandler;

class ApiTickets {


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
        $responseHandler = new ResponseHandler;
        //controle du client 
        if (empty($_POST['file'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Le fichier est vide'
            ], 404, 'bad request');
        }
        if (empty($_POST['tkl__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' La ligne de ticket n est pas précisée'
            ], 404, 'bad request');
        }

        $fileName = $_FILES['file']['name'];
        $tempPath = $_FILES['file']['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $pathToFile = 'public/img/tickets/' . $_POST['tkl__id'];
        $uniquename = $fileName . '.' . $fileExtension;
        if (!is_dir($pathToFile)) {
            mkdir($pathToFile, 7777);
        }

        if (!move_uploaded_file($tempPath, $pathToFile . '/' . $uniquename)) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  'Un problème est survenu dans la sauvergarde du fichier'
            ], 500, 'Internal server error');
        }

        return $responseHandler->handleJsonResponse([
            'data' =>  'OK !'
        ], 201, 'ressource created');

    }
   
}
