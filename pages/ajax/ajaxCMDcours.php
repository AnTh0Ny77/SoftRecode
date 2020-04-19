<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Devis = new App\Tables\Cmd($Database);


// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['AjaxCmd'])){
    $resArray = [];
    $command =  $Devis->GetById($_POST['AjaxCmd']);
    $ligne = $Devis->devisLigne($_POST['AjaxCmd']);
    array_push($resArray , $command , $ligne);
    echo  json_encode($resArray);
 }
 else {
    echo 'request failed';
 }

}