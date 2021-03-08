<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new \App\Tables\Client($Database);
$Contact = new \App\Tables\Contact($Database);
$Keyword = new \App\Tables\Keyword($Database);
$Cmd = new \App\Tables\Cmd($Database);
$Stats = new App\Tables\Stats($Database);
$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
use App\Methods\Pdfunctions;
use App\Methods\Devis_functions;

//URL bloqué si pas de connexion :
if (empty($_SESSION['user'])) 
{
        header('location: login');
}

//si une redirection arrive d'une autre page par le biais de la variable de session :
if (!empty($_SESSION['search_switch'])) 
{
        $_POST['search'] =  $_SESSION['search_switch'];
        $_SESSION['search_switch'] = '';
}
//switch sur la variable de recherche : 
if (!empty($_POST['search'])) 
{
        switch (strlen($_POST['search'])) 
        {
                //si la chaine fait une longueur 6 et qu'elle ne contient que des numérics
                case (strlen($_POST['search']) == 6 and ctype_digit($_POST['search'])):
                        //je fais une recherche par id 
                        $client = $Client->search_client_devis($_POST['search']);
                        foreach ($client as $client_results) 
                        {
                                $date_modif = new DateTime($client_results->client__dt_last_modif);
                                $client_results->client__dt_last_modif = $date_modif->format('d/m/Y');
                        }
                        //Si le résultat est bien unique : 
                        if (count($client) == 1) 
                        {
                                $client = $Client->getOne($client[0]->client__id);
                                //compte les contacts : 
                                $count_contact = $Contact->count_contact($client->client__id);
                                //liste des 3 principaux contacts
                                $contact_list = $Contact->get_contact_search($client->client__id ,3 );
                                //si la liste des contacts est plus grande que les 3 contact proposés : 
                                if (intval($count_contact[0]["COUNT(*)"]) > count($contact_list)) 
                                {
                                       $extendre_contacts = intval($count_contact[0]["COUNT(*)"]) - count($contact_list) ;
                                }
                                else $extendre_contacts = false ;
                                //liste des quinze dernière commmandes : 
                                $cmd_list = $Cmd->get_by_client_id($client->client__id , 10 );
                                //format les dates de la commande : 
                                foreach ($cmd_list as $cmd) 
                                {
                                        $date =  new DateTime($cmd->cmd__date_devis);
                                        $cmd->cmd__date_devis =  $date->format('d/m/Y');
                                }
                                // Donnée transmise au template : 
                                echo $twig->render(
                                        'consult_client.twig',
                                        [
                                                'user' => $_SESSION['user'],
                                                'client' => $client ,
                                                'contact_list' => $contact_list ,
                                                'etendre_contact' =>  $extendre_contacts ,
                                                'commandes_list' => $cmd_list
                                        ]);
                        }
                        else 
                        {
                                $client_list = $client ;  
                                 // Donnée transmise au template : 
                                 echo $twig->render(
                                        'consult_client_list.twig',
                                        [
                                                'user' => $_SESSION['user'],
                                                'client_list' => $client_list 
                                        ]);
                        }

                        break;
                
                //si la chaine fait une longueur 7 et qu'elle ne contient que des numérics
                case (strlen($_POST['search']) == 7 and ctype_digit($_POST['search'])):
                        $etat_list = $Keyword->get_etat();
                        $commande = $Cmd->GetById($_POST['search']);
                        $lignes = $Cmd->devisLigne($_POST['search']);

                        if ($commande->devis__etat == 'VLD' || $commande->devis__etat == 'VLA') 
                        {
                                $totaux = Pdfunctions::totalFacturePDF($commande, $lignes );
                        }
                        else 
                        {
                                $totaux = Pdfunctions::totalFacturePRO($commande, $lignes );
                        }
                        


                        //formatte les dates pour l'utilisateur : 
                        $date =  new DateTime($commande->devis__date_crea);
                        $commande->devis__date_crea =  $date->format('d/m/Y');
                        if (!empty($commande->cmd__date_cmd)) 
                        {
                                $date =  new DateTime($commande->cmd__date_cmd);
                                $commande->cmd__date_cmd =  $date->format('d/m/Y');
                        }
                        if (!empty($commande->cmd__date_fact)) 
                        {
                                $date =  new DateTime($commande->cmd__date_fact);
                                $commande->cmd__date_fact =  $date->format('d/m/Y');
                        }
                        
                                echo $twig->render(
                                        'consult_commande.twig',
                                        [
                                                'user' => $_SESSION['user'],
                                                'commande' => $commande ,
                                                'etat_list' => $etat_list,
                                                'lignes' => $lignes , 
                                                'totaux' => $totaux
                                        ]);
                        break;
                
                //par default je recherche un client : 
                default:
                        $client_list = $Client->search_client_devis($_POST['search']);
                        foreach ($client_list as $client) 
                        {
                                $date_modif = new DateTime($client->client__dt_last_modif);
                                $client->client__dt_last_modif = $date_modif->format('d/m/Y');
                        }
                        if (count($client_list) == 1) 
                        {
                                $client = $Client->getOne($client_list[0]->client__id);
                                //compte les contacts : 
                                $count_contact = $Contact->count_contact($client->client__id);
                                //liste des 3 principaux contacts
                                $contact_list = $Contact->get_contact_search($client->client__id, 3);
                                //si la liste des contacts est plus grande que les 3 contact proposés : 
                                if (intval($count_contact[0]["COUNT(*)"]) > count($contact_list)) {
                                        $extendre_contacts = intval($count_contact[0]["COUNT(*)"]) - count($contact_list);
                                } else $extendre_contacts = false;
                                //liste des quinze dernière commmandes : 
                                $cmd_list = $Cmd->get_by_client_id($client->client__id, 10);
                                //format les dates de la commande : 
                                foreach ($cmd_list as $cmd) {
                                        $date =  new DateTime($cmd->cmd__date_devis);
                                        $cmd->cmd__date_devis =  $date->format('d/m/Y');
                                }
                                // Donnée transmise au template : 
                                echo $twig->render(
                                        'consult_client.twig',
                                        [
                                                'user' => $_SESSION['user'],
                                                'client' => $client,
                                                'contact_list' => $contact_list,
                                                'etendre_contact' =>  $extendre_contacts,
                                                'commandes_list' => $cmd_list
                                        ]
                                );
                        }
                        else 
                        {
                                // Donnée transmise au template : 
                                echo $twig->render(
                                        'consult_client_list.twig',
                                        [
                                                'user' => $_SESSION['user'],
                                                'client_list' => $client_list
                                        ]
                                );
                        }
                        
                        break;
        }

}
else
{
        header('location: dashboard');
}

