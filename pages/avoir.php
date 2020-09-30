<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
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
 $General = new App\Tables\General($Database);
 $Article = new App\Tables\Article($Database);
 

 $articleTypeList = $Article->getModels();
 $prestaList = $Keyword->getPresta();
 $keywordList = $Keyword->get2_icon();
 $tvaList = $Keyword->getAllFromParam('tva');
 $garantiesList = $Keyword->getGaranties();
 $etatList = $Keyword->getEtat();

 

 
 
//par défault le champ de recherche est égal a null:
 $champRecherche = null;
 
// variable qui determine la liste des devis à afficher:
if (!empty($_POST['recherche-fiche'])) 
{
    switch ($_POST['recherche-fiche']) {
        case 'search':
            if ($_POST['rechercheF'] != "") 
            {
               $devisList = $Cmd->magicRequestStatus($_POST['rechercheF'] , 'VLD');
               $champRecherche = $_POST['rechercheF'];
               break;
            }
            else 
            {
               $devisList = $Cmd->getFromStatusAll('VLD');
               $champRecherche = $_POST['rechercheF'];
               break;
            }
           
        case 'id-fiche':
           $devisList = [];
           $devisSeul = $Cmd->GetById(intval($_POST['rechercheF']));
           $champRecherche = $_POST['rechercheF'];
           array_push($devisList, $devisSeul);
           break;
        
        default:
           $devisList = $Cmd->getFromStatusAll('VLD');
           break;
    }
   
} else $devisList = $Cmd->getFromStatusAll('VLD');

//nombre des fiches dans la liste 
$NbDevis = count($devisList);

//liste des transporteur :
$TransportListe = $Keyword->getTransporteur();

//formatte la date pour l'utilisateur:
 foreach ($devisList as $devis) 
 {
   $devisDate = date_create($devis->cmd__date_fact);
   $date = date_format($devisDate, 'd/m/Y');
   $devis->cmd__date_fact = $date;
   

   $devis->DataLigne = json_encode($Cmd->devisLigne($devis->devis__id));
  
 }



 //si un Avoir global à été posté 
 if (!empty($_POST['makeAvoir'])) 
 {
     $facture = $Cmd->GetById($_POST['makeAvoir']);
     $avoir = $Cmd->makeAvoir($facture);
     $newFacture = $Cmd->commande2facture($avoir);
     $General->updateAll('cmd' , 'AVR', 'cmd__modele_facture', 'cmd__id' , $avoir );
     $linesHoldFact = $Cmd->devisLigne($_POST['makeAvoir']);

    foreach ($_POST['lines'] as $key => $value) 
    {
       if (!empty($value)) 
       {
          $avoirLigne = $Cmd->makeAvoirLigne($key , $avoir , $value);
          $newLines = $Cmd->devisLigneId($key);
          $Cmd->reversePrice($key);
       }
    }

    header('location: avoir');

 }

 
  
// Donnée transmise au template : 
echo $twig->render('avoir.twig',
[
'user'=>$user,
'devisList'=>$devisList,
'NbDevis'=>$NbDevis,
'champRecherche'=>$champRecherche,
'transporteurs'=>$TransportListe,
'keywordList' => $keywordList,
'tvaList' => $tvaList,

]);