<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);


if (empty($_SESSION['user']->id_utilisateur)) 
{
    echo 'Connection failed';
} 
else 
{

    //requete renvoi le pn selectionné de la ligne ainsi qu' un tableau des autre pn dispo pour cet id fmm 
    if (!empty($_POST['ligneID'])) 
    {
        $pnList = json_encode($Article->get_line_pn_and_return_liaison_list($_POST['ligneID']));
        
        echo $pnList;
    } 
    else 
    {
        echo 'request failed';
    }
}
