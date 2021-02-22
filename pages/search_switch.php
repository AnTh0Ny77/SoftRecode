<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new \App\Tables\Client($Database);
$Contact = new \App\Tables\Contact($Database);
$Cmd = new \App\Tables\Cmd($Database);


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
                       
                        $commande = $Cmd->GetById($_POST['search']);
                                echo $twig->render(
                                        'consult_commande.twig',
                                        [
                                                'user' => $_SESSION['user'],
                                                'commande' => $commande 
                                        ]);
                        break;
                
                //par default je recherche un client : 
                default:
                        $client_list = $Client->search_client_devis($_POST['search']);
                        // Donnée transmise au template : 
                        echo $twig->render(
                                'consult_client_list.twig',
                                [
                                        'user' => $_SESSION['user'],
                                        'client_list' => $client_list 
                                ]);
                        break;
        }

}
else
{
        header('location: dashboard');
}

