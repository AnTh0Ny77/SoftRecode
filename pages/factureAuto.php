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
 
 
 
 


 if (!empty($_POST['trimestre']) && !empty($_POST['anneAuto'])) 
 {

   $ABNList = $Abonnement->getActifAndFacturable();
   $arrayList = [];

   switch ($_POST['trimestre']) 
   {
      case '1':
         $start = 1 ; 
         $end = 3;
         break;

      case '2':
         $start = 4; 
         $end = 6;
         break;

      case '3':
         $start = 7; 
         $end = 9;
         break;

      case '4':
         $start = 10; 
         $end = 12;
         break;
   }

   $dateStart = new DateTime();
   $dateFin = new DateTime();
   
   $dateStart->setDate(intval($_POST['anneAuto']) , $start , 1);
   $dateFin ->setDate(intval($_POST['anneAuto']) , $end , 30);
   var_dump($dateStart);
   $arrayMachine = [];

   foreach ($ABNList as $abn) 
   {
    $ligne = $Abonnement->getLigneFacturableAuto($abn->ab__cmd__id ,$dateStart->date);
    $ligneProRata = $Abonnement->getLigneFacturableAutoBetween2Dates($abn->ab__cmd__id ,$dateStart->date , $dateFin->date);
    die();
    //défini le prorata : 
      foreach ($ligneProRata as $item) 
      {
         $test = $Abonnement->DiffDate($item->abl__dt_debut , $dateFin->date);
         var_dump($test);
      }
    array_push($arrayMachine, $ligne);
   }
   
 }

 
 
 


 

 
  
// Donnée transmise au template : 
echo $twig->render('factureAuto.twig',
[
'user'=>$user,
'devisList'=>$arrayList

]);