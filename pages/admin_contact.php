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

//Connexion et Entités : 
$Database = new App\Database('devis');
$Client = new App\Tables\Client($Database);
$Keywords = new App\Tables\Keyword($Database);
$Contact = new App\Tables\Contact($Database);
$Article = new App\Tables\Article($Database);
$General = new App\Tables\General($Database);
$Cmd = new App\Tables\Cmd($Database);
$Stats = new App\Tables\Stats($Database);

$Database->DbConnect();
$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
//listes  : 
$modeleList = $Keywords->getModele();

// Donnée transmise au template : 
echo $twig->render('admin_contact.twig',
[
   'user'=>$_SESSION['user'],
   'modeleList' => $modeleList,  
]);;
