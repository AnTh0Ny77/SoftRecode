<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }
 unset($_SESSION['Contact']);
 unset($_SESSION['Client']);
 unset($_SESSION['livraison']);
 unset($_SESSION['ModifierDevis']);
 $user= $_SESSION['user'];
 $Database = new App\Database('devisrecode');
 $Database->DbConnect();
 $Devis = new App\Tables\Devis($Database);
 $devisList = $Devis->getAll();

// Donnée transmise au template : 
echo $twig->render('mesDevis.twig',['user'=>$user,
'user'=> $user,
'devisList'=> $devisList
]);