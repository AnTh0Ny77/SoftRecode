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
if (!empty($_GET['search']))
    $_POST['art_filtre'] = $_GET['search'];

$ArtFiltre ='';
if (!empty($_POST['art_filtre'])) 
{
    $ArtFiltre = $_POST['art_filtre'];
    $pn_list = $Stocks->get_pn_list($ArtFiltre);
    $model_list = $Stocks->get_model_list($ArtFiltre);
}
elseif (!empty($_POST['recherche_guide'])) {
    $forms_data = $Stocks->get_famille_forms($_POST['famille']);
    $pn_list = $Stocks->find_pn_spec( $_POST);
    $model_list = $Stocks->find_model_spec($_POST);
 }
else{
    $pn_list = $Article->select_all_pn();
    $model_list = $Article->select_all_model();
} 

if (!isset($_SESSION['config'])) {
    $_SESSION['config']= [
        "model" => true,
        "pn" => true
    ];
}

if (!empty($_POST['config_model']))
    $_SESSION['config']['model'] = true;

if (!empty($_POST['config_pn']))
    $_SESSION['config']['pn'] = true; 

if (empty($_POST['config_model']))
        $_SESSION['config']['model'] = false ;

if (empty($_POST['config_pn']))
    $_SESSION['config']['pn'] = false; 

if ($_SESSION['config']['pn'] == false ) 
    $pn_list = [];

if ($_SESSION['config']['model'] == false) 
    $model_list = [];





foreach ($pn_list as $pn) 
{
   
    $pn->specs = $Stocks->get_specs_pn_show($pn->apn__pn);
    $pn->apn__image  = base64_encode($pn->apn__image);
}

foreach ($model_list as $model) 
{
    $model->specs = $Stocks->get_specs_modele_show($model->afmm__id);
}

$results_pn = count($pn_list);
$results_model = count($model_list);
$total_results = $results_pn + $results_model ;
// Donnée transmise au template : 
echo $twig->render('ArtCataloguePN.twig',
[
    'user'=> $_SESSION['user'],
    'pn_list' => $pn_list,
    'model_list' => $model_list,
    'art_filtre' =>  $ArtFiltre,
    'results' => $results_pn,
    'results_model' => $results_model ,
    'total' => $total_results, 
    'config' => $_SESSION['config']
]);
