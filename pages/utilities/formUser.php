<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']) ) {
    header('location: login');
 }else{


//Connexion et requetes : 
$Database = new App\Database('devis');
$Database->DbConnect();
$User = new App\Tables\Users($Database);

// traitement du formulaire : 
$message = "";
if (!empty($_POST['idUser']) &&  !empty($_POST['loginUser']) && !empty($_POST['lognecUser']) && !empty($_POST['passwordUser'])) {



$password = password_hash($_POST['passwordUser'], PASSWORD_DEFAULT);

$user = $User->create(intval($_POST['idUser']), $_POST['loginUser'],$_POST['dateUser'],$_POST['prenomUser'],$_POST['nameUser'],$_POST['lognecUser'], $_POST['emailUser'],
$_POST['posteUser'], $_POST['gsmUser'], $_POST['crmUser'], $_POST['poUser'], $_POST['devisUser'],$_POST['cmdUser'],$_POST['sasieUser'], $_POST['factureUser'],
$_POST['adminUser'], $password);

header('location: utilisateurs');

    
}
else {
    header('location: addUser');
}



}