<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
use App\Methods\Pdfunctions;
session_start();


 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) 
 {
    header('location: login');
 }
 

 //déclaration des instances nécéssaires :
 $user= $_SESSION['user'];
 $Database = new App\Database('devis');
 $Database->DbConnect();
 $Keyword = new App\Tables\Keyword($Database);
 $Client = new App\Tables\Client($Database);
 $Contact = new \App\Tables\Contact($Database);
 $Cmd = new App\Tables\Cmd($Database);
 $General = new App\Tables\General($Database);
 $Article = new App\Tables\Article($Database);
 

if (!empty($_POST['POSTGarantie'])) 
{
    $cmd = $Cmd->GetById($_POST['POSTGarantie']);
    $lignes = $Cmd->devisLigne($_POST['POSTGarantie']); 
}

//si une mise a jour de commentaire a ete effectuée
if (!empty($_POST['hiddenLigne']) && !empty($_POST['ComInt'])) 
{
    $Cmd->updateComInterneLigne($_POST['ComInt'], intval($_POST['hiddenLigne']));
    $idDevis = $Cmd->returnDevis($_POST['hiddenLigne']);

    

    $cmd = $Cmd->GetById($idDevis->devis__id);
    $lignes = $Cmd->devisLigne($idDevis->devis__id);
}

//si une fiche de garantie a été crée : 

if (!empty($_POST['qteArray'])) 
{
  foreach ($_POST['qteArray'] as $key => $value) 
  {
    if (intval($value) > 0) 
    {
       var_dump($_POST['typeArray'][$key]);
    }
  }
  die();
}
 


 // Donnée transmise au template : 
 echo $twig->render('garantiesFiches.twig',
 [
 'user'=>$user,
 'cmd'=> $cmd,
 'lignes'=> $lignes
 ]);
 
 
  
