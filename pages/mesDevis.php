<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }

 $user= $_SESSION['user'];
 

// Donnée transmise au template : 
echo $twig->render('mesDevis.twig',['user'=>$user,
'Get'=> $_GET,
]);