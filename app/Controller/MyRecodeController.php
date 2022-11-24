<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Keyword;
use DateTime;
use App\Tables\Tickets;
use App\Apiservice\ApiTest;

class MyRecodeController extends BasicController {


    public static function displayList(){
        self::init();
        self::security();
        $Api = new ApiTest();
        $token = $Api->login('anthonybs.pro@gmail.com' , 'hello1H.test8');
        if ($token['code'] != 200) {
           
        }

        $tk__lu = [ 0 ,1 , 2 ];
        $tk__motif = ['TKM'];
        $search = null;

        if (!empty($_GET)){
            if (!empty($_GET['searchTickets'])) {
                $search = $_GET['searchTickets'] ;
            }
        }
       
        $query_exemple = [
        'tk__id' =>  [ 10044 , 10045 ]
        ];
    
        $token = $token['data']['token'];
        $list = $Api->getTicketList($token , $query_exemple);
        $list = $list['data'];
        $definitive_edition = [];
        foreach ($list as $ticket){
            $ticket['user'] = reset($ticket['lignes']);
            $ticket['user'] = $ticket['user']['tkl__user_id'];
            $ticket['dest'] = end($ticket['lignes']);
            $ticket['last'] =  $ticket['dest']['tkl__user_id'];
            $ticket['dest'] =  $ticket['dest']['tkl__user_id_dest'];
            $ticket['info'] = end($ticket['lignes']);
            $ticket['memo']  =  $ticket['info']['tkl__memo'];
            $mat_request = $Api->getMateriel($token, ['mat__id[]' =>  $ticket['tk__motif_id']]);

            if ($mat_request['code'] == 200) {
                $ticket['mat'] =  $mat_request['data'][0];
                $ticket['cli'] =  $Api->getClient($token, ['cli__id' => $ticket['mat']['mat__cli__id']])['data'];
            }
           
            $date_time = new DateTime($ticket['info']['tkl__dt']);
			$ticket['date'] = $date_time->format('d/m/Y H:i');
            array_push($definitive_edition , $ticket );
        }  

        return self::$twig->render(
            'display_ticket_myrecode_list.html.twig',[
                'user' => $_SESSION['user'],
                'list' => $definitive_edition
            ]
        );
    }

    

}