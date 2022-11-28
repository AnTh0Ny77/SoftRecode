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
        $Api->getFiles("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE2Njk2NDgzOTYsInVpZCI6NywiZXhwIjoxNjY5NjUxOTk2fQ.-1GvqcPJGfD8OmJ6w9SMMQAl9OG8CsgDOA1IckVpjHY");
        die();
        $token = $Api->login('anthonybs.pro@gmail.com' , 'hello1H.test8');
        if ($token['code'] != 200) {
           
        }

        $query_exemple = [
            'tk__id' =>  [],
            'tk__groupe' => [] ,
            'tk__lu' => [], 
            'tk__motif' => ['TKM'] , 
            'search' => ''
        ];

        if (!empty($_GET)){
            if (!empty($_GET['search'])) {
                $query_exemple['search'] = $_GET['search'] ;
            }

            if (!empty($_GET['tk__lu'])) {
                foreach ($_GET['tk__lu']  as  $value) {
                    array_push($query_exemple['tk__lu'], $value);
                }
            }

            if (!empty($_GET['tk__id'])) {
                $tempo = explode(' ' ,$_GET['tk__id']  );
                foreach ($tempo as  $value) {
                    if (is_numeric($value) and strlen($value) ==  5 ) {
                        array_push( $query_exemple['tk__id'] , $value);
                    }
                }
            }

            if (!empty($_GET['tk__groupe'])) {
                $tempo = explode(' ' ,$_GET['tk__groupe']  );
                foreach ($tempo as  $value) {
                    if (is_numeric($value) and strlen($value) ==  4 ) {
                        array_push( $query_exemple['tk__groupe'] , $value);
                    }
                }
            }
        }
       
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
        
        //////////////////filters
        $filters = [];
        $filters['lu'] = false ;
        $filters['nonLu'] = false;
        $filters['cloture'] = false ;
        $filters['search'] = false ;
        $filters['tk__id'] = false ;
        $filters['tk__groupe'] = false ;
        foreach ($query_exemple['tk__lu'] as $value) {
           switch ( $value) {
               case  0 :
                   $filters['nonLu'] =  true ;
                   break;
                case  1 :
                    $filters['lu'] = true;
                    break;
                case  2 :
                    $filters['cloture'] = true;
                    break; 
           }
        }

        if (!empty($_GET['search']))
            $filters['search'] = $_GET['search'];

        if (!empty($query_exemple['tk__id'])) {
            $filters['tk__id'] = " ";
            foreach ($query_exemple['tk__id'] as  $value) {
                $filters['tk__id'] .= " " . $value . " ";
            }
        }

        if (!empty($query_exemple['tk__groupe'])) {
            $filters['tk__groupe'] = " ";
            foreach ($query_exemple['tk__groupe'] as  $value) {
                $filters['tk__groupe'] .= " " . $value . " ";
            }
        }
        
        return self::$twig->render(
            'display_ticket_myrecode_list.html.twig',[
                'user' => $_SESSION['user'],
                'list' => $definitive_edition , 
                'filters' => $filters
            ]
        );
    }



    public static function displayTickets(){
        self::init();
        self::security();
        $Api = new ApiTest();
        $token = $Api->login('anthonybs.pro@gmail.com' , 'hello1H.test8');

        if ($token['code'] != 200) {
           //pas de connexion à l 'api : 
        }

        if (!empty($_GET['tk__id'])) {
            $query_exemple = [
                'tk__id' => []
             ] ;
            if (is_numeric($_GET['tk__id']) and strlen($_GET['tk__id']) ==  5 ) {
                array_push( $query_exemple['tk__id'] ,$_GET['tk__id']);

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

                if (empty($definitive_edition)) {
                    header('location: myRecode');
                    exit;
                }

                $ticket = $definitive_edition[0];
                $ticket['lignes'][0]['entities'][0] = [
                    "name" => $ticket['mat']['mat__model'], 
                    "label" => $ticket['mat']['mat__pn'], 
                    "additionals" => $ticket['mat']['mat__sn']  , 
                    "alternative" => "public/img/pn2.jpg"
                ];
                $ticket['lignes'][0]['entities'][1] = [
                    "name" => $ticket['cli']['cli__nom'], 
                    "label" => $ticket['cli']['cli__adr1'], 
                    "additionals" => $ticket['cli']['cli__cp'] . ' ' . $ticket['cli']['cli__ville'] , 
                    "alternative" => "public/img/client_image.png"
                ];

                return self::$twig->render(
                    'display_ticket_myrecode.html.twig',[
                        'user' => $_SESSION['user'],
                        'ticket' => $ticket 
                    ]
                );

            }else{
                header('location: myRecode');
                exit;
            }
            
        }else{
            header('location: myRecode');
            exit;
        }

        
    }

    

}