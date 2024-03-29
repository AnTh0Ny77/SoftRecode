<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']->id_utilisateur)) 
 {
    header('location: login');
 }
 if ($_SESSION['user']->user__saisie_acces < 10 ) 
 {
   header('location: noAccess');
 }

 

 //déclaration des instances nécéssaires :
 $user= $_SESSION['user'];
 $Database = new App\Database('devis');
 $Database->DbConnect();
 $Keyword = new App\Tables\Keyword($Database);
 $General = new App\Tables\General($Database);
 $Client = new App\Tables\Client($Database);
 $Contact = new \App\Tables\Contact($Database);
 $Cmd = new App\Tables\Cmd($Database);

//par défault le champ de recherche est égal a null:
 $champRecherche = null;
 
// variable qui determine la liste des devis à afficher:
if (!empty($_POST['recherche-fiche'])) 
{
    switch ($_POST['recherche-fiche']) {
        case 'search':
            if ($_POST['rechercheF'] != "") 
            {
               $devisList = $Cmd->magicRequestStatus($_POST['rechercheF'] , 'CMD');
               $champRecherche = $_POST['rechercheF'];
               break;
            }
            else 
            {
               $devisList = $Cmd->getFromStatusAll('CMD');
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
           $devisList = $Cmd->getFromStatusAll('CMD');
           break;
    }
   
} else $devisList = $Cmd->getFromStatusAll('CMD');

//nombre des fiches dans la liste 
$NbDevis = count($devisList);

//liste des transporteur :
$TransportListe = $Keyword->getTransporteur();

//formatte la date pour l'utilisateur:
 foreach ($devisList as $devis) 
 {
   $devisDate = date_create($devis->cmd__date_cmd);
   $date = date_format($devisDate, 'd/m/Y');
   $devis->devis__date_crea = $date;
   if ($devis->cmd__date_envoi) 
   {
      $envoiDate = date_create($devis->cmd__date_envoi);
      $envoiDate = date_format($envoiDate, 'd/m/Y');
      $devis->cmd__date_envoi = $envoiDate;
   }

   $devis->DataLigne = json_encode($Cmd->devisLigne($devis->devis__id));
  
 }

// Donnée transmise au template : 
echo $twig->render('transport.twig',
[
'user'=>$user,
'devisList'=>$devisList,
'NbDevis'=>$NbDevis,
'champRecherche'=>$champRecherche,
'transporteurs'=>$TransportListe,
]);