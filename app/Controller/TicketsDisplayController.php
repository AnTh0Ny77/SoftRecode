<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Article;
use App\Tables\Keyword;
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
        $list = $Ticket->get_last();
        
        return self::$twig->render(
            'display_ticket_list.html.twig',
            [
                'user' => $_SESSION['user'], 
                'list' => $list
            ]
        );
    }


    //@route: /tickets-display
    public static function displayTicket($Request){
        self::init();
        self::security();
        $Ticket = new Tickets(self::$Db);
        $sujet = null;
        $user_destinataire = null;
        if(empty($Request['id'])){
            header('location: tickets-display-list');
            die();
        }

        $ticket = $Ticket->findOne($Request['id']);
        $user_destinataire = $Ticket->getCurrentUser($Request['id']);
        
        if (empty($ticket)){
            header('location: tickets-display-list');
            die();
        }
        
        $config = file_get_contents('configDisplay.json');
        $config = json_decode($config);
        $config = $config->entities;
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
                
            ]
        );
    }
}