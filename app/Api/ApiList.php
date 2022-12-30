<?php

namespace App\Api;

require_once  '././vendor/autoload.php';

use DateTime;
use App\Database;
use App\Tables\Cmd;
use App\Tables\Client;
use App\Api\ResponseHandler;
use App\Methods\Pdfunctions;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Devis_functions;
use Spipu\Html2Pdf\Exception\Html2PdfException;

class ApiList
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
        if (empty($_GET['cli__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' lID du client semble etre vide  '
            ], 404, 'bad request');
        }

        $Database = new Database('devis');
        $Database->DbConnect();
        $Client = new Client($Database);
        $Cmd= new Cmd($Database);
        $client = $Client->getOne($_GET['cli__id']);

        if (empty($client)) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' aucun cloient n a été trouvé '
            ], 404, 'bad request');
        }

        $list = $Cmd->get_by_client_id($_GET['cli__id'] , 10000);
        if (empty($list)) {
            return $responseHandler->handleJsonResponse([
                'data' =>  []
            ], 200, 'OK mais pas de commandes');
        }

        $response = [
            'ATN' => [], 
            'CMD' => [] , 
            'IMP' => [] , 
            'VLD' => [], 
            'ABN' => [] , 
            'VLA' => []
        ];

        foreach ($list as  $value) {
            switch ($value->cmd__etat) {
                case 'ABN':
                    $temp = [
                        "cmd__id" => $value->cmd__id , 
                        "cmd__date_cmd" =>  $value->cmd__date_cmd,
                        "cmd__date_envoi" =>  $value->cmd__date_envoi,
                        "cmd__date_fact" =>  $value->cmd__date_fact,
                        "cmd__code_cmd_client" =>  $value->cmd__code_cmd_client,
                        "cmd__nom_devis" =>  $value->cmd__nom_devis,
                        "cmd__id_facture" =>  $value->cmd__id_facture
                    ];
                    array_push($response['ABN'], $temp);
                    break;
                case 'CMD':
                    $temp = [
                        "cmd__id" => $value->cmd__id , 
                        "cmd__date_cmd" =>  $value->cmd__date_cmd,
                        "cmd__date_envoi" =>  $value->cmd__date_envoi,
                        "cmd__date_fact" =>  $value->cmd__date_fact,
                        "cmd__code_cmd_client" =>  $value->cmd__code_cmd_client,
                        "cmd__nom_devis" =>  $value->cmd__nom_devis,
                        "cmd__id_facture" =>  $value->cmd__id_facture
                    ];
                    array_push($response['CMD'],  $temp);
                    break;
                case 'VLA':
                    $temp = [
                        "cmd__id" => $value->cmd__id , 
                        "cmd__date_cmd" =>  $value->cmd__date_cmd,
                        "cmd__date_envoi" =>  $value->cmd__date_envoi,
                        "cmd__date_fact" =>  $value->cmd__date_fact,
                        "cmd__code_cmd_client" =>  $value->cmd__code_cmd_client,
                        "cmd__nom_devis" =>  $value->cmd__nom_devis,
                        "cmd__id_facture" =>  $value->cmd__id_facture
                    ];
                    array_push($response['VLA'], $temp);
                    break;
                case 'VLD':
                    $temp = [
                        "cmd__id" => $value->cmd__id , 
                        "cmd__date_cmd" =>  $value->cmd__date_cmd,
                        "cmd__date_envoi" =>  $value->cmd__date_envoi,
                        "cmd__date_fact" =>  $value->cmd__date_fact,
                        "cmd__code_cmd_client" =>  $value->cmd__code_cmd_client,
                        "cmd__nom_devis" =>  $value->cmd__nom_devis,
                        "cmd__id_facture" =>  $value->cmd__id_facture
                    ];
                    array_push($response['VLD'],  $temp);
                    break;
                case 'IMP':
                    $temp = [
                        "cmd__id" => $value->cmd__id , 
                        "cmd__date_cmd" =>  $value->cmd__date_cmd,
                        "cmd__date_envoi" =>  $value->cmd__date_envoi,
                        "cmd__date_fact" =>  $value->cmd__date_fact,
                        "cmd__code_cmd_client" =>  $value->cmd__code_cmd_client,
                        "cmd__nom_devis" =>  $value->cmd__nom_devis,
                        "cmd__id_facture" =>  $value->cmd__id_facture
                    ];
                    array_push($response['IMP'] ,  $temp );
                    break;
            }
        }
        
        return $responseHandler->handleJsonResponse([
            'data' =>  $response
        ], 200, 'ok');
    }

}
