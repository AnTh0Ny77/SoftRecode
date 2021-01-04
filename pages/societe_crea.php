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
 
 $tva_list = $Keywords->getAllFromParam('tva');

 $alert = false ;
 $alertSuccess = false ;
 

// Donnée transmise au template : 
echo $twig->render('societe_crea.twig',[ 
   'user'=>$user , 
   'alert' => $alert , 
   'alertSucces' => $alertSuccess , 
   'tva_list' => $tva_list
   ]);
}