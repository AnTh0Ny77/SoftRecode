<?php

use App\Methods\Forms;

require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }

 //Connexion et requetes : 
 $Database = new App\Database('devisrecode');
 $Database->DbConnect();
 $Client = new App\Tables\Client($Database,'client');
 $clientList = $Client->getAll();
 $keywords = new App\Tables\Keyword($Database);
 $keywordList = $keywords->getAll();

 $user= $_SESSION['user'];
 $choixClient = false;
 $client = false ;


// Si un nouveau client à été crée  :  traitement par la classe Form 
if (isset($_POST['societe'])) {
      $societe = Forms::societeValidate($_POST);

      if ($societe == !false ) {
        $_POST['choixClient'] = $Client->insertOne($societe['societe'],$societe['adr1'],$societe['adr2'],$societe['cp'],$societe['ville']); 
      }
}


 // Si un choix de client a été effectué dans la base de donnéé: 
 if (isset($_POST['choixClient'])) {
   $choixClient = ($_POST['choixClient']);
   $client = $Client->getOne($choixClient);
   $_SESSION['Client'] = $client;
 }

// Donnée transmise au template : 
echo $twig->render('nouveauDevis.twig',[
   'user'=>$user,
   'clientList'=>$clientList,
   'client'=>$client,
   'keywordList'=>$keywordList
]);;
