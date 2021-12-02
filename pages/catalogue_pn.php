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
$recherche_précédente = false ;

// traitement des variables de sessions 
if (!empty($_GET['config_demande'])) {
    if (empty($_GET['config_model']))
        $_SESSION['config']['model'] = false ;

    if (empty($_GET['config_pn']))
        $_SESSION['config']['pn'] = false; 

    if (empty($_GET['config_neuf']))
        $_SESSION['config']['neuf'] = false;
    
    if (empty($_GET['config_occasion']))
        $_SESSION['config']['occasion'] = false;

    if (empty($_GET['config_reconstruire']))
        $_SESSION['config']['reconstruire'] = false ;
    
    if (empty($_GET['config_hs']))
        $_SESSION['config']['hs'] = false;
    ///////////////////////////////////////////////////
    if (!empty($_GET['config_neuf']))
        $_SESSION['config']['neuf'] = true;
    
    if (!empty($_GET['config_occasion']))
        $_SESSION['config']['occasion'] = true;

    if (!empty($_GET['config_reconstruire'])) 
            $_SESSION['config']['reconstruire'] = true ;
    
    
    if (!empty($_GET['config_hs']))
        $_SESSION['config']['hs'] = true;


    if (!empty($_GET['config_model']))
        $_SESSION['config']['model'] = true;
    
    if (!empty($_GET['config_pn']))
        $_SESSION['config']['pn'] = true; 
}

if (!empty($_GET['config_model']))
        $_SESSION['config']['model'] = true;
    
if (!empty($_GET['config_pn']))
    $_SESSION['config']['pn'] = true; 

if (!empty($_GET['config_reconstruire'])) 
    $_SESSION['config']['reconstruire'] = true ;

if (!empty($_GET['config_neuf']))
    $_SESSION['config']['neuf'] = true;

if (!empty($_GET['config_occasion']))
    $_SESSION['config']['occasion'] = true;

if (!empty($_GET['config_hs']))
    $_SESSION['config']['hs'] = true;

if (!empty($_GET['search']))
    $_GET['art_filtre'] = $_GET['search'];

if (!isset($_SESSION['config']['hs']))
    $_SESSION['config']['hs'] = false ;
if (!isset($_SESSION['config']['occasion']))
    $_SESSION['config']['occasion'] = false;
if (!isset($_SESSION['config']['neuf']))
    $_SESSION['config']['neuf'] = false;
if (!isset($_SESSION['config']['reconstruire']))
    $_SESSION['config']['neuf'] = false;

if (!isset($_SESSION['config']['pn']))
    $_SESSION['config']['pn'] = true;
if (!isset($_SESSION['config']['model']))
    $_SESSION['config']['model'] = true;


if (!isset($_SESSION['config']['search']))
    $_SESSION['config']['search'] = '';

if (isset($_GET['art_filtre']) && empty($_GET['art_filtre'])) {
        $_SESSION['config']['search'] = '';
}
if (empty($_GET['art_filtre']) && !empty($_SESSION['config']['search'])) {
        $_GET['art_filtre'] =  $_SESSION['config']['search'] ;
}

$ArtFiltre ='';
$alert_delete = false ; 
//efface un pn si demandé : 
if (!empty($_POST['retour_pn'])) {
    $Article->delete_pn($_POST['retour_pn']);
    $alert_delete = true ;
}

if (!empty($_POST['recherche_guide'])) 
{
    $_SESSION['config']['search'] = '';
    $forms_data = $Stocks->get_famille_forms($_POST['famille']);
    $pn_list = $Stocks->find_pn_spec( $_POST);
    $model_list = $Stocks->find_model_spec($_POST);
    $return_query = $Stocks->return_forms($_POST);
    $recherche_précédente['famille'] = $_POST['famille'];
    $recherche_précédente['json'] = json_encode($_POST);

    foreach ($return_query as $key => $value) {
        $query_resume .=  $key . ': ' ;
        foreach ($value as $text) {
            $query_resume .= $text . ' - ';
        }
        $query_resume .=  ' | ' ;
    }
}
elseif (!empty($_GET['art_filtre']) && empty($_POST['recherche_guide'])) {
   
    $_SESSION['config']['search'] = $_GET['art_filtre'];
    $ArtFiltre = $_GET['art_filtre'];
    $pn_list = $Stocks->get_pn_list($ArtFiltre);
    $model_list = $Stocks->get_model_list($ArtFiltre);
 }
else{
    $pn_list = $Stocks->get_pn_list('');
    $model_list = $Stocks->get_model_list('');
} 

if (!isset($_SESSION['config'])) {
    $_SESSION['config']= [
        "model" => true,
        "pn" => true ,
        "neuf" => true ,
        "occasion" => true , 
        "hs" => true , 
        "reconstruire" => true
    ];
}

if ($_SESSION['config']['pn'] == false ) 
    $pn_list = [];

if ($_SESSION['config']['model'] == false) 
    $model_list = [];


$Totoro = new App\Totoro('euro');
$Totoro->DbConnect();

$temp_pn = [];
foreach ($pn_list as $key => $pn) 
{
    $pn->specs = $Stocks->get_specs_pn_show($pn->apn__pn);
    $pn->apn__image  = base64_encode($pn->apn__image);
   
    $count_stock = $Stocks->count_from_totoro($Totoro, $pn->apn__pn);
    $date_time = new DateTime($pn->apn__date_modif);
	$date_time = $date_time->format('d/m/Y');
	$pn->apn__date_modif = $date_time ; 
      
    foreach ($count_stock as $count) 
    {
        if (intval($count->id_etat == 1  )) 
            $pn->neuf = $count->ct_etat ;
        if (intval($count->id_etat == 11  )) 
            $pn->occasion = $count->ct_etat;
        if (intval($count->id_etat == 21)) 
            $pn->reconstruire = $count->ct_etat; 
        if (intval($count->id_etat == 22)) 
            $pn->hs = $count->ct_etat; 

    }

    $marqueur = false;


    if (isset($pn->neuf) && $_SESSION['config']['neuf'] == true) {
        if ($marqueur == false) {
            array_push($temp_pn, $pn_list[$key]);
        }
        $marqueur = true;
    }

    if (isset($pn->occasion) && $_SESSION['config']['occasion'] == true) {
        if ($marqueur == false) {
            array_push($temp_pn, $pn_list[$key]);
        }
        $marqueur = true;
    }

    if (isset($pn->hs) && $_SESSION['config']['hs'] == true) {
        if ($marqueur == false) {
            array_push($temp_pn, $pn_list[$key]);
        }
        $marqueur = true;
    }

    if (isset($pn->reconstruire) && $_SESSION['config']['reconstruire'] == true) {
        if ($marqueur == false) {
            array_push($temp_pn, $pn_list[$key]);
        }
        $marqueur = true;
    }

}

$temp = [];
foreach ($model_list as $key => $model) 
{
    $model->specs = $Stocks->get_specs_modele_show($model->afmm__id);
    $model->afmm__image = base64_encode($model->afmm__image);

    $date_model = new DateTime($model->afmm__dt_modif);
    $date_model = $date_model->format('d/m/Y');
    $model->afmm__dt_modif = $date_model;
    
    $count_stock = $Stocks->count_from_totoro($Totoro,$model->afmm__modele);

    foreach ($count_stock as $count) 
    {
        if (intval($count->id_etat == 1  )) 
            $model->neuf = $count->ct_etat ;
        if (intval($count->id_etat == 11  )) 
            $model->occasion = $count->ct_etat;
        if (intval($count->id_etat == 21)) 
            $model->reconstruire = $count->ct_etat; 
        if (intval($count->id_etat == 22)) 
            $model->hs = $count->ct_etat; 
    }

    $marqueur = false ;

    if(isset($model->neuf) && $_SESSION['config']['neuf'] == true){
        if ($marqueur == false) {
            array_push($temp,$model_list[$key]);
        }
        $marqueur = true ;
    }

    if(isset($model->occasion) && $_SESSION['config']['occasion'] == true){
        if ($marqueur == false) {
            array_push($temp,$model_list[$key]);
        }
        $marqueur = true ;
     }

     if(isset($model->hs) && $_SESSION['config']['hs'] == true){
        if ($marqueur == false) {
            array_push($temp,$model_list[$key]);
        }
        $marqueur = true ;
     }

     if(isset($model->reconstruire) && $_SESSION['config']['reconstruire'] == true){
        if ($marqueur == false) {
            array_push($temp,$model_list[$key]);
        }
        $marqueur = true ;
     }
}

if ( $_SESSION['config']['neuf'] == false and $_SESSION['config']['occasion'] == false and $_SESSION['config']['hs'] == false  and $_SESSION['config']['reconstruire'] == false ) 
{
    $model_list = $model_list;
    $pn_list = $pn_list;
}
else {
    $model_list = $temp;
    $pn_list = $temp_pn;
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
    'query_resume' => $query_resume , 
    'recherche_precedente' => $recherche_précédente , 
    'search' => $_SESSION['config']['search'] , 
    'alert_delete' => $alert_delete
]);
