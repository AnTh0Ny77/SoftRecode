<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['AjaxDevis'])){
    $resArray = [];
    $devis =  $Cmd->GetById($_POST['AjaxDevis']);
    $ligne = $Cmd->devisLigne($_POST['AjaxDevis']);
    array_push($resArray , $devis , $ligne) ; 
    echo  json_encode($resArray);
 }
 else {
    echo 'request failed';
 }

}