<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }

 //Connexion et requete liste  clients  : 
 $Database = new App\Database('devisrecode');
 $Database->DbConnect();
 $Client = new App\Tables\Client($Database,'client2');
 $clientList = $Client->getAll();

 $user= $_SESSION['user'];
 $choixClient = null;
 if (isset($_POST['choixClient'])) {
   $choixClient = $_POST['choixClient'];
  // $client = $Client->getOne($_POST['choixClient']);
 }

// Donnée transmise au template : 
echo $twig->render('nouveauDevis.twig',[
   'user'=>$user,
   'clientList'=>$clientList,
   'client'=>$choixClient
]);;