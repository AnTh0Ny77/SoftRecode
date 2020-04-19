<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
unset($_SESSION['ModifierDevis']);
 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }else{

   if ($_SESSION['user']->user__cmd_acces < 10 ) {
      header('location: noAccess');
    }
 //connexion et requetes :
 $Database = new App\Database('devis');
 $Devis = new App\Tables\Cmd($Database);
 $Database->DbConnect();
 $user= $_SESSION['user'];
 $devisList = $Devis->getFromStatus();
 foreach ($devisList as $devis) {
   $devisDate = date_create($devis->devis__date_crea);
   $date = date_format($devisDate, 'd/m/Y');
   $devis->devis__date_crea = $date;
}

// Donnée transmise au template : 
echo $twig->render('home.twig',[ 
   'user'=>$user , 
   'devisList'=> $devisList
   ]);
}