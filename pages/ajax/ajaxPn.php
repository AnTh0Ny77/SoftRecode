<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);


if (empty($_SESSION['user']->id_utilisateur)) {
    echo 'utilisateur non connecté ';
 }
 else {

    // requete table client:
        if (!empty($_POST['AjaxPn']))
        {
            // if(intval($_POST['AjaxPn']) == 11 )
            // {
            //     $pnList = json_encode($Article->get_pn_where_not_liaison($_POST['AjaxPn']));
            // }
            // else
            // {
                $pnList = json_encode($Article->get_pn_from_liaison($_POST['AjaxPn']));
            // }
            
            
            echo $pnList;
            
        }
        else {
            echo 'request failed';
        }


 }