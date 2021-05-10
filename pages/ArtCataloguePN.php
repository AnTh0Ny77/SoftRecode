<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
require "./App/Methods/tools_functions.php"; // fonctions

// Validation de connexion :
if(empty($_SESSION['user']->id_utilisateur)) 
	{ header('location: login'); }

// Validation de droits - si plus petit que 10 pas de droits sur cette page.
if($_SESSION['user']->user__cmd_acces < 10 )
	{ header('location: noAccess'); }

// Récup de variables (Session et post get)
	$user       = $_SESSION['user'];
	$art_filtre = get_post('art_filtre', FALSE, 'GETPOST');
	if(isset($_POST['link_famille'])) $art_filtre = trim($_POST['filtre_famille']);
	if(isset($_POST['link_marque']))  $art_filtre = trim($_POST['filtre_marque']);
	if(isset($_POST['link_modele']))  $art_filtre = trim($_POST['filtre_modele']);
	if(isset($_POST['link_pn']))      $art_filtre = trim($_POST['filtre_pn']);

// variable locale
	$big_sql = $Art_ACC_CON_PID = $ArtACC = $ArtCON = $ArtPID = $ArtModele = FALSE;
	$CountACC = $CountCON = $CountPID = 0;
// Connexion Base de données
	$Database = new App\Database('devis');
	$Database->DbConnect();
	$Article = new App\Tables\Article($Database);
	$Stats = new App\Tables\Stats($Database);
 	$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
// rechreche de prefixe (: ou !) pour savoir si c'est Modele po PN et faire plus de recherche sur les parts...
if(substr($art_filtre,0,1) == ":" OR substr($art_filtre,0,1) == "!") // c'est bien un Modele ou PN en filtre
	$big_sql = TRUE;
// Requetes
	$ArtListe = $Article->getART($art_filtre);
	$CountListe = count($ArtListe);

if (isset($_POST['link_pn'])) // c'est bien un PN que je cherche mais il me faut son Modele pour les accessoir conso, pieces 
	$ArtModele = $ArtListe[0]['Modele']; // il y a forcement 1 seul ligne 
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
echo $twig->render('ArtCataloguePN.twig',[
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
