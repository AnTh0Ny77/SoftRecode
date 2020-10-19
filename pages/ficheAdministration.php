<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();


 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) 
 {
    header('location: login');
 }
 if ($_SESSION['user']->user__cmd_acces < 10 ) 
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
 $Pisteur = new App\Tables\Pistage($Database);


 //date du jour:
 $datePist = date("Y-m-d H:i:s");
 

 //Recupere les données transmises : 

 if (!empty($_POST['AdminGarantie'])) 
 {
    
    $cmd = $Cmd->GetById($_POST['AdminGarantie']);
    $lignes = $Cmd->devisLigne($_POST['AdminGarantie']);

    $devisDate = date_create($cmd->devis__date_crea);
    $date = date_format($devisDate, 'd/m/Y');
    $cmd->devis__date_crea = $date;

    $cmdDate = date_create($cmd->cmd__date_cmd);
    $date = date_format($cmdDate, 'd/m/Y');
    $cmd->cmd__date_cmd = $date;

    $Pisteur->addPiste($_SESSION['user']->id_utilisateur , $datePist , $_POST['AdminGarantie'] ,' a cliqué sur administration de fiche de travail');
 }
 


 // si une mise a jour de commentaire ou de code commande a ete effectué: 
 if (!empty($_POST['idAdminFiche']) ) 
 {
    $umpdateCode = $General->updateAll('cmd' , $_POST['codeCommandeClient'] , 'cmd__code_cmd_client' , 'cmd__id' , $_POST['idAdminFiche'] );
    
    $umpdateCommentaire = $General->updateAll('cmd' , $_POST['comInterne'] , 'cmd__note_interne' , 'cmd__id' , $_POST['idAdminFiche'] );
    $cmd = $Cmd->GetById($_POST['idAdminFiche']);
    $lignes = $Cmd->devisLigne($_POST['idAdminFiche']);

    $devisDate = date_create($cmd->devis__date_crea);
    $date = date_format($devisDate, 'd/m/Y');
    $cmd->devis__date_crea = $date;

    $cmdDate = date_create($cmd->cmd__date_cmd);
    $date = date_format($cmdDate, 'd/m/Y');
    $cmd->cmd__date_cmd = $date;

    $Pisteur->addPiste($_SESSION['user']->id_utilisateur , $datePist , $_POST['idAdminFiche'] ,'a mis a jour le code commande ou le commentaire de la Fiche de Travail ');

 }

 //si une mise a jour de livraison a été effectuée: 
 if (!empty($_POST['majIdFiche']) && !empty($_POST['MajLivraison'])) 
 {
    $umpdateLivraion = $General->updateAll('cmd' , $_POST['MajLivraison'] , 'cmd__client__id_livr' , 'cmd__id' , $_POST['majIdFiche'] );
    
    
    $cmd = $Cmd->GetById($_POST['majIdFiche']);
    $lignes = $Cmd->devisLigne($_POST['majIdFiche']);

    $devisDate = date_create($cmd->devis__date_crea);
    $date = date_format($devisDate, 'd/m/Y');
    $cmd->devis__date_crea = $date;

    $cmdDate = date_create($cmd->cmd__date_cmd);
    $date = date_format($cmdDate, 'd/m/Y');
    $cmd->cmd__date_cmd = $date;

    $Pisteur->addPiste($_SESSION['user']->id_utilisateur , $datePist , $_POST['majIdFiche'] ,'a changé l adresse de livraison de la Fiche de Travail');
    
 }



 // Donnée transmise au template : 
 echo $twig->render('ficheAdministration.twig',
 [
 'user'=>$user , 
 'cmd' =>$cmd , 
 'lignes' =>$lignes
 
 ]);
 
 
  
