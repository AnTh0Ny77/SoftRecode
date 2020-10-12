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

 if ($_SESSION['user']->user__facture_acces < 10 ) 
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

   $text = $_POST['anneAuto'] . '-' . $start .'-' . '1' ;
   $textEnd  = $_POST['anneAuto'] . '-' . $end .'-' . '30' ;
   $dateStart = new DateTime($text);
   $dateFin = new DateTime($textEnd);
   $dateStart = $dateStart->format('Y-m-d H:i:s');
   $dateFin = $dateFin->format('Y-m-d H:i:s');

   
  
   foreach ($ABNList as $abn) 
   {
    $abn->client = $Client->getOne($abn->ab__client__id_fact);
    $ligne = $Abonnement->getLigneFacturableAuto($abn->ab__cmd__id ,$dateStart);
    $abn->nbMachine =  sizeof($ligne);
    $abn->total = 00.00;
      foreach($ligne as $machine)
      {
         $machine->totalTrim =  number_format($machine->abl__prix_mois * 3 , 2 , ',', ' ') ;
         $abn->total += $machine->abl__prix_mois * 3  ;
      }
      $abn->total = number_format($abn->total , 2 , ',', ' ') ;
      $abn->array = $ligne;
      
   }
  
 }

$arrayFacturable = json_encode($ABNList);

$dateDebut = new DateTime($text);
$dateEnd = new DateTime($textEnd);
$dateDebut = $dateDebut->format('Y/m/d');
$dateEnd = $dateEnd->format('Y/m/d');
 
// Donnée transmise au template : 
echo $twig->render('factureAuto.twig',
[
'user'=>$user,
'ABNList'=>$ABNList, 
'arrayfacturable'=> $arrayFacturable, 
'dateDebut' => $dateDebut, 
'dateFin' => $dateEnd
]);