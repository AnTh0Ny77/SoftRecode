<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Article;
use App\Tables\Keyword;
use App\Tables\General;
use App\Tables\Stock;
use App\Tables\User;
use App\Tables\Tickets;

class TicketsDisplayController extends BasicController
{
   
   
    //@route: /tickets-display-list
    public static function displayTicketList(){
        self::init();
        self::security();
        $Ticket = new Tickets(self::$Db);
        $alert_results = false ;
        $text_results = false ; 
       
        //si un ticket à été cloturé : 
        if (!empty($_POST['ticketsCloture'])){
            $Ticket->cloture_ticket($_POST['ticketsCloture'], $_SESSION['user']->id_utilisateur , date('Y-m-d H:i:s') , $_POST['commentaire']);
        }
       
        $config = file_get_contents('configDisplay.json');
        $config = json_decode($config);
        $config = $config->entities;

        if (!empty($_GET['searchTickets'])){

            $text_results = $_GET['searchTickets'];
            $list = $Ticket->search_ticket($_GET['searchTickets'] , $config);
        
            if (empty($list)){
                $alert_results = true;
            }
           
        }else $list = $Ticket->get_last();

        if (!empty($list)) {
            foreach ($list as $ticket) {
                foreach ($config as  $entitie) {
                    if (!empty($ticket->sujet)) {
                        $subject_identifier = $ticket->sujet->tksc__option;
                        $display_entitie = $Ticket->createEntities($entitie, $subject_identifier, $ticket->tk__motif_id);
                        if (!empty($display_entitie)) {
                            $ticket->sujet = $display_entitie;
                        }
                    }
                }
                if ( !empty($ticket->sujet)  && !is_array($ticket->sujet)) unset($ticket->sujet);
            }
        }
        
        return self::$twig->render(
            'display_ticket_list.html.twig',
            [
                'user' => $_SESSION['user'], 
                'list' => $list , 
                'alert_results' => $alert_results , 
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

        if (!empty($_POST['id'])) {
            $Request['id'] = $_POST['id'] ;
        }
       

        if(empty($Request['id'])){
            header('location: tickets-display-list');
            die();
        }

        $ticket = $Ticket->findOne($Request['id']);
        if (empty($ticket)) {
            header('location: tickets-display-list');
            die();
        }

        
        $config = file_get_contents('configDisplay.json');
        $config = json_decode($config);
        $config = $config->entities;
        foreach ($ticket->lignes as $ligne) {
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
        }
        if ($ticket->tk__lu != 2) {
            $General->updateAll('ticket', 1 , 'tk__lu', 'tk__id', $Request['id']);
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