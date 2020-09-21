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

//traite le post avec l'id abonnement: 

if (!empty($_POST['hiddenId'])) 
{

    $abn = $Abonnement->getById($_POST['hiddenId']);
    $cmd = $Cmd->GetById($abn->ab__cmd__id);
    $lignes = $Abonnement->getLigne($abn->ab__cmd__id);

   // Donnée transmise au template : 
    echo $twig->render('abonnementAdmin.twig',
    [
    'user'=>$user,
    'prestaList'=> $prestaList,
    'moisList' => $moisList,
    'cmd'=> $cmd, 
    'abn'=> $abn,
    'lignes' => $lignes
]);
}
else 
{
    header('location: abonnement');
}

 
 
  
