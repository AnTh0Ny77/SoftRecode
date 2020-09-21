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
 if ($_SESSION['user']->user__devis_acces < 10 ) 
 {
   header('location: noAccess');
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
 $Abonnement = new App\Tables\Abonnement($Database);
 
 $prestaList = $Keyword->getPrestaABN();
 $moisList = $Keyword->getGaranties();

 $alert = false;

//si un abonnement a été crée:
if (!empty($_POST['idCmd']))
{
  $valid = $Cmd->getById($_POST['idCmd']);
  
  if (!empty($valid)) 
  {

    
    $verif = $Abonnement->getById($_POST['idCmd']);  
   
  }
  else
  {
    $alert = true;
  }

}
 
 
  
// Donnée transmise au template : 
echo $twig->render('ajoutMachine.twig',
[
'user'=>$user,
'prestaList'=> $prestaList,
'moisList' => $moisList,
'alert' => $alert ,
'cmd' => $valid
]);