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
if (!empty($_SESSION['search-switch'])) 
{
        $_POST['search'] =  $_SESSION['search-switch'];
        $_SESSION['search_switch'] = '';
}
//switch sur la variable de recherche : 
if ($_POST['search']) 
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
                                $contact_list = $Contact->get_contact_search($client->client__id ,3 );
                                // Donnée transmise au template : 
                                echo $twig->render(
                                        'consult_client.twig',
                                        [
                                                'user' => $_SESSION['user'],
                                                'client' => $client ,
                                                'contact_list' => $contact_list       
                                        ]
                                );
                        }
                        else 
                        {
                                $client_list = $client ;  
                        }

                        break;
                
                //si la chaine fait une longueur 7 et qu'elle ne contient que des numérics
                case (strlen($_POST['search']) == 7 and ctype_digit($_POST['search'])):
                        echo 'recherche de commande : logueur 7 et digit';
                        break;
                
                //par default je recherche un client : 
                default:
                        $client_list = $Client->search_client_devis($_POST['search']);
                        break;
        }

}
else
{
        header('location: dashboard');
}

