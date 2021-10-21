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
$query_resume = false ;

// traitement des variables de sessions 
if (!empty($_GET['config_demande'])) {
    if (empty($_GET['config_model']))
        $_SESSION['config']['model'] = false ;

    if (empty($_GET['config_pn']))
        $_SESSION['config']['pn'] = false; 

    if (!empty($_GET['config_model']))
        $_SESSION['config']['model'] = true;
    
    if (!empty($_GET['config_pn']))
        $_SESSION['config']['pn'] = true; 
}

if (!empty($_GET['config_model']))
        $_SESSION['config']['model'] = true;
    
if (!empty($_GET['config_pn']))
    $_SESSION['config']['pn'] = true; 

if (!empty($_GET['search']))
    $_GET['art_filtre'] = $_GET['search'];

$ArtFiltre ='';
if (!empty($_GET['art_filtre'])) 
{
    $ArtFiltre = $_GET['art_filtre'];
    $pn_list = $Stocks->get_pn_list($ArtFiltre);
    $model_list = $Stocks->get_model_list($ArtFiltre);
}
elseif (!empty($_POST['recherche_guide'])) {
    $forms_data = $Stocks->get_famille_forms($_POST['famille']);
    $pn_list = $Stocks->find_pn_spec( $_POST);
    $model_list = $Stocks->find_model_spec($_POST);
    $return_query = $Stocks->return_forms($_POST);
    
    foreach ($return_query as $key => $value) {
        $query_resume .=  $key . ': ' ;
        foreach ($value as $text) {
            $query_resume .= $text . ' - ';
        }
        $query_resume .=  ' | ' ;
    }
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

if ($_SESSION['config']['pn'] == false ) 
    $pn_list = [];

if ($_SESSION['config']['model'] == false) 
    $model_list = [];


$Totoro = new App\Totoro('euro');
$Totoro->DbConnect();


foreach ($pn_list as $pn) 
{
   
    $pn->specs = $Stocks->get_specs_pn_show($pn->apn__pn);
    $pn->apn__image  = base64_encode($pn->apn__image);
    $count_stock = $Stocks->count_from_totoro($Totoro, $pn->apn__pn);
    foreach ($count_stock as $count) 
    {
        if (intval($count->id_etat == 1  )) 
            $pn->neuf = $count->ct_etat ;
        if (intval($count->id_etat == 11  )) 
            $pn->occasion = $count->ct_etat;
        if (intval($count->id_etat == 21)) 
            $pn->hs = $count->ct_etat; 
    }
}


foreach ($model_list as $model) 
{
    $model->specs = $Stocks->get_specs_modele_show($model->afmm__id);
    $model->afmm__image = base64_encode($model->afmm__image);
    $count_stock = $Stocks->count_from_totoro($Totoro,$model->afmm__modele);

    foreach ($count_stock as $count) 
    {
        if (intval($count->id_etat == 1  )) 
            $model->neuf = $count->ct_etat ;
        if (intval($count->id_etat == 11  )) 
            $model->occasion = $count->ct_etat;
        if (intval($count->id_etat == 21)) 
            $model->hs = $count->ct_etat; 
    }
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
    'config' => $_SESSION['config'],
    'query_resume' => $query_resume
]);
