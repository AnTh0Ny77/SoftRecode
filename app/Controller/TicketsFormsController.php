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

    public static function find_for_duplicata(string $ticket_id, object $Tickets){
        $response = [];
        $response['fields'] = [];
        $ticket = $Tickets->findOne(intval($ticket_id));
        $response['TypeTickets'] = $ticket->tk__motif;
        foreach ($ticket->lignes as $ligne){
                if (!empty($ticket->first_line) and $ticket->first_line->tkl__id === $ligne->tkl__id ){
                        foreach ($ligne->fields as $key => $field){
                            if (preg_match('/@/', $field->tklc__memo) == 1){
                                $pattern = "@";
                                $client = explode($pattern ,$field->tklc__memo);
                                if ($client[0] === 'client__id') {
                                    $response['Client'] = $client[2];
                                }
                                else{
                                    //trouve le nom du champs et la valeur 
                                }
                            }else{
                                
                                $response['fields'][$field->tklc__nom_champ] = $field->tklc__memo;

                            }
                        }
                }
        }   
        return $response;
    }

    //@route: /tickets-handle-forms
    public static function FormsMarker(){
        self::init();
        self::security();
        $keyword = new Keyword(self::$Db);
        $Ticket = new Tickets(self::$Db);
        $presta_list = $keyword->findByType('pres');
        $type_list = $keyword->findByType('tmoti');
        $subject_list = null;
        $crea_forms = null;
        $motif = null;
        $motif_lib = null;  
        $duplicata_ticket = null;
        $preset = [];   
        //preset traitement : 
        if (!empty($_GET)){
            //si duplicata : 
            if (!empty($_GET['duplicata'])){
                $duplicata = self::find_for_duplicata($_GET['duplicata'] , $Ticket);
                if (!empty($duplicata)){
                    $duplicata_ticket = $_GET['duplicata'];
                    if (!empty($duplicata['Client'])){
                        $_GET['Client'] = $duplicata['Client'];
                    }
                    if (!empty($duplicata['TypeTickets'])){
                        $_GET['TypeTickets'] = $duplicata['TypeTickets'];
                    }
                    if (!empty($duplicata['fields'])){
                        foreach ($duplicata['fields'] as $key => $value){
                            $preset[$key] = $value;
                        }
                    }
                }
            }
            if (!empty($_GET['TypeTickets'])){
                $_POST['TypeTickets'] = $_GET['TypeTickets'];
            }
            foreach ($_GET as $key => $value){
                if ($key != 'TypeTickets'){
                    $preset[$key] = $value;
                }
            }
        }

        //soit le post émane de la creation ( avec motif / soit le post contient un id tickets )
        if (!empty($_POST['TypeTickets'])){
            $crea_forms = 1 ;
            $tickets = $Ticket->find_first_step($_POST['TypeTickets']);
            $motif = $tickets->tks__motif_ligne;
            $motif_lib = $tickets->tks__lib;            
            return self::$twig->render(
                'forms_tickets_generator.html.twig',
                [
                    // 'subject_list' => $subject_list,
                    'user' => $_SESSION['user'],
                    'presta_list' => $presta_list ,
                    'preset' => $preset ,
                    'duplicata_ticket' => $duplicata_ticket,
                   
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
                $sujet = null;
                //recueperer les données du tickets + entitées 
                $ticket = $Ticket->findOne($_POST['tickets']);
                $config = file_get_contents('configDisplay.json');
                $config = json_decode($config);
                $config = $config->entities;
                foreach ($ticket->lignes as $ligne){
                    $entitites_array = [];
                    $pattern = "@";
                    $other_fields = [];
                    foreach ($ligne->fields as $key => $field) {
                        if (stripos($field->tklc__memo, $pattern)) {
                            foreach ($config as  $entitie) {
                                $secondary_entities = $Ticket->create_secondary_entities($entitie, $field->tklc__memo);
                                if (!empty($secondary_entities)) {
                                    array_push($entitites_array, $secondary_entities);
                                }
                            }
                        } else {
                            $field->tklc__memo = nl2br($field->tklc__memo);
                            array_push($other_fields, $field);
                        }
                        $ligne->fields =  $other_fields;
                    }
                    if (!empty($entitites_array)) {
                        $ligne->entities = $entitites_array;
                    }
                }
                $sujet = null;
                foreach ($config as  $entitie) {
                    if (!empty($ticket->sujet)) {
                        $subject_identifier = $ticket->sujet->tksc__option;
                        $display_entitie = $Ticket->createEntities($entitie, $subject_identifier, $ticket->tk__motif_id);
                        if (!empty($display_entitie)) {
                            $sujet = $display_entitie;
                        }
                    }
                }
                $last_ligne = end($ticket->lignes);
               
                foreach ($config as  $entitie) {
                    if (!empty($ticket->sujet)) {
                        $subject_identifier = $ticket->sujet->tksc__option;
                        $display_entitie = $Ticket->createEntities($entitie, $subject_identifier, $ticket->tk__motif_id);
                        if (!empty($display_entitie)) {
                            $sujet = $display_entitie;
                        }
                    }
                }
               
                //recupere le formulaire :  
                if (!empty($_POST['scenario'])){
                   
                    $forms = $Ticket->find_next_step($_POST['scenario']);
                    $motif_lib = $forms->tks__lib;
                    $motif = $forms->tks__motif_ligne;
                    return self::$twig->render(
                        'forms_tickets_steps_generator.html.twig',
                        [
                            // 'subject_list' => $subject_list,
                            'current_tickets' => $ticket,  
                            'presta_list' => $presta_list ,
                            'ligne' => $last_ligne ,
                            'user' => $_SESSION['user'],
                            'ligne_forms' => $forms,
                            'forms' => $forms->forms,
                            'sujet' => $sujet,
                            'tickets' => $forms,
                            'crea_forms' => $crea_forms,
                            'motif' => $motif,
                            'libelle' => $motif_lib,
                            'multiparts' =>   $forms->multiparts
                        ]
                    );

                } else {
                    header('location: tickets-create-forms');
                }

            }
            else{
                header('location: tickets-create-forms');
            }
        }
    }

    //@route: /tickets-post-data
    public static function formsHandler(){
        self::init();
        self::security();
        self::handleForms($_POST, $_FILES);
        return header('location: tickets-display-list?searchTickets=&StateFilter[]=Lus&&StateFilter[]=NonLus&AuthorFilter=1&TypeFilter=DP');
    }

    public static function handleForms(array $post , array $files){
       
            $Ticket = new Tickets(self::$Db);
            if (!empty($post['creaForms'])){
                //si un titre est donné : 
                if (!empty($post['Titre'])){
                    $post['Titre'] = $post['Titre'];
                } else $post['Titre'] = null;
                    $post['creator'] = $_SESSION['user']->id_utilisateur;
                    $new_tickets = $Ticket->insert_ticket($post );
                    $post['id_ligne'] = $new_tickets;
                    $post['dt'] = date('Y-m-d H:i:s');
                   
                    $new_line = $Ticket->insert_line($post);
                    //$Ticket->insert_multipart('C:\laragon\www\SoftRecode\upload', $new_line , $files );
                    $Ticket->attribute_attachements($new_line);
                    $new_field = $Ticket->insert_field($post,$new_line , $new_tickets);
                    if (!empty($post['duplicate'])){
                        $General = new General(self::$Db);
                        $groupe = $Ticket->return_group($post['duplicate']);
                        $General->updateAll('ticket', $groupe , 'tk__groupe', 'tk__id',  $post['duplicate']);
                        $General->updateAll('ticket', $groupe , 'tk__groupe', 'tk__id',  $new_tickets);
                    }
            }
            else{
                if (!empty($_POST['currentTicket'])) {
                    $post['id_ligne'] = $_POST['currentTicket'] ;
                    $post['creator'] = $_SESSION['user']->id_utilisateur;
                    $post['dt'] = date('Y-m-d H:i:s');
                    if (intval($post['A_Qui']) == 9999) {
                        $demandeur = $Ticket->return_demandeur($_POST['currentTicket']);
                        $post['A_Qui'] = $demandeur->tkl__user_id;
                    }
                    $new_line = $Ticket->insert_line($post);
                    $new_field = $Ticket->insert_field($post, $new_line, $_POST['currentTicket']);
                    $Ticket->attribute_attachements($new_line);
                    $General = new General(self::$Db);
                    $General->updateAll('ticket', 0 , 'tk__lu', 'tk__id', $_POST['currentTicket']);
                }
            }
    }

}

