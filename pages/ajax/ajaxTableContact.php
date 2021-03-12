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

// requete table Contact:
 if (!empty($_POST['AjaxContactTable'])){
    $contactList = $Contact->getFromLiaison($_POST['AjaxContactTable']);
    echo  json_encode($contactList);
 }
 else {
    echo 'request failed';
 }

}