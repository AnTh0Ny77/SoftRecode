<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
//URL bloqué si pas de connexion :
if (empty($_SESSION['user'])) 
{
    header('location: login');
}
//URL bloqué si pas de droit d'accès :
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
 
 
//si une facturation auto a été demandée :
 if (!empty($_POST['trimestre']) ) 
 {  
    $mois = $_POST['trimestre'];
   
   //recupère les abonnements actif :
   $abonnement_liste = $Abonnement->get_actif_for_fact($mois);
   //tableau qui contiendra les abonnement avec ligne facturable présentes: 
   $abonnement_facturable = [];
   $date = date("".$_POST['anneAuto']."-".$mois."-d H:i:s");
   foreach ($abonnement_liste as $abn) 
   {
     
    $abn->client = $Client->getOne($abn->ab__client__id_fact);
    
    $ligne = $Abonnement->getLigneFacturableAuto($abn->ab__cmd__id , $date);
    
    $abn->nbMachine =  sizeof($ligne);
    $abn->total = 00.00;
      foreach($ligne as $machine)
      {
         $machine->totalTrim =  number_format($machine->abl__prix_mois * $abn->ab__fact_periode , 2 , ',', ' ') ;
         $abn->total += $machine->abl__prix_mois * $abn->ab__fact_periode  ;
      }
    $abn->total = number_format($abn->total , 2 , ',', ' ') ;
    $abn->array = $ligne;
    if (!empty($ligne)) 
      {
         array_push($abonnement_facturable , $abn);
      }
    
   }
   
  

   $text = $_POST['anneAuto'] . '-' . $mois .'-' . '1' ;
   $date = new DateTime($text);
   $date = date_format($date,'m-Y');
   $arrayFacturable = json_encode($abonnement_facturable);
  
    // Donnée transmise au template : 
    echo $twig->render('factureAuto.twig',
    [
    'user'=>$user,
    'ABNList'=>$abonnement_liste, 
    'arrayfacturable'=> $arrayFacturable, 
    'date' => $date
    ]);
  
 }
//sinon redirection : 
 else 
 { 
    header('location:  abonnement'); 
 }