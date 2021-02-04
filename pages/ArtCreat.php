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
$art_modif = $art_creat = FALSE;

$art_creat = TRUE;

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




/*888888 88 888888 88""Yb 888888 
	88   88   88   88__dP 88__   
	88   88   88   88"Yb  88""   
	88   88   88   88  Yb 88888*/
	$P_titre = '
	<main role="main" class="col-md-10 ml-sm-auto col-lg-10 pt-3 px-4">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">';
	if($art_modif)
		$P_titre .= '<h1 class="h2">Modification de Modèle <i class="fad fa-user-edit"></i></h1>';
	if($art_creat)
		$P_titre .= '<h1 class="h2">Création de Modèle <i class="fad fa-robot"></i></h1>';
	$P_titre .= '
	</div>';

	// test de tableau
	// foreach($ArtFamille as $ID => $Line)
	//	print_r($Line->kw__value);


	// Donnée transmise au template : 
	echo $twig->render('ArtUpdateModele.twig',[
	'user'       => $user,
	'action'     => 'Creat',
	'ArtFamille' => $ArtFamille,
	'ArtMarque'  => $ArtMarque,
	'P_titre'    => $P_titre,
	]);
}
