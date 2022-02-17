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
        $list = $Ticket->get_last();

        if(empty($Request['id']))
           header('location: tickets-display-list');

        

        
        return self::$twig->render(
            'display_ticket.html.twig',
            [
                'user' => $_SESSION['user'], 
                'list' => $list
            ]
        );
    }
}