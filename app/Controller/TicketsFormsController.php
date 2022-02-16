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
        $subject_list = null;
        $crea_forms = null;
        $motif = null;
        $motif_lib = null;     
        //soit le post émane de la creation ( avec motif / soit le post contient un id tickets )
        if (!empty($_POST['TypeTickets'])){
            $crea_forms = 1 ;
            $tickets = $Ticket->find_first_step($_POST['TypeTickets']);
            $motif = $tickets->tks__motif_ligne;
            $motif_lib = $tickets->tks__lib;
            if (!empty($tickets->kw__info)){
                $request = explode('|',$tickets->kw__info);
                $subject_list = $Ticket->get_subject_table($request[0]);
                $subject_list = $Ticket->get_subject_list($request , $subject_list['TABLE_NAME']);
            }
          
            return self::$twig->render(
                'forms_tickets_generator.html.twig',
                [
                    'subject_list' => $subject_list,
                    'user' => $_SESSION['user'],
                    'forms' => $tickets->forms , 
                    'tickets' => $tickets,
                    'crea_forms' => $crea_forms,
                    'type_tickets' => $_POST['TypeTickets'],
                    'motif' => $motif,
                    'libelle' => $motif_lib , 
                    'multiparts' =>  $tickets->multiparts
                ]
            );
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



    //@route: /tickets-post-data
    public static function formsHandler(){
        self::init();
        self::security();
       
        return self::handleForms($_POST, $_FILES);
    }

    public static function handleForms(array $post , array $files){
            $Ticket = new Tickets(self::$Db);
            if (!empty($post['creaForms'])){

                //si un sujet est donné : 
                if (!empty($post['idSubject'])) {
                    $post['idSubject'] = $post['idSubject'];
                }else $post['idSubject'] = null;
                //si un titre est donné : 
                if (!empty($post['Titre'])) {
                    $post['Titre'] = $post['Titre'];
                } else $post['Titre'] = null;

                $post['creator'] = $_SESSION['user']->id_utilisateur;
                $new_tickets = $Ticket->insert_ticket($post);
                $post['id_ligne'] = $new_tickets;
                $post['dt'] = date('now');
                $new_line = $Ticket->insert_line($post);
                $Ticket->insert_multipart('C:\laragon\www\SoftRecode', $new_line , $files );
                $new_field = $Ticket->insert_field($post,$new_line);
            }
            else{
                //j'update
            }
    }

}

