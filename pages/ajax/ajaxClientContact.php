<?php
require "./vendor/autoload.php";


session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new App\Tables\Client($Database);
$Contact = new App\Tables\Contact($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['AjaxDevis'])){
    $client = $Client->getOne($_POST['AjaxDevis']);
    $contact= $Contact->getFromLiaison($client->client__id);
    $response = [];
    array_push($response, $client , $contact);
    echo  json_encode($response);
 }
 else 
 {
    echo json_encode("error");
 }


}