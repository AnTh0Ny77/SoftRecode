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
        $query_exemple = [
            'tk__lu[]' => 0 , 
            'tk__motif[]' => 'TKM'
        ];
        $token = $token['data']['token'];
        $list = $Api->getTicketList($token , $query_exemple);
        $list = $list['data'];
        $definitive_edition = [];
        foreach ($list as $ticket){
            $ticket['user'] = reset($ticket['lignes']);
            $ticket['user'] = $ticket['user']['tkl__user_id'];
            $ticket['dest'] = end($ticket['lignes']);
            $ticket['dest'] =  $ticket['dest']['tkl__user_id_dest'];
            $ticket['info'] = end($ticket['lignes']);
            $ticket['memo']  =  $ticket['info']['tkl__memo'];
            $ticket['mat'] = $Api->getMateriel($token , ['mat__id[]' =>  $ticket['tk__motif_id']]);
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