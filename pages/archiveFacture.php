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
 $Cmd = new App\Tables\Cmd($Database);
 $General = new App\Tables\General($Database);

 if (!empty($_SESSION['factureMoins'])) 
 {
  $_POST['archiveID'] = $_SESSION['factureMoins'] ;
  $_SESSION['factureMoins'] = '';
 }

//appel de la page: 
if (!empty($_POST['archiveID']))
{
    
  $valid = $Cmd->getById($_POST['archiveID']);
  //  4 activer une alert pour indiquer le bon fonctionnement du logiciel 
  $relique = $Cmd->classicReliquat($_POST['archiveID']);

  // alerte si un reliquat à été facturé : 
  $alertReliquat = $Cmd->alertReliquat($_POST['archiveID']);

  $Cmd->updateStatus('NFT' , $valid->devis__id);
  $date = date("Y-m-d H:i:s");
  $updateCmd = $General->updateAll('cmd', $date , 'cmd__date_fact' , 'cmd__id' , $valid->devis__id);
  $_SESSION['archive'] = $valid->devis__id;
  header('location: facture');
  
}



  

      

 
  
