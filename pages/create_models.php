
<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Keyword = new \App\Tables\Keyword($Database);
$Cmd = new \App\Tables\Cmd($Database);
$Pistage = new App\Tables\Pistage($Database);
$Article = new App\Tables\Article($Database);
$General = new App\Tables\General($Database);
$Stocks = new App\Tables\Stock($Database);

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) 
{
    header('location: login');
}


if (!empty($_POST['id_models'])) 
{
    $pn = $Article->get_fmm_by_id($_POST['id_models']);
    $forms_data = $Stocks->get_famille_forms($pn->afmm__famille);

    $count = 0;
    foreach ($forms_data as $data) 
    {
        $count += 1;
        if (!empty($_POST[$data->aac__cle])) 
        {
            if ($count == 1) {
                $delete_all_specs = $Stocks->delete_specs_models($pn->afmm__id);
            }
            if (is_array($_POST[$data->aac__cle])) {
                foreach ($_POST[$data->aac__cle] as $value) {
                    if (!empty($value)) {
                        $Stocks->insert_attr_models($_POST['id_models'], $data->aac__cle, $value);
                    }
                }
            } else {
                $Stocks->insert_attr_models($_POST['id_models'], $data->aac__cle, $_POST[$data->aac__cle]);
            }
        }
    }
    header('location: ArtCatalogueModele');
    die();
}

if (!empty($_SESSION['models_id']))
{
       
        $pn_id = $_SESSION['models_id'];
        $_SESSION['models_id'] = "";
        $pn_court = preg_replace("#[^!A-Za-z0-9_%]+#", "", $pn_id);

        $Modele = $Article->get_fmm_by_id($pn_id);

       $spec_array = $Stocks->get_specs_models($pn_id);
    
       
        //data nécéssaire pour la déclaration des attributs :
        $forms_data = $Stocks->get_famille_forms($Modele->afmm__famille);
        // $spec_array = $Stocks->get_specs($pn);

    echo $twig->render(
            'create_models.twig',
            [
                'user' => $_SESSION['user'],
                'pn_id' => $pn_id ,
                'object' => $Modele ,
                'forms_data' => $forms_data ,
                'spec_array' => $spec_array
            ]
            );
}