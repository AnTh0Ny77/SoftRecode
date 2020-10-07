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
 $Cmd = new App\Tables\Cmd($Database);
 $General = new App\Tables\General($Database);


//appel de la page: 
if (!empty($_POST['archiveID']))
{
    
  $valid = $Cmd->getById($_POST['archiveID']);
  $Cmd->updateStatus('NFT' , $valid->devis__id);
  $date = date("Y-m-d H:i:s");
  $updateCmd = $General->updateAll('cmd', $date , 'cmd__date_fact' , 'cmd__id' , $valid->devis__id);
  $_SESSION['archive'] = $valid->devis__id;
  header('location: facture');
  
}



  

      

 
  
