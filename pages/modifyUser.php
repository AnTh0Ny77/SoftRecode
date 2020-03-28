<?php

require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }

   if (!empty($_POST['modifyUser'])) {
      //Connexion et requetes : 
   $Database = new App\Database('devis');
   $user = $_SESSION['user'];
   $Database->DbConnect();
   $User = new App\Tables\Users($Database);
   $thisUser = $User->getByID($_POST['modifyUser']);
    

    
    // Donnée transmise au template : 
    echo $twig->render('modifyUser.twig',[
    'user'=> $user,
    'thisUser'=> $thisUser
    ]);
   }else {
      header('location: utilisateurs');
   }
   