<?php
require "./vendor/autoload.php";


session_start();
$Database = new App\Database('devisrecode');
$Database->DbConnect();
$Contact = new App\Tables\Contact($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['AjaxContact'])){
    $contact= $Contact->getOne($_POST['AjaxContact']);
    echo  json_encode($contact);
 }
 else {
    echo json_encode("error");
 }


}