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
            case 'GET':
                return self::get();
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
    
        if (empty($_FILES['file'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Le fichier est vide'
            ], 404, 'bad request');
        }
        if (empty($_POST['tkl__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' La ligne de ticket n est pas précisée'
            ], 404, 'bad request');
        }

        if (empty($_POST['nom'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Le nom du fichier n est pas précisée'
            ], 404, 'bad request');
        }

        $fileName = $_POST['nom'];
        $tempPath = $_FILES['file']['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $pathToFile = 'public/img/tickets/' .$_POST['tkl__id'];

        if (!is_dir($pathToFile)){
            mkdir($pathToFile, 7777);
        }

        if (!move_uploaded_file($tempPath, $pathToFile . '/' . $fileName)) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  'Un problème est survenu dans la sauvergarde du fichier'
            ], 500, 'Internal server error');
        }

        return $responseHandler->handleJsonResponse([
            'data' =>  'OK !'
        ], 201, 'ressource created');
    }



    public static function get(){
        $responseHandler = new ResponseHandler;
        //controle du client 
        if (empty($_GET['tkl__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' lID de la ligne  semble etre vide  '
            ], 404, 'bad request');
        }
        //renvoi la list des documents si le répertoire est trouvé :
        if (!empty($_GET['list'])) {
            $scanned_directory = array_diff(scandir('public/img/tickets/' . $_GET['tkl__id']), array('..', '.'));
            if (!empty($scanned_directory)) {
                return $responseHandler->handleJsonResponse([
                    'data' => $scanned_directory
                ], 200, 'bad request');
            }
            return $responseHandler->handleJsonResponse([
                'msg' => 'aucun répertoire trouvé'
            ], 404, 'Not found');     
        }

        if (empty($_GET['name'])){
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' le nom du fichier semble etre vide'
            ], 404, 'bad request');
        }

        if (!file_exists('public/img/tickets/'. $_GET['tkl__id'] .'/' . $_GET['name'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' le fichier demandé n existe pas'
            ], 404, 'bad request');
        }



        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        header('Content-Type: ' . finfo_file($finfo, 'public/img/tickets/' . $_GET['tkl__id'] . '/' . $_GET['name']));
        finfo_close($finfo);
        header('Content-Disposition: attachment; filename=' . basename('public/img/tickets/' . $_GET['tkl__id'] . '/' . $_GET['name']));
        header('Content-Length: ' . filesize('public/img/tickets/' . $_GET['tkl__id'] . '/' . $_GET['name']));
        ob_clean();
        flush();
        readfile('public/img/tickets/' . $_GET['tkl__id'] . '/' . $_GET['name']);
        exit;

    }
   
}
