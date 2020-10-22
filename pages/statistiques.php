<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";

session_start();


 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) 
 {
    header('location: login');
 }
 if ($_SESSION['user']->user__cmd_acces < 10 ) 
 {
   header('location: noAccess');
 }

 //déclaration des instances nécéssaires :
 $user= $_SESSION['user'];
 $Database = new App\Database('devis');
 $Database->DbConnect();
 $Keyword = new App\Tables\Keyword($Database);
 $Client = new App\Tables\Client($Database);
 $Contact = new \App\Tables\Contact($Database);
 $Cmd = new App\Tables\Cmd($Database);
 $General = new App\Tables\General($Database);
 $Article = new App\Tables\Article($Database);
 $UserClass = new App\Tables\User($Database);
 $Pisteur = new App\Tables\Pistage($Database);
 


$clientList = $Client->getAll();
$articleTypeList = $Article->getModels();
$vendeurList = $UserClass->getCommerciaux();

 

// Donnée transmise au template : 
echo $twig->render('statistique.twig',
[
'user' => $user,
'vendeurList' => $vendeurList, 
'clientList' =>$clientList , 
'articleList' => $articleTypeList,
]);
 
 
  
