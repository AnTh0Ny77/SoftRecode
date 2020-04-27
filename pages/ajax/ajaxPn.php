<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

    // requete table client:
        if (!empty($_POST['AjaxPn'])){
            $pnList = json_encode($Article->getPn($_POST['AjaxPn']));
            echo $pnList;
            
        }
        else {
            echo 'request failed';
        }


 }