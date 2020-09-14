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
 $Article = new App\Tables\Article($Database);
 

 
 $prestaList = $Keyword->getPresta();
 
 $tvaList = $Keyword->getAllFromParam('tva');
 
 $devisList = $Cmd->getFromStatusAll('VLD');
 
 
  
// Donnée transmise au template : 
echo $twig->render('abonnement.twig',
[
'user'=>$user,
'devisList'=>$devisList,

'tvaList' => $tvaList,

]);