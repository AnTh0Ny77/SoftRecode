<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

if ($_SESSION['user']->user__devis_acces < 10) {
  header('location: noAccess');
}
//URL bloqué si pas de connexion :
if (empty($_SESSION['user']) || empty($_POST['ValideCmd'])) {
  header('location: login');
} else {

  $user = $_SESSION['user'];

  //Connexion et requetes : 
  $Database = new App\Database('devis');
  $Devis = new App\Tables\Cmd($Database);
  $Database->DbConnect();

  //recupere le devis:
  $devis = $Devis->GetById($_POST['ValideCmd']);

  //recupere les lignes de devis:
  $arrayOfDevisLigne = $Devis->devisLigne_sous_ref($_POST['ValideCmd']);



  //recupere les garanties liees:
  foreach ($arrayOfDevisLigne as $ligne) {
    $xtendArray = $Devis->xtenGarantie($ligne->devl__id);
    $ligne->devl__prix_barre = $xtendArray;
  }

  //encode en json pour Javascript:
  $jsonPack = json_encode($arrayOfDevisLigne);

  // Donnée transmise au template : 
  echo $twig->render('commande.twig', [
    'user' => $user,
    'user' => $user,
    'devis' => $devis,
    'devisLigne' => $arrayOfDevisLigne,
    'jsonPack' => $jsonPack,
  ]);
}
