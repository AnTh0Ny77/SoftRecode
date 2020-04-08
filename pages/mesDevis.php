<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }
 if ($_SESSION['user']->user__devis_acces < 10 ) {
   header('location: noAccess');
 }
 unset($_SESSION['Contact']);
 unset($_SESSION['Client']);
 unset($_SESSION['livraison']);
 unset($_SESSION['ModifierDevis']);
 $user= $_SESSION['user'];
 $Database = new App\Database('devis');
 $Database->DbConnect();
 $Devis = new App\Tables\Devis($Database);
 $Keyword = new App\Tables\Keyword($Database);
 $Client = new App\Tables\Client($Database);
 $Contact = new \App\Tables\Contact($Database);
 $listOfStatus = $Keyword->getStat();
 $devisList = [];

 // Si un devis a été validé : 
if (!empty($_POST['clientSelect'])) {
   $devisData = json_decode($_POST["dataDevis"]);
   $date = date("Y-m-d H:i:s");
   $client = $Client->getOne($_POST['clientSelect']);
  

   // corrige la notice lié a l'accesion d'un non objet -> :
   $contactId = NULL;
   $livraisonId = NULL;
   $livraisonContact = NULL;
   if (!empty( $_POST['contactSelect'])) {
     $contactId = $_POST['contactSelect'];
     $contact = $Contact->getOne($_POST['contactSelect']);
   }
   if (!empty($_POST['livraisonSelect'])) {
       $livraisonId = $_POST['livraisonSelect'];
   }

   if (!empty($_POST['contact_livraison'])) {
       $livraisonContact = $_POST['contact_livraison'];
   }
   $status = 'ATN';


   if (!empty($_POST['ModifierDevis'])) {
       $devis = $Devis->Modify(
       intval($_POST['ModifierDevis']),
       $date,
       $_SESSION['user']->id_utilisateur,
       $_POST['clientSelect'],
       $livraisonId,
       $_POST['port'],
       $contactId,
       $_POST['globalComClient'],
       $_POST['globalComInt'],
       $status,
       NULL,
       $devisData , 
       $livraisonContact 
     );
   } else {
       $devis = $Devis->insertOne(
           $date,
           $_SESSION['user']->id_utilisateur,
           $_POST['clientSelect'],
           $livraisonId,
           $_POST['port'],
           $contactId,
           $_POST['globalComClient'],
           $_POST['globalComInt'],
           $status,
           NULL,
           $devisData,
           $livraisonContact );
   }}

 if (!empty($_POST['ValiderDevis'])) {
    $Devis->updateStatus($_POST['statusRadio'],$_POST['ValiderDevis']);
 }
 if (!empty($_POST['RefuserDevis'])) {
   $Devis->updateStatus('RFS',$_POST['RefuserDevis']);
}
if ($_SESSION['user']->user__devis_acces < 20 ) {
$devisList = $Devis->getUserDevis($_SESSION['user']->id_utilisateur);
}else {
$devisList = $Devis->getNotCMD();
}

 foreach ($devisList as $devis) {
   $devisDate = date_create($devis->devis__date_crea);
   $date = date_format($devisDate, 'Y/m/d');
   $devis->devis__date_crea = $date;
}
// Donnée transmise au template : 
echo $twig->render('mesDevis.twig',['user'=>$user,
'user'=> $user,
'devisList'=> $devisList,
'listOfStatus'=> $listOfStatus
]);