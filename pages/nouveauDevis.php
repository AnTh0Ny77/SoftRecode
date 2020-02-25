<?php

use App\Database;
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
 $Article = new App\Tables\Article($Database);
 $Devis = new App\Tables\Devis($Database);
 $Database->DbConnect();
 $clientList = $Client->getAll();
 $keywordList = $Keywords->getI_con();

 //initialisation des variables a false en cas de premiere init :  
 $user =false ;
 $contact = false;
 $choixClient = false;
 $livraison= false;
 $contactList = false;
 $articleTypeList = false;
 $prestaList = false;
 $devisModif = false ;


// si un duplicata de devis a été demandée depuis : modifier devis :  
  if (!empty($_POST['DupliquerDevis'])) {
  $devisModif = [];
  $temp =   $Devis->GetById($_POST['DupliquerDevis']);
  $_SESSION['Client'] = $Client->getOne($temp->devis__client__id);
  if (!empty($temp->devis__contact__id)) {
    $_SESSION['Contact'] = $Contact->getOne($temp->devis__contact__id);
  }
  if (!empty($temp->devis__id_client_livraison)){
    $_POST['choixLivraison'] =  $temp->devis__id_client_livraison;
  }
  $arrayOfDevisLigne = $Devis->devisLigne($_POST['DupliquerDevis']);
    foreach ($arrayOfDevisLigne as $ligne) {
      $xtendArray = $Devis->xtenGarantie($ligne->devl__id);
      $ligne->ordre = $xtendArray;
      array_push($devisModif,$ligne);
    }
  }
$test = json_encode($devisModif);

// si une modication de devis à été demandée depuis : modifier devis : 
if (!empty($_POST['ModifierDevis'])) {
  $devisModif = [];
  $temp =   $Devis->GetById($_POST['ModifierDevis']);
  $_SESSION['Client'] = $Client->getOne($temp->devis__client__id);
  if (!empty($temp->devis__contact__id)) {
    $_SESSION['Contact'] = $Contact->getOne($temp->devis__contact__id);
  }
  if (!empty($temp->devis__id_client_livraison)){
    $_POST['choixLivraison'] =  $temp->devis__id_client_livraison;
  }
  $arrayOfDevisLigne = $Devis->devisLigne($_POST['ModifierDevis']);
    foreach ($arrayOfDevisLigne as $ligne) {
      $xtendArray = $Devis->xtenGarantie($ligne->devl__id);
      $ligne->ordre = $xtendArray;
      array_push($devisModif,$ligne);
    }
    // on crée un variable de session pour la modification :
      $_SESSION['ModifierDevis'] = $_POST['ModifierDevis'];
}
$test = json_encode($devisModif);

 
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
   $articleTypeList = $Article->getAll();
   $prestaList = $Keywords->getPresta();
}

// Si un nouveau contact a été crée :  traitement par la classe Form 
if (!empty($_POST['fonctionContact']) && isset($_POST['fonctionContact'])) {
  $contact = Forms::contactValidate($_POST);
  if ($contact == !false ) {
    $_POST['choixContact'] = $Contact->insertOne($contact['fonctionContact'],$contact['civiliteContact'],$contact['nomContact'],$contact['prenomContact'],
    $contact['telContact'],$contact['faxContact'],$contact['mailContact'],$_SESSION['Client']->client__id);
    
  }
  
}

// Si un choix de contact à été effectué : 
if(!empty($_POST['choixContact'])){
  $_SESSION['Contact'] = $Contact->getOne($_POST['choixContact']);
  
}
// si la session contact est deja ouverte : 
if (isset( $_SESSION['Contact'])) {
  $contact = $_SESSION['Contact'];
}

// si un choix d'adresse de livraison à été efectué : 
if (!empty($_POST['choixLivraison'])) {
   $_SESSION['livraison'] = $Client->getOne($_POST['choixLivraison']); 
   $livraison = $_SESSION['livraison'];
   
}

// si l'utilisateur a demandé la supression du client: 
if (!empty($_POST['deleteContact'])) {
 unset($_SESSION['Contact']);
 $contact = false;
}

// le formulaire de creation de pdf a été soumis : redirection vers mes devis -> 
if (!empty($_POST['ValidDevis'])) {
  header("Location: mesDevis");
}

// Donnée transmise au template : 
echo $twig->render('nouveauDevis.twig',[
   'user'=>$_SESSION['user'],
   'clientList'=>$clientList,
   'client'=>$user,
   'contact'=> $contact,
   'keywordList'=>$keywordList,
   'contactList'=>$contactList,
   'articleList'=>$articleTypeList,
   'prestaList'=> $prestaList,
   'livraison' => $livraison,
   'devisModif' => $test
]);;
