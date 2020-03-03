<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']) || empty($_POST['ValideCmd'])) {
    header('location: login');
 }else{

$user = $_SESSION['user'];
//Connexion et requetes : 
$Database = new App\Database('devisrecode');
$Devis = new App\Tables\Devis($Database);
$Database->DbConnect();
$Devis = new App\Tables\Devis($Database);
$devis = $Devis->GetById($_POST['ValideCmd']);
$arrayOfDevisLigne = $Devis->devisLigne($_POST['ValideCmd']);
foreach ($arrayOfDevisLigne as $ligne) {
    $xtendArray = $Devis->xtenGarantie($ligne->devl__id);

  } 
//var_dump($arrayOfDevisLigne);


// Donnée transmise au template : 
echo $twig->render('commande.twig',['user'=>$user,
'user'=> $user,
'devis'=> $devis,
'devisLigne'=> $arrayOfDevisLigne
]);


 }