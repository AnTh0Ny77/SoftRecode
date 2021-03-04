<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$General = new App\Tables\General($Database);
$Cmd = new App\Tables\Cmd($Database);
$Pistage = new App\Tables\Pistage($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) 
{
        header('location: login');
} 
else 
{
        // requete table client:
        if (!empty($_POST['update']) && !empty($_POST['etat'])) 
        {
               $General->updateAll('cmd', $_POST['etat'] , 'cmd__etat' , 'cmd__id' , $_POST['update']);
               $date = date("Y-m-d H:i:s");
               $Pistage->addPiste($_SESSION['user']->id_utilisateur , $date, $_POST['update'] ,'A changé l état de la fiche '.$_POST['update'].' en '.$_POST['etat'].'');
               $cmd = $Cmd->GetById($_POST['update']);
               echo json_encode($cmd);
        }      
}
