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

$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);
$Stocks = new App\Tables\Stock($Database);
// Récup de variables (Session et post get)

$pn_list = $Article->select_all_pn();

foreach ($pn_list as $pn) 
{
    $pn->specs = $Stocks->get_specs_value($pn->apn__pn);
    $pn->apn__image  = base64_encode($pn->apn__image);
}

// Donnée transmise au template : 
echo $twig->render('ArtCataloguePN.twig',
[
    'user'=> $_SESSION['user'],
    'pn_list' => $pn_list
]);
