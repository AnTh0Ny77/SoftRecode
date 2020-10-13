<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }else{

   if ($_SESSION['user']->user__devis_acces < 10 ) {
      header('location: noAccess');
    }

$user = $_SESSION['user'];
 //connexion et requetes :
 
 $Database = new App\Database('devis');
 $Database->DbConnect();
 $Client = new App\Tables\Client($Database);
 $Keywords = new App\Tables\Keyword($Database);
 
 
 $keywordList = $Keywords->get2_icon();

 $alert = false ;
 $alertSuccess = false ;
 if (!empty($_POST['fonctionContact']) && !empty($_POST['sociteContact'])) 
 {
     $verif = $Client->getOne($_POST['sociteContact']);
     if (!empty($verif)) 
     {
       
      $Database = null;
      $Totoro = new App\Totoro('euro');
      
      $Totoro->DbConnect();
     
      $Database = new App\Database('devis');
      $Database->DbConnect();
      $ContactTotoro = new App\Tables\ContactTotoro($Totoro);
       //questions sur les controles de tout
         $newClientTotoro = $ContactTotoro->insertOne(
         $_POST['fonctionContact'] , $_POST['civiliteContact'],
         $_POST['nomContact'] ,$_POST['prenomContact'], 
         $_POST['telContact'] , "" ,  $_POST['faxContact'], 
         $_POST['mailContact'] , $_POST['sociteContact']);

         $newId = '0000000' . $newClientTotoro ;
         $newId = substr($newId , -6 );
         
         $Contact = new App\Tables\Contact($Database);
         $newLocalContact = $Contact->insertTotoro(
         $newClientTotoro , $_POST['fonctionContact'] , $_POST['civiliteContact'],
         $_POST['nomContact'] ,$_POST['prenomContact'], 
         $_POST['telContact'] , "" , 
         $_POST['mailContact'] , $_POST['sociteContact']);

         $alertSuccess = true;
     }

     else 
     {
         $alert = true;
     }
 }

// Donnée transmise au template : 
echo $twig->render('contactCrea.twig',[ 
   'user'=>$user , 
   'keywordList' => $keywordList, 
   'alert' => $alert , 
   'alertSucces' => $alertSuccess
   ]);
}