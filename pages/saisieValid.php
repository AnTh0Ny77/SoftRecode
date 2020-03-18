<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }

 if (!empty($_POST['saisieLivraison'])) {
    $user = $_SESSION['user'];
    //Connexion et requetes : 
    $Database = new App\Database('devisrecode');
    $Command = new App\Tables\Command($Database);
    $Database->DbConnect();
    $command = $Command->getById($_POST['saisieLivraison']);
    $arrayOfCommandLigne= $Command->commandLigne($_POST['saisieLivraison']);
    $jsonPack = json_encode($arrayOfCommandLigne);
    
    // Donnée transmise au template : 
    echo $twig->render('saisieValid.twig',[
    'user'=> $user,
    'command' => $command,
    'jsonPack' => $jsonPack,
    'arrayLigne'=> $arrayOfCommandLigne
    ]);
   
 }
 else {
    header('location: saisie');
 }




 