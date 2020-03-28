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
 $Database = new App\Database('devis');
 $Client = new App\Tables\Client($Database);
 $Keywords = new App\Tables\Keyword($Database);
 $Contact = new App\Tables\Contact($Database);
 $Article = new App\Tables\Article($Database);
 $Devis = new App\Tables\Devis($Database);
 $Database->DbConnect();
 $keywordList = $Keywords->getI_con();


 //initialisation des variables a false en cas de premiere init :  
 $user =false ;
 $contact = false;
 $choixClient = false;
 $livraison= false;
 $articleTypeList = false;
 $prestaList = false;
 $devisModif = false ;
 $sessionModif = false;
 $societe = false;
 $temp = false;




// si un duplicata de devis a été demandée depuis : modifier devis :  
  if (!empty($_POST['DupliquerDevis'])) {

  $devisModif = [];

  $temp =   $Devis->GetById($_POST['DupliquerDevis']);

  $societe = $Client->getOne($temp->devis__client__id);

  if (!empty($temp->devis__contact__id)) {
    $contact = $Contact->getOne($temp->devis__contact__id);
  }

  if (!empty($temp->devis__id_client_livraison)){
    $livraison =  $Client->getOne($temp->devis__id_client_livraison);
  }

  $arrayOfDevisLigne = $Devis->devisLigne($_POST['DupliquerDevis']);
    foreach ($arrayOfDevisLigne as $ligne) {
      $xtendArray = $Devis->xtenGarantie($ligne->devl__id);
      $ligne->ordre = $xtendArray;
      array_push($devisModif,$ligne);
    }
  }



// si une modication de devis à été demandée depuis : modifier devis : 
if (!empty($_POST['ModifierDevis'])) {
  $devisModif = [];

  $temp =   $Devis->GetById($_POST['ModifierDevis']);

  $societe = $Client->getOne($temp->devis__client__id);

  if (!empty($temp->devis__contact__id)) {
    $contact = $Contact->getOne($temp->devis__contact__id);
  }

  if (!empty($temp->devis__id_client_livraison)){
    $livraison =  $Client->getOne($temp->devis__id_client_livraison);
  }

  $arrayOfDevisLigne = $Devis->devisLigne($_POST['ModifierDevis']);
    foreach ($arrayOfDevisLigne as $ligne) {
      $xtendArray = $Devis->xtenGarantie($ligne->devl__id);
      $ligne->ordre = $xtendArray;
      array_push($devisModif,$ligne);
    }

      $sessionModif = $_POST['ModifierDevis'];
}


// tableau si devis modif et requete des differentes listes de la page: 
$test = json_encode($devisModif);
$articleTypeList = $Article->getAll();
$prestaList = $Keywords->getPresta();



// le formulaire de creation de pdf a été soumis : redirection vers mes devis -> 
if (!empty($_POST['ValidDevis'])) {
  header("Location: mesDevis");
}



// Donnée transmise au template : 
echo $twig->render('nouveauDevis.twig',[
   'user'=>$_SESSION['user'],
   'contact' => $contact,
   'temp' => $temp,
   'societe'=> $societe,
   'keywordList'=>$keywordList,
   'articleList'=>$articleTypeList,
   'prestaList'=> $prestaList,
   'livraison' => $livraison,
   'devisModif' => $test,
   'sessionModif'=> $sessionModif
]);;
