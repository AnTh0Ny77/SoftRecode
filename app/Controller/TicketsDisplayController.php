<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Article;
use App\Tables\Keyword;
use App\Tables\General;
use App\Tables\UserGroup;
use App\Tables\Stock;
use App\Debug;
use App\Tables\User;
use ReflectionClass;
use App\Tables\Tickets;

class TicketsDisplayController extends BasicController
{
    public static function handle_groups(array $list) :array{
        $array_tickets = [];
        foreach ($list as $key => $value){
            $array_groups = [];
            if (!empty($value->tk__groupe)){
                foreach ($list as $index => $ticket) {
                    if ($ticket->tk__groupe === $value->tk__groupe and  $ticket->tk__id != $value->tk__id ){
                        array_push($array_groups  , $ticket);
                    }
                }
                $ticket->groups = $array_groups;
                $temp = $ticket;
                array_push($array_tickets , $temp);
                unset($list[$index]);
             }else array_push($array_tickets ,$value); 
        }
        return $array_tickets;
    }

    public static function handle_search(){
            $cloture   = false;
            $lus  = false;
            $nonLus  = false;

            if (!empty($_GET['StateFilter']) && isset($_SESSION['filters'])){
                foreach ($_GET['StateFilter'] as $key => $value) {
                   
                    if ($value == 'Cloture') {
                         $_SESSION['filters']['Cloture'] = 1;
                        $cloture = true;
                    }
                    if ($value == 'Lus'){
                        $_SESSION['filters']['Lus'] = 1;
                        $lus = true;
                    }
                    if ($value == 'NonLus'){
                        $_SESSION['filters']['NonLus'] = 1;
                        $nonLus = true;
                    }  
                }
            }

            if ($cloture == false ) 
                $_SESSION['filters']['Cloture'] = 0;

            if ($lus == false)
                $_SESSION['filters']['Lus'] = 0;

            if ($nonLus == false)
                $_SESSION['filters']['NonLus'] = 0;
            
            
            if (!empty($_GET['AuthorFilter']) && isset($_SESSION['filters']))
                $_SESSION['filters']['Author'] = $_GET['AuthorFilter'];

            if (!isset($_SESSION['filters']) && empty($_GET)){
                $_SESSION['filters'] = [
                    'Cloture' =>  0 ,
                    'Lus' => 1,
                    'NonLus' => 1,
                    'Author' =>  1,
                    'Type' => 'DP'
                ];
            }
    }
   
    //@route: /tickets-display-list
    public static function displayTicketList(){
        self::init();
        self::security();
        $Ticket = new Tickets(self::$Db);
        $General = new General(self::$Db);
        $alert_results = false;
        $text_results = false; 
        $list = null;
        if (!empty($_GET['searchTickets'])) {
            //clean the search :
            $_GET['searchTickets'] =  $Ticket->clean($_GET['searchTickets']);
            $text_results = $_GET['searchTickets'];
            //handle get variable :
            $_GET['StateFilter'] =  ['Cloture' , 'Lus' , 'NonLus'];
            $_GET['AuthorFilter'] = 1 ;
        }
        if (empty($_GET['searchTickets']))
            $_GET['searchTickets'] = '';
        
           
        self::handle_search();
        $results = $Ticket->search_tickets_filters($_SESSION['filters'], $_GET['searchTickets'] , $_SESSION['user']->id_utilisateur);
       
        if (!empty($results[0]))
            $list = $results[0];
        
        if (empty($list))
            $alert_results = true;
        
        if (!empty($results[1])) 
            $_SESSION['filters'] = $results[1];
        
       
        $filters = $_SESSION['filters'];
        //si un ticket à été cloturé : 
        if (!empty($_POST['ticketsCloture'])){
            $Ticket->cloture_ticket($_POST['ticketsCloture'], $_SESSION['user']->id_utilisateur , date('Y-m-d H:i:s') , $_POST['commentaire']);
        }
        $config = file_get_contents('configDisplay.json');
        $config = json_decode($config);
        $config = $config->entities;
        if (!empty($_GET['nonLu'])){
            $General->updateAll('ticket', 0 , 'tk__lu', 'tk__id', $_GET['nonLu']);
        }
        $_SESSION['cloture'] = 0;
        // if (!isset($_SESSION['cloture'])) {
        //     $_SESSION['cloture'] = 0;
        // }
        // if (!empty($_GET['cloture'])) {
        //     $_SESSION['cloture'] = 1 ;
        // }
        // if (isset($_SESSION['cloture']) && empty($_GET['cloture'])) {
        //     $_SESSION['cloture'] = 0;
        // }
        // if (!empty($_GET['searchTickets'])){
        //     $text_results = $_GET['searchTickets'];
        //     $_GET['searchTickets'] =  $Ticket->clean($_GET['searchTickets']);
        //     $_GET['searchTickets'] = trim($_GET['searchTickets']);
        //     $list = $Ticket->search_ticket($_GET['searchTickets'] , $config , $_SESSION['cloture']);
        //     if (empty($list))
        //         $alert_results = true;
        // }elseif(!empty($_GET['id_user'])){
        //         $list = $Ticket->search_user_tickets($_GET['id_user'], $_GET['tk__lu'] , $_SESSION['cloture']);
        // }
        // else $list = $Ticket->get_last(1);
        if (!empty($list)){
            $temp_list = $list;
            foreach ($list as $key => $ticket){
                $ticket->demandeur = $Ticket->return_demandeur($ticket->tk__id);
                // foreach ($config as  $entitie){
                //     if (!empty($ticket->sujet)){
                //         $subject_identifier = $ticket->sujet->tksc__option;
                //         $display_entitie = $Ticket->createEntities($entitie, $subject_identifier, $ticket->tk__motif_id);
                //         if (!empty($display_entitie)) {
                //             $ticket->sujet = $display_entitie;
                //         }
                //     }
                // }
                if(!empty($ticket->sujet)  && !is_array($ticket->sujet)) unset($ticket->sujet);
                //groupe les ticket 
                if (!empty($ticket->tk__groupe)){
                    $array_groups = []; 
                    foreach ( $temp_list as $index => $other_tickets){
                            if ($other_tickets->tk__groupe === $ticket->tk__groupe and  $ticket->tk__id != $other_tickets->tk__id ){
                                $temp = $other_tickets ;
                                array_push($array_groups  , $temp);
                                unset($temp_list[$key]);
                                unset($list[$index]);
                            }
                    }
                    $ticket->groups = $array_groups;
                }
                $ticket->client = $Ticket->get_dp_client($ticket->tk__id);
               
            }
        }
        return self::$twig->render(
            'display_ticket_list.html.twig',
            [
                'user' => $_SESSION['user'], 
                'list' => $list , 
                'filters' => $filters ,
                'alert_results' => $alert_results , 
                'cloture' => $_SESSION['cloture'],
                'text_results' => $text_results
            ]
        );
    }

    //@route: /tickets-display
    public static function displayTicket($Request){
        self::init();
        self::security();
        $Ticket = new Tickets(self::$Db);
        $General = new General(self::$Db);
        $sujet = null;
        $user_destinataire = null;
        $next_action = null;
        if (!empty($_POST['id']))
            $Request['id'] = $_POST['id'] ;
        if(empty($Request['id'])){
            header('location: tickets-display-list');
            die();
        }
        $ticket = $Ticket->findOne($Request['id']);
        if (empty($ticket)){
            header('location: tickets-display-list');
            die();
        }
        if ($ticket->tk__lu != 2){
            $last_line = $Ticket->get_last_line($Request['id']);
            if ($last_line->id_dest == $_SESSION['user']->id_utilisateur) {
                $General->updateAll('ticket', 1, 'tk__lu', 'tk__id', $Request['id']);
            }elseif ($last_line->id_dest == 1011){
                $groups = new UserGroup(self::$Db);
                $array_user = $groups->get_user_by_groups(1011);
                if (!empty($array_user)){
                    foreach ($array_user as $user) {
                        if ($user->id_utilisateur == $_SESSION['user']->id_utilisateur) {
                            $General->updateAll('ticket', 1, 'tk__lu', 'tk__id', $Request['id']);
                        }
                    }
                }
            }elseif ($last_line->id_dest == 1012) {
                $groups = new UserGroup(self::$Db);
                $array_user = $groups->get_user_by_groups(1012);
                if (!empty($array_user)) {
                    foreach ($array_user as $user) {
                        if ($user->id_utilisateur == $_SESSION['user']->id_utilisateur) {
                            $General->updateAll('ticket', 1, 'tk__lu', 'tk__id', $Request['id']);
                        }
                    }
                }
            }
        }
        $ticket = $Ticket->findOne($Request['id']);
        $config = file_get_contents('configDisplay.json');
        $config = json_decode($config);
        $config = $config->entities;
        foreach ($ticket->lignes as $ligne){
            $entitites_array = [];
            $pattern = "@";
            $other_fields = [];
            foreach ($ligne->fields as $key => $field){   
                    if (stripos($field->tklc__memo , $pattern)){
                        foreach($config as  $entitie) {
                           $secondary_entities = $Ticket->create_secondary_entities($entitie , $field->tklc__memo);
                           if(!empty($secondary_entities)){
                               array_push($entitites_array, $secondary_entities );
                           }
                        }
                    }else{
                        array_push($other_fields,$field);
                    } 
                    $ligne->fields =  $other_fields;
            }
            if (!empty($entitites_array)) {
                $ligne->entities = $entitites_array;
            }
            $files = $Ticket->getFiles($ligne->tkl__id);
            if (!empty($files)) {
             $ligne->path = 'upload/'.$ligne->tkl__id.'/';
             $ligne->files = $files;
            }
        }
        $user_destinataire = $Ticket->getCurrentUser($Request['id']);
        $next_action = $Ticket->getNextAction($Request['id']);
        foreach ($config as  $entitie){
           if (!empty($ticket->sujet)){
                $subject_identifier = $ticket->sujet->tksc__option;
                $display_entitie = $Ticket->createEntities($entitie , $subject_identifier, $ticket->tk__motif_id );
                if (!empty($display_entitie)) {
                    $sujet = $display_entitie;
                }
           }
        }
        return self::$twig->render(
            'display_ticket.html.twig',
            [
                'user' => $_SESSION['user'],
                'destinataire' => $user_destinataire, 
                'sujet' =>  $sujet ,
                'ticket' => $ticket , 
                'next_action'=> $next_action
            ]
        );
    }
}