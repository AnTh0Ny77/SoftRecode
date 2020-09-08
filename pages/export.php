<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();


 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) 
 {
    header('location: login');
 }
 if ($_SESSION['user']->user__devis_acces < 10 ) 
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
 $marqueur = $Keyword->getExport();

 $marqueur = intval($marqueur->kw__lib);
 

 
 
 $devisList = $Cmd->getFromStatusAll('VLD');
 //formatte la date pour l'utilisateur:
    foreach ($devisList as $devis) 
    {
      $devisDate = date_create($devis->cmd__date_fact);
      $date = date_format($devisDate, 'd/m/Y');
      $devis->cmd__date_fact = $date;
    }

 
  
// Donnée transmise au template : 
echo $twig->render('export.twig',
[
'user'=>$user,
'devisList'=>$devisList,
'marqueur'=>$marqueur,
'tvaList' => $tvaList,
]);