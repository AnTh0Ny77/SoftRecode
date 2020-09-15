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


   // Donnée transmise au template : 
    echo $twig->render('garantiesFiches.twig',
    [
    'user'=>$user,
    'cmd'=> $Cmd,
    'lignes'=> $lignes
    ]);
}
else 
{
    header('location: fichesTravail');
}
 
 
 
 
  
