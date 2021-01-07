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
 $User = new App\Tables\User($Database);
 
 $tva_list = $Keywords->getAllFromParam('tva');
 $user_list = $User->getAll();
 $alert = false ;
 $alertSuccess = false ;


 if (!empty($_POST['nom_societe']) && !empty($_POST['ville']) && !empty($_POST['code_postal']) && !empty($_POST['select_tva'])) 
 {
      $creation_societe = $Client->create_one(
      $_POST['nom_societe'],$_POST['adresse_1'] ,$_POST['adresse_2'] , $_POST['code_postal'] , $_POST['ville'], $_POST['telephone'],
      $_POST['fax'] ,  $_POST['select_tva'] , $_POST['intracom_input'] , $_POST['commentaire_client'] , $_POST['vendeur']);

      $creation_totoro = $Client->getOne($creation_societe);
      $Totoro = new App\Totoro('euro');
      $Totoro->DbConnect();
      $ContactTotoro = new App\Tables\ContactTotoro($Totoro);
      $creation =  $ContactTotoro->insertSociete($creation_totoro);

      $alertSuccess = $creation_societe ;
 }
 

// Donnée transmise au template : 
echo $twig->render('societe_crea.twig',[ 
   'user'=>$user , 
   'alert' => $alert , 
   'alertSucces' => $alertSuccess , 
   'tva_list' => $tva_list, 
   'user_list' => $user_list
   ]);
}