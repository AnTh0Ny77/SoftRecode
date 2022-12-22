<?php

namespace App\Api;

require_once  '././vendor/autoload.php';

use App\Api\ResponseHandler;

class ApiListDocTickets
{

    public static  function index($method)
    {
        $responseHandler = new ResponseHandler;
        switch ($method) {
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

    public static function get()
    {
        $responseHandler = new ResponseHandler;
        //controle du client 
        if (empty($_GET['tkl__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' lID de la ligne  semble etre vide  '
            ], 404, 'bad request');
        }

        if (!is_dir('public/img/tickets/' . $_GET['tkl__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' la ligne ne comporte pas documents'
            ], 404, 'bad request');
        }

        $scanned_directory = array_diff(scandir('public/img/tickets/' . $_GET['tkl__id']), array('..', '.'));

        return $responseHandler->handleJsonResponse([
            'data' =>  $scanned_directory
        ], 200, 'OK !');



    }
}
