<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Article;
use App\Tables\Keyword;
use App\Tables\Stock;
use App\Tables\User;
use App\Tables\Tickets;

class TicketsFormsController extends BasicController
{

    public static function forms(): string
    {
        self::init();
        self::security();

        $Article = new Article(self::$Db);
        $famille_list = $Article->getFAMILLE();

        return self::$twig->render(
            'forms_tickets.twig',
            [
                'user' => $_SESSION['user'],
                'famille_list' => $famille_list
            ]
        );
    }

    public static function formsLigne(): string
    {
        self::init();
        self::security();

        $Article = new Article(self::$Db);
        $famille_list = $Article->getFAMILLE();
        $user = new User(self::$Db);
        $user_list = $user->getAll();
        $Tickets = new Tickets(self::$Db);
        $motifs_list = $Tickets->get_scenario();

        return self::$twig->render(
            'forms_ligne.twig',
            [
                'user' => $_SESSION['user'],
                'famille_list' => $famille_list , 
                'user_list' => $user_list,
                'motifs_list' => $motifs_list
            ]
        );
    }

    //@route: /tickets-select-type
    public static function selectTicketsType(){
        self::init();
        self::security();
        $keyword = new Keyword(self::$Db);
        $type_list = $keyword->findByType('tmoti');

        return self::$twig->render(
            'forms_select_type.twig',
            [
                'user' => $_SESSION['user'],
                'type_list' => $type_list 
            ]
        );
    }


    //@route: /tickets-handle-forms
    public static function FormsMarker(){
        self::init();
        self::security();
        $keyword = new Keyword(self::$Db);
        $Ticket = new Tickets(self::$Db);
        $type_list = $keyword->findByType('tmoti');


        //soit le post émane de la creation ( avec motif / soit le post contient un id tickets )
        if (!empty($_POST['TypeTickets'])){
            $tickets = $Ticket->find_first_step($_POST['TypeTickets']);
            //switch pour récuperer la liste des id nécéssaire 
            switch ($tickets) {
                case 'value':
                    # code...
                    break;
                
                default:
                    # code...
                    break;
            }
            $forms = $tickets->forms;
            return self::$twig->render(
                'forms_tickets.twig',
                [
                    'user' => $_SESSION['user'],
                    'forms' => $forms , 
                    'tickets' => $tickets
                ]
            );
            var_dump($tickets);
            die();
        }
        else{
            //le post est un ticket existant:  
            if (!empty($_POST['tickets'])){
                # code...
            }
            else{
                header('location: /tickets-create-forms');
            }
                
            
        }

        //recupère le scénario lié au motif du tickets  :   

        //recupère le forms lié au scénario et à  l'étape : 
        
        //vérifie les droit de l'utilisateur :  

        //affiche le forms 
    }

    protected function handleForms(bool $crea, string $previous , string $motif){

    }

}

