<?php
require "./vendor/autoload.php";


session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Contact = new App\Tables\Contact($Database);


if (empty($_SESSION['user']->id_utilisateur)) {
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