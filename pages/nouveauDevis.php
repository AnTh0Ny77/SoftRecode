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
 $Client = new App\Tables\Client($Database);
 $Keywords = new App\Tables\Keyword($Database);
 $Contact = new App\Tables\Contact($Database);
 $Database->DbConnect();
 $clientList = $Client->getAll();
 $keywordList = $Keywords->getAll();

 //initialisation des variables a false en cas de premiere init :  
 $user =false ;
 $contact = false;
 $choixClient = false;
 $contactList = false;
 
// Si un nouveau client à été crée  :  traitement par la classe Form 
if ( isset($_POST['societe']) && !empty($_POST['societe'])) {
      $societe = Forms::societeValidate($_POST);
      if ($societe == !false ) {
        $_POST['choixClient'] = $Client->insertOne($societe['societe'],$societe['adr1'],$societe['adr2'],$societe['cp'],$societe['ville']); 
      }
}

// Si un choix de client a été effectué dans la base de donnéé: 
if (!empty($_POST['choixClient'])) {
   $_SESSION['Contact'] = "";
   $_SESSION['Client'] =$Client->getOne($_POST['choixClient']);
   
 }

// Si un client a été entré en session : 
if(!empty($_SESSION['Client'])){
  $contactList = $Contact->getFromLiaison(intval($_SESSION['Client']->client__id));
}
 
// si la session client est deja ouverte
if (isset( $_SESSION['Client'])) {
   $user = $_SESSION['Client'];
}

// Si un nouveau contact a été crée :  traitement par la classe Form 
if (!empty($_POST['fonctionContact'])) {
  $contact = Forms::contactValidate($_POST);
  if ($contact == !false ) {
    $_POST['choixContact'] = $Contact->insertOne($contact['fonctionContact'],$contact['civiliteContact'],$contact['nomContact'],$contact['prenomContact'],
    $contact['telContact'],$contact['faxContact'],$contact['mailContact']);
  }
// Si un seul champ à été rempli pas besoin d'enregistrer : 
  elseif ($contact == 206) {
  }
}

// Si un choix de contact à été effectué : 
if(!empty($_POST['choixContact'])){
  $_SESSION['Contact'] = $Contact->getOne($_POST['choixContact']);
  
}

// si la session contact est deja ouverte
if (isset( $_SESSION['Contact'])) {
  $contact = $_SESSION['Contact'];
}

// Donnée transmise au template : 
echo $twig->render('nouveauDevis.twig',[
   'user'=>$_SESSION['user'],
   'clientList'=>$clientList,
   'client'=>$user,
   'contact'=> $contact,
   'keywordList'=>$keywordList,
   'contactList'=>$contactList
]);;
