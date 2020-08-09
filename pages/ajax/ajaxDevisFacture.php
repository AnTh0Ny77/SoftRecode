<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Client = new App\Tables\Client($Database);
$Contact = new App\Tables\Contact($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['AjaxDevis'])){
    $resArray = [];
    $devis =  $Cmd->GetById($_POST['AjaxDevis']);
    $client = $Client->getOne($devis->client__id);
    $contactList = $Contact->getFromLiaison($client->client__id);
    

    array_push($resArray , $devis , $client , $contactList ) ; 
    echo  json_encode($resArray);
 }
 else {
    echo 'request failed';
 }

}