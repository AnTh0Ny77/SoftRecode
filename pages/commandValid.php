<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

if ($_SESSION['user']->user__devis_acces < 10 ) {
  header('location: noAccess');
}
 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']) || empty($_POST['ValideCmd'])) {
    header('location: login');
 }else{

$user = $_SESSION['user'];

//Connexion et requetes : 
$Database = new App\Database('devis');
$Devis = new App\Tables\Cmd($Database);
$Database->DbConnect();

//recupere le devis:
$devis = $Devis->GetById($_POST['ValideCmd']);

//recupere les lignes de devis:
$arrayOfDevisLigne = $Devis->devisLigne($_POST['ValideCmd']);



// foreach ($arrayOfDevisLigne as $lignes) {
//   $arrayOfData = [];
//   array_push($arrayOfData, intval($lignes->devl__ordre));
//   array_push($arrayOfData, intval($lignes->devl_quantite));
//   array_push($arrayOfData, $lignes->devl__note_client);
// var_dump( $arrayOfData);
// }

//recupere les garanties liees:
foreach ($arrayOfDevisLigne as $ligne) 
{
  $xtendArray = $Devis->xtenGarantie($ligne->devl__id);
  $ligne->devl__prix_barre = $xtendArray;
}

//encode en json pour Javascript:
$jsonPack = json_encode($arrayOfDevisLigne);

// Donnée transmise au template : 
echo $twig->render('commande.twig',['user'=>$user,
'user'=> $user,
'devis'=> $devis,
'devisLigne'=> $arrayOfDevisLigne,
'jsonPack'=> $jsonPack,
]);


 }