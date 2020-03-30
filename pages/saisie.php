<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }else{

if ($_SESSION['user']->user__saisie_acces < 10 ) {
header('location: noAccess');
}
$user = $_SESSION['user'];

//Connexion et requetes : 
$Database = new App\Database('devis');
$Database->DbConnect();
$Command = new \App\Tables\Command($Database);
$commandList =  $Command->getByStatus();

foreach ($commandList as $command) {
    $commandDate = date_create($command->cmd__date_crea);
    $date = date_format($commandDate, 'd/m/Y');
    $command->cmd__date_crea = $date;
 }



// Donnée transmise au template : 
echo $twig->render('saisie.twig',[
    "user"=> $user, 
    'listOfCommand' => $commandList
]);


 }