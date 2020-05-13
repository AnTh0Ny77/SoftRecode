<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

//URL bloqué si pas de connexion :
if (empty($_SESSION['user'])) 
  { header('location: login'); }

//URL bloqué si pas de Droits
if ($_SESSION['user']->user__admin_acces < 10 ) 
  { header('location: noAccess'); }

// variables
$choix_grp = TRUE;
$GrpMarque = $GrpModele = $GrpPN = $Cancel = FALSE;


// recuperation des post et get..
if (isset($_POST['GrpMarque'])) { $GrpMarque = TRUE; $choix_grp = FALSE; }
if (isset($_POST['GrpModele'])) { $GrpModele = TRUE; $choix_grp = FALSE; }
if (isset($_POST['GrpPN']))     { $GrpPN = TRUE; $choix_grp = FALSE; }
if (isset($_POST['Cancel']))    { $Cancel = TRUE; }

//Connexion sur la base
$Database = new App\Database('devis');
$user = $_SESSION['user'];
$Database->DbConnect();
$Article = new App\Tables\Article($Database);

if ($Cancel)
{ 
  header('location: catalogue'); 
}

if ($choix_grp) // pas encore de choix sur le groupe a créer / Modifier (Famille , PN, Marque ...)
{
  // Donnée transmise au template : 
  echo $twig->render('ArtChoix.twig',[
    'user'       => $user
    ]);
}

if ($GrpModele)
{
  //Requetes (récuperation des listes Famille et Marque)
  $ArtFamille = $Article->getFAMILLE();
  $ArtMarque  = $Article->getMARQUE();

  // Donnée transmise au template : 
  echo $twig->render('ArtUpdateModele.twig',[
  'user'       => $user,
  'action'     => 'Creat',
  'ArtFamille' => $ArtFamille,
  'ArtMarque'  => $ArtMarque 
  ]);
}
