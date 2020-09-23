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
 $Abonnement = new App\Tables\Abonnement($Database);
 
 
 
 $ABNList = $Abonnement->getActifAndFacturable();
 $arrayList = [];
 
 foreach ($ABNList as $abn) 
 {
    $item = $Cmd->GetById($abn->ab__cmd__id);
    $item->devis__note_client = $abn;
    $ligne = $Abonnement->getLigneActives($abn->ab__cmd__id);
    
    
    array_push($arrayList, $item);
 }

 foreach ($arrayList as $devis) 
 {
   $devisDate = date_create($devis->cmd__date_cmd);
   $date = date_format($devisDate, 'd/m/Y');
   $devis->devis__date_crea = $date;
 }
 
  
// Donnée transmise au template : 
echo $twig->render('factureAuto.twig',
[
'user'=>$user,
'devisList'=>$arrayList

]);