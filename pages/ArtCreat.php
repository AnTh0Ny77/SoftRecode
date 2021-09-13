<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
require "./App/Methods/tools_functions.php"; // fonctions


//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) 
	{ header('location: login'); }

//URL bloqué si pas de Droits
if ($_SESSION['user']->user__cmd_acces < 10 ) 
	{ header('location: noAccess'); }

// variables
$choix_grp = TRUE; // choix du group (que choisir comme dreation ?)
$GrpMarque = $GrpModele = $GrpPN = $Cancel = $action_modele = FALSE;
$art_modif = FALSE;
$art_creat = TRUE;

// recuperation des post et get..  0:0, 1:false, 2:false/true, 3:Null, 8:img, 7:SQL, 9:Tab
$GrpMarque   = get_post('GrpMarque', 2);
$GrpModele   = get_post('GrpModele', 2);
$GrpPN       = get_post('GrpPN', 2);
$id_fmm      = get_post('id_fmm', 1, 'GETPOST');
if($id_fmm) { $GrpModele = $art_modif= TRUE; $art_creat = FALSE; }
if($GrpMarque OR $GrpModele OR $GrpPN) $choix_grp = FALSE;
$cat_marque = get_post('CatMarque', 2);
$cat_modele = get_post('CatModele', 2);
$cat_pn     = get_post('CatPn', 2);

// redirection sur pages si demande
if ($cat_marque)
	header('location:ArtCatalogueMarque'); 
if ($cat_modele)
	header('location:ArtCatalogueModele'); 
if ($cat_pn)
	header('location:ArtCataloguePN'); 

//Connexion sur la base
$Database = new App\Database('devis');
$user = $_SESSION['user'];
$Database->DbConnect();
$Article = new App\Tables\Article($Database);
$Stats = new App\Tables\Stats($Database);
 $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
$_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);

if ($choix_grp) // pas encore de choix sur le groupe a créer / Modifier (Famille , PN, Marque ...)
{  // Affichage de la page de choix
	echo $twig->render('ArtChoix.twig',[
		'user'       => $user
		]);
}

if ($GrpModele)
{
	//Requetes (récuperation des listes Famille et Marque)
	$ArtFamille = $Article->get_famille_for_spec();
	$ArtMarque  = $Article->getMARQUE();
	$ArtFmm     = $Article->get_fmm_by_id($id_fmm);
	if($art_modif) $action_modele = 'Modif';
	if($art_creat) $action_modele = 'Creat';

	// test de tableau
	//foreach($ArtFmm as $ID => $Line)
	//print_r($ArtFmm->afmm__design_com);

	// Donnée transmise au template : 
	echo $twig->render('ArtUpdateModele.twig',[
	'user'       => $user,
	'action'     => $action_modele,
	'ArtFamille' => $ArtFamille,
	'ArtMarque'  => $ArtMarque,
	'ArtFmm'     => $ArtFmm,
	'id_fmm'     => $id_fmm
	]);
}
