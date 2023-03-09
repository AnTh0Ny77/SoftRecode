<?php

namespace App\Controller;

require_once  '././vendor/autoload.php';

use App\Controller\BasicController;
use App\Api\ResponseHandler;
use DateTime;
use App\Database;
use App\ApiService\ApiTest;

class MachineController  extends BasicController {

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

    public static function forms(){
        self::init();
        self::security();
        if (empty($_GET['tk__id'])) {
            header('location: myRecode');
            exit;
        }
        $Api = new ApiTest();
        //API MYRECODE CONNECTION/////////
        if (empty($_SESSION['user']->refresh_token)) {$token = $Api->login($_SESSION['user']->email, 'test'); if ($token['code'] != 200) {echo 'Connexion LOGIN à L API IMPOSSIBLE';die();}$_SESSION['user']->refresh_token = $token['data']['refresh_token'];$token =  $token['data']['token'];} else {$refresh = $Api->refresh($_SESSION['user']->refresh_token);if ($refresh['code'] != 200) {echo 'Rafraichissemnt de jeton API IMPOSSIBLE';die();}$token =  $refresh['token']['token'];}
        /////////////////////////////////
        $query_exemple = [
            'tk__id' => [],
            'RECODE__PASS' => "secret"
        ];
       
        array_push($query_exemple['tk__id'], $_GET['tk__id']);
        $list = $Api->getTicketList($token, $query_exemple);
        $list = $list['data'];
        $definitive_edition = [];
        foreach ($list as $ticket) {
            $mat_request = $Api->getMateriel($token, ['mat__id[]' =>  $ticket['tk__motif_id'], 'RECODE__PASS' => 'secret']);
            if ($mat_request['code'] == 200) {
                $ticket['mat'] =  $mat_request['data'][0];
                $ticket['cli'] =  $Api->getClient($token, ['cli__id' => $ticket['mat']['mat__cli__id']])['data'];
            }
            array_push($definitive_edition, $ticket);
        }
        $ticket = $definitive_edition[0];
        switch ($ticket['mat']['mat__kw_tg']) {
            case 'AUT':
                $gar = 'Autre';
                break;
            case 'GCO':
                $gar = 'Garantie constructeur';
                break;
            case 'GNO':
                $gar = 'NON garantie';
                break;
            case 'GRE':
                $gar = 'Garantie RECODE';
                break;
            case 'LOC':
                $gar = 'Location RECODE';
                break;
            case 'MNT':
                $gar = 'Maintenance RECODE';
                break;
            default:
                $gar = 'NON garantie';
                break;
        }
        $date_sortie = '';
        if (!empty($ticket['mat']['mat__sn'])) {
            // $date_sortie = $totoro_request->get_sortie_sn($ticket['mat']['mat__sn']);

            if (!empty($date_sortie)) {
                if (!empty($date_sortie['sortie'])) {
                    $date_sortie = new DateTime($date_sortie['sortie']);
                    $date_sortie = $date_sortie->format('d/m/Y');
                    $date_sortie =  'Dernière sortie le ' . $date_sortie;
                } else {
                    $date_sortie = '';
                }
            }
        }
        $ticket['entitie']=[
            "gar" => $gar,
            'dateof' => $ticket['mat']['mat__date_offg'],
            "name" => $ticket['mat']['mat__model'],
            "label" => $ticket['mat']['mat__pn'],
            "sortie" => $date_sortie,
            'bl' => $ticket['mat']['mat__idnec'],
            'dt_off' => date("d/m/Y", strtotime($ticket['mat']['mat__date_offg'])),
            "additionals" => $ticket['mat']['mat__sn'],
            "alternative" => "public/img/pn2.jpg",
        ];
       
        return self::$twig->render(
            'display_ticket_machine.html.twig',
            [
                'user' => $_SESSION['user'],
                'ticket' => $ticket
            ]
        );

    }


}