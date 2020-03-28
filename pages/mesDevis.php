<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
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
 $listOfStatus = $Keyword->getStat();

 if (!empty($_POST['ValiderDevis'])) {
    $Devis->updateStatus($_POST['statusRadio'],$_POST['ValiderDevis']);
 }
 if (!empty($_POST['RefuserDevis'])) {
   $Devis->updateStatus('RFS',$_POST['RefuserDevis']);
}

 $devisList = $Devis->getNotCMD();
 foreach ($devisList as $devis) {
   $devisDate = date_create($devis->devis__date_crea);
   $date = date_format($devisDate, 'd/m/Y');
   $devis->devis__date_crea = $date;
}
// Donnée transmise au template : 
echo $twig->render('mesDevis.twig',['user'=>$user,
'user'=> $user,
'devisList'=> $devisList,
'listOfStatus'=> $listOfStatus
]);