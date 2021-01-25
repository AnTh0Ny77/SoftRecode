<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
require "./App/Methods/tools_functions.php"; // fonctions


//URL bloqué si pas de connexion :
if (empty($_SESSION['user'])) 
  { header('location: login'); }

//URL bloqué si pas de Droits
if ($_SESSION['user']->user__admin_acces < 10 ) 
  { header('location: noAccess'); }

// variables
$choix_grp = TRUE;
$GrpMarque = $GrpModele = $GrpPN = $Cancel = FALSE;

// recuperation des post et get..  0:0, 1:false, 2:false/true, 3:Null, 8:img, 7:SQL, 9:Tab
$GrpMarque = get_post('GrpMarque', 2);
$GrpModele = get_post('GrpModele', 2);
$GrpPN     = get_post('GrpPN', 2);
if ($GrpMarque OR $GrpModele OR $GrpPN) $choix_grp = FALSE;
$Cancel    = get_post('Cancel', 2);

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
