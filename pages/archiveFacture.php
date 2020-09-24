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
 $Cmd = new App\Tables\Cmd($Database);
 


//appel de la page: 
if (!empty($_POST['archiveID']))
{
    
  $valid = $Cmd->getById($_POST['archiveID']);
  $Cmd->updateStatus('NFT' , $valid->devis__id);
  header('location: facture');
  
}



  

      

 
  
