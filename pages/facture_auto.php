<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) 
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
 $Stats = new App\Tables\Stats($Database);
 $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
 $_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
 
 
//si une facturation auto a été demandée :
 if (!empty($_POST['trimestre']) ) 
 {  
    $mois = $_POST['trimestre'];
   
   //recupère les abonnements actif :
   $abonnement_liste = $Abonnement->get_actif_for_fact($mois);

   //tableau qui contiendra les abonnement avec ligne facturable présentes: 
   $abonnement_facturable = [];
   $array_premiere_echeance = [];
   $date = date("".$_POST['anneAuto']."-".$mois."-d H:i:s");
   foreach ($abonnement_liste as $key => $abn) 
   {

      $effectiveDate = strtotime("+".$abn->ab__fact_periode." months", strtotime($abn->ab__date_anniv));
      $effectiveDate = date('Y-m-d',$effectiveDate);
      $automateDate =  date("".$_POST['anneAuto']."-".$mois."-d ");

      if (strtotime($effectiveDate) >= strtotime($automateDate)) 
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
         $abn->ab__date_anniv = $dateFact->format('d/m/Y'); 
         array_push($array_premiere_echeance , $abn);
         unset($abonnement_liste[$key]);
      }
      else{
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
   
         //formatte les dates pour la liste en visu  
         $dateFact = new DateTime( $abn->ab__date_anniv);
         $abn->ab__date_anniv = $dateFact->format('d/m/Y'); 
      }
   }
   
   
   
   $text = $_POST['anneAuto'] . '-' . $mois .'-' . '1' ;
   $date = new DateTime($text);
   $date = date_format($date,'m-Y');
   $arrayFacturable = json_encode($abonnement_facturable);
   $count_list = count($abonnement_liste);
  
    // Donnée transmise au template : 
    echo $twig->render('factureAuto.twig',
    [
    'user'=>$user,
    'ABNList'=>$abonnement_liste, 
    'arrayfacturable'=> $arrayFacturable, 
    'date' => $date ,
    'premiere_echeance' => $array_premiere_echeance,
    'count_list' => $count_list
    ]);
  
 }
//sinon redirection : 
 else 
 { 
    header('location:  abonnement'); 
 }