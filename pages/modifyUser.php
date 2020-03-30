<?php

require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }
 if ($_SESSION['user']->user__admin_acces < 10 ) {
   header('location: noAccess');
 }

   if (!empty($_POST['modifyUser'])) {
      //Connexion et requetes : 
   $Database = new App\Database('devis');
   $user = $_SESSION['user'];
   $Database->DbConnect();
   $User = new App\Tables\Users($Database);
   $sujet = $User->getByID(intval($_POST['modifyUser']));
    

    
    // Donnée transmise au template : 
    echo $twig->render('modifyUser.twig',[
    'user'=> $user,
    'sujet'=> $sujet
    ]);
   }else {
      header('location: utilisateurs');
   }
   