<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

if ($_SESSION['user']->user__cmd_acces < 10 ) {
  header('location: noAccess');
}
 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']) || empty($_POST['ValideCmd'])) {
    header('location: login');
 }else{

 

$user = $_SESSION['user'];
//Connexion et requetes : 
$Database = new App\Database('devis');
$Devis = new App\Tables\Devis($Database);
$Database->DbConnect();
$Devis = new App\Tables\Devis($Database);
$devis = $Devis->GetById($_POST['ValideCmd']);


$arrayOfDevisLigne = $Devis->devisLigne($_POST['ValideCmd']);
foreach ($arrayOfDevisLigne as $ligne) {
    $xtendArray = $Devis->xtenGarantie($ligne->devl__id);
    $ligne->devl__prix_barre = $xtendArray;
  } 
$jsonPack = json_encode($arrayOfDevisLigne);

// Donnée transmise au template : 
echo $twig->render('commande.twig',['user'=>$user,
'user'=> $user,
'devis'=> $devis,
'devisLigne'=> $arrayOfDevisLigne,
'jsonPack'=> $jsonPack,
]);


 }