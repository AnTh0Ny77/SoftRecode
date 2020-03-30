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

 $user= $_SESSION['user'];
 $Database = new App\Database('devis');
 $Database->DbConnect();
 $User = new App\Tables\Users($Database);
 $userList = $User->getAll();
 

 
// Donnée transmise au template : 
echo $twig->render('utilisateurs.twig',[
'user'=> $user,
'userList'=> $userList

]);