<?php
require "./vendor/autoload.php";
session_start();
//instanciation des entités nécéssaires au programme:
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);




if (empty($_SESSION['user']->id_utilisateur)) 
{
    header('location: login');
}
//sinon exécution du programme:
else 
{
if (!empty($_POST['AjaxLigneFT']))
{
    //recupere la lingne:
    $ligne = $Cmd->devisLigneUnit($_POST['AjaxLigneFT']);
    echo  json_encode($ligne);
}
else{
    $error = array('error');
    echo json_encode($error , JSON_FORCE_OBJECT);
}
}