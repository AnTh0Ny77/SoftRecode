<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) 
  { header('location: login'); }

if ($_SESSION['user']->user__admin_acces < 10 ) 
  { header('location: noAccess'); }

//Connexion et requetes : 
$Database = new App\Database('devis');
$user = $_SESSION['user'];
$Database->DbConnect();
$User = new App\Tables\User($Database);
$sujet = $User->getByID(intval($_POST['UserModif']));

// Donnée transmise au template : 
echo $twig->render('UserUpdate.twig',[
'user'=> $user,
'sujet'=> $sujet,
'action'=> 'Modif' ]);
