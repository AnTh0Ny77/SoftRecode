<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
require "./App/Methods/tools_functions.php"; // fonctions

//validation Login et droits
if (empty($_SESSION['user']->id_utilisateur))                   header('location: login');
if ($_SESSION['user']->user__admin_acces < 10 ) header('location: noAccess');

// recup variable sess et get post
$user = $_SESSION['user'];
$art_filtre  = get_post('art_filtre', FALSE, 'GETPOST');
if(isset($_POST['link_famille']))  $art_filtre = trim($_POST['filtre_famille']);
if(isset($_POST['link_marque']))   $art_filtre = trim($_POST['filtre_marque']);
if(isset($_POST['link_modele']))   $art_filtre = trim($_POST['filtre_modele']);

// variable locale
$big_sql = $Art_ACC_CON_PID = $ArtACC = $ArtCON = $ArtPID = $ArtModele = FALSE;
$CountACC = $CountCON = $CountPID = 0;
// Connexion Base de données
$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);
$Stats = new App\Tables\Stats($Database);
 $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);

// rechreche de prefixe (:) pour savoir si c'est Modele et faire plus de recherche sur les complements...
if(substr($art_filtre,0,1) == ":") // c'est bien un Modele
{
	$big_sql = TRUE;
	$ArtModele = substr($art_filtre,1);
}
// Requetes
$ArtListe = $Article->get_catalogue_fmm($art_filtre);
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
// sous titre
if ($art_filtre)
	$txt_order = 'Tris par Famille et Modèles';
else
	$txt_order = 'Tris par Date de dernière modification';

// Donnée transmise au template : 
echo $twig->render('ArtCatalogueModele.twig',[
'user'=> $user,
'ArtListe'=> $ArtListe,
'CountListe' => count($ArtListe),
'TxtOrder' => $txt_order,
'ArtACC'=> $ArtACC,
'CountACC' => $CountACC,
'ArtCON'=> $ArtCON,
'CountCON' => $CountCON,
'ArtPID'=> $ArtPID,
'CountPID' => $CountPID,
'ArtFiltre' => $art_filtre
]);
