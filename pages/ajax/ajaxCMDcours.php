<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devisrecode');
$Database->DbConnect();
$Devis = new App\Tables\Devis($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['AjaxCmd'])){
    $resArray = [];
    $devis =  $Devis->GetById($_POST['AjaxCmd']);
    $ligne = $Devis->devisLigne($_POST['AjaxCmd']);
    array_push($resArray , $devis , $ligne);
    echo  json_encode($resArray);
 }
 else {
    echo 'request failed';
 }

}