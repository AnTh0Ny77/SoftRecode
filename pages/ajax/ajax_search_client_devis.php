<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new App\Tables\Client($Database);
$Contact = new App\Tables\Contact($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
        header('location: login');
} else {
        // requete table client:
        if (!empty($_POST['search'])) {
                $search =  htmlentities($_POST['search']);
                $clientList = $Client->search_client_devis($search);
                foreach ($clientList as $client) {
                        $contactList = $Contact->getFromLiaison($client->client__id);
                        $client->contact_list = $contactList;
                }
                $clientList = json_encode($clientList);

                echo $clientList;
        } else {
                $clientList = [];
                $clientList = json_encode($clientList);
                echo  $clientList;
        }
}
