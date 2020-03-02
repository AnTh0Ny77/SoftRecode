<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }else{

//Connexion et requetes : 
$Database = new App\Database('devisrecode');
$Devis = new App\Tables\Devis($Database);
$Database->DbConnect();




 }