<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
use App\Methods\Pdfunctions;
session_start();


 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']->id_utilisateur)) 
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
 $Article = new App\Tables\Article($Database);
 $Stats = new App\Tables\Stats($Database);
 $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
 $_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
 $moisList = $Keyword->getGaranties();
 $modeleList = $Article->getModels();
 $alert = false;

//appel de la page: 
if (!empty($_POST['idCMD'])){
  $valid = $Cmd->getById($_POST['idCMD']);
  $verif = $Abonnement->getById($_POST['idCMD']); 
  $ligne = $Abonnement->getOneLigne($_POST['idCMD'] ,$_POST['numLigne']);
  if ($verif->ab__presta == 'LOC'){
    $prestaList = $Keyword->getPrestaABL();
  }
  else{
    $prestaList = $Keyword->getPrestaABM();
  }
  // Donnée transmise au template : 
  echo $twig->render('adminMachine.twig',
  [
  'user'=>$user,
  'prestaList'=> $prestaList,
  'moisList' => $moisList,
  'alert' => $alert ,
  'cmd' => $valid, 
  'modeleList' => $modeleList,
  'ligne' => $ligne
  ]);
 
  
}



  

      

 
  
