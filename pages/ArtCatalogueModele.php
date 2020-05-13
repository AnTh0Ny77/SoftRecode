<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

// Validation de connexion :
if(empty($_SESSION['user'])) 
  { header('location: login'); }

// Validation de droits - si plus petit que 10 pas de droits sur cette page.
if($_SESSION['user']->user__admin_acces < 10 )
  { header('location: noAccess'); }

// Variables de session
$user = $_SESSION['user'];
$art_filtre = $_SESSION['art_filtre'];
// variable locale
$big_sql = $Art_ACC_CON_PID = $ArtACC = $ArtCON = $ArtPID = $ArtModele = FALSE;
$CountACC = $CountCON = $CountPID = 0;
// Connexion Base de données
$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);
// recuperation des post et get..
if(isset($_POST['art_filtre']))
  $art_filtre = trim($_POST['art_filtre']);
if(isset($_POST['link_famille']))
  $art_filtre = trim($_POST['filtre_famille']);
if(isset($_POST['link_marque']))
  $art_filtre = trim($_POST['filtre_marque']);
if(isset($_POST['link_modele']))
  $art_filtre = trim($_POST['filtre_modele']);
// rechreche de prefixe (:) pour savoir si c'est Modele et faire plus de recherche sur les complements...
if(substr($art_filtre,0,1) == ":") // c'est bien un Modele
{
  $big_sql = TRUE;
  $ArtModele = substr($art_filtre,1);
}
// Requetes
$ArtListe = $Article->getMODELE($art_filtre);
$CountListe = count($ArtListe);

if($big_sql)
{
  $Art_ACC_CON_PID = $Article->getPARTS($art_filtre, $ArtModele);
  $ArtACC = $Art_ACC_CON_PID['ACC'];
  $ArtCON = $Art_ACC_CON_PID['CON'];
  $ArtPID = $Art_ACC_CON_PID['PID'];
  $CountACC = count($ArtACC);
  $CountCON = count($ArtCON);
  $CountPID = count($ArtPID);
}
// Donnée transmise au template : 
echo $twig->render('ArtCatalogueModele.twig',[
'user'=> $user,
'ArtListe'=> $ArtListe,
'CountListe' => count($ArtListe),
'ArtACC'=> $ArtACC,
'CountACC' => $CountACC,
'ArtCON'=> $ArtCON,
'CountCON' => $CountCON,
'ArtPID'=> $ArtPID,
'CountPID' => $CountPID,
'ArtFiltre' => $art_filtre
]);
