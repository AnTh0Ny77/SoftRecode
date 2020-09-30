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
 if ($_SESSION['user']->user__facture_acces  < 10 ) 
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
    foreach ($lignes as $ligne) 
    {
      $devisDate = date_create($ligne->abl__dt_debut);
      $date = date_format($devisDate, 'd/m/Y');
      $ligne->abl__dt_debut = $date; 
    }
  
}

//si une mise a jour d'abonnement a été effectué : 
if (!empty($_POST['idAbnUpdate'])) 
{

 
  $update = $Abonnement->UpdateAbn($_POST['idAbnUpdate'] , $_POST['actifAbn'] ,  $_POST['facturationAuto'] ,  $_POST['prestationAbn'] ,  $_POST['comAbnG'] ,  $_POST['moisAbn'] );
  $abn = $Abonnement->getById($_POST['idAbnUpdate']);
  $cmd = $Cmd->GetById($abn->ab__cmd__id);
  $lignes = $Abonnement->getLigne($abn->ab__cmd__id);
  foreach ($lignes as $ligne) 
  {
    $devisDate = date_create($ligne->abl__dt_debut);
    $date = date_format($devisDate, 'd/m/Y');
    $ligne->abl__dt_debut = $date; 
  }
  
}

//si une mise à jour de ligne à été effectuée : 
if (!empty($_POST['idCmd']))
{
  $update = $Abonnement->UpdateMachine(
  $_POST['idCmd'],  $_POST['numL'], $_POST['date'], $_POST['actif'], $_POST['fmm'], $_POST['designation'], intval($_POST['sn']), $_POST['prestation'], floatval($_POST['prix']), $_POST['comAbn']);

 
  $abn = $Abonnement->getById($_POST['idCmd']);
  $cmd = $Cmd->GetById($abn->ab__cmd__id);
  $lignes = $Abonnement->getLigne($abn->ab__cmd__id);
  foreach ($lignes as $ligne) 
  {
    $devisDate = date_create($ligne->abl__dt_debut);
    $date = date_format($devisDate, 'd/m/Y');
    $ligne->abl__dt_debut = $date; 
  } 
}


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
 
  
