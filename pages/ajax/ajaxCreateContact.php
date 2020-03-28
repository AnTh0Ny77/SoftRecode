<?php
require "./vendor/autoload.php";


session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Contact = new App\Tables\Contact($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['inputStateContact'])){
    $response = $Contact->insertOne($_POST['inputStateContact'],$_POST['inputCiv'],$_POST['nomContact'],$_POST['prenomContact'],
    $_POST['telContact'],$_POST['faxContact'],$_POST['mailContact'],$_POST['societeLiaison']);
    $contact = $Contact->getOne(intval($response));
    echo  json_encode($contact);
 }
 else {
    echo 'request failed';
 }


}