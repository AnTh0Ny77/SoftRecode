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
 $General = new App\Tables\General($Database);
 $Pisteur = new App\Tables\Pistage($Database);


 $tva_list = $Keywords->getAllFromParam('tva');
 $user_list = $User->getAll();
 $alert = false ;
 $alertSuccess = false ;
 $alertModif = false ;
 $modif = false ;

if (!empty($_POST['hidden_client'])) 
{
   $modif = $Client->getOne($_POST['hidden_client']);
}


 if ( empty($_POST['modif__id']) &&!empty($_POST['nom_societe']) && !empty($_POST['ville']) && !empty($_POST['code_postal'])) 
 {
      $pays = mb_strtoupper($_POST['input_pays'], 'UTF8');
      if ($pays = "FRANCE") {
         $pays = '';
         
      } else {
         $pays = $pays;
      }
      var_dump($pays);
      die();
      $creation_societe = $Client->create_one(
      $_POST['nom_societe'],$_POST['adresse_1'] ,$_POST['adresse_2'] , $_POST['code_postal'] , $_POST['ville'], $_POST['telephone'],
      $_POST['fax'] ,  $_POST['select_tva'] , $_POST['intracom_input'] , $_POST['commentaire_client'] , $_POST['vendeur'], $pays);

      if (!empty($_POST['input_pays'])) 
      {
         $General->updateAll('client' , $_POST['input_pays'] , 'client__pays' , 'client__id' , $creation_societe);
      }

      if (!empty($_POST['config'])) 
      {
         $General->updateAll('client' , $_POST['config'] , 'client__memo_config' , 'client__id' , $creation_societe);
      }
    
      // $creation_totoro = $Client->getOne($creation_societe);
      // $Totoro = new App\Totoro('euro');
      // $Totoro->DbConnect();
      // $ContactTotoro = new App\Tables\ContactTotoro($Totoro);
      // $creation =  $ContactTotoro->insertSociete($creation_totoro);

      $alertSuccess = $creation_societe ;
      $date = date("Y-m-d H:i:s");
      $Pisteur->addPiste($_SESSION['user']->id_utilisateur, $date , $creation_societe , ' création de societe: ' ); 
 }

 if (!empty($_POST['modif__id']) &&!empty($_POST['nom_societe']) && !empty($_POST['ville']) && !empty($_POST['code_postal']) ) 
 {
   //on met dabord à jour dans sossuke : 
   $General->updateAll('client' , $_POST['nom_societe'] , 'client__societe' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['adresse_1'] , 'client__adr1' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['adresse_2'] , 'client__adr2' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['code_postal'] , 'client__cp' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['ville'] , 'client__ville' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['telephone'] , 'client__tel' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['fax'] , 'client__fax' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['select_tva'] , 'client__tva' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['intracom_input'] , 'client__tva_intracom' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['commentaire_client'] , 'client__comment' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['vendeur'] , 'client__id_vendeur' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['input_pays'] , 'client__pays' , 'client__id' , $_POST['modif__id']);
   $General->updateAll('client' , $_POST['config'] , 'client__memo_config' , 'client__id' , $_POST['modif__id']);
   //ensuite totoro : 
   $Totoro = new App\Totoro('euro');
   $Totoro->DbConnect();
   $ContactTotoro = new App\Tables\ContactTotoro($Totoro);
   $ContactTotoro->updateAll('client' , $_POST['nom_societe'] , 'nsoc' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['adresse_1'] , 'adr1' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['adresse_2'] , 'adr2' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['code_postal'] , 'cp' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['ville'] , 'ville' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['telephone'] , 'tel' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['fax'] , 'fax' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['select_tva'] , 'code_tva' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['intracom_input'] , 'tva' , 'id_client' , $_POST['modif__id']);
   $ContactTotoro->updateAll('client' , $_POST['vendeur'] , 'tva' , 'id_vendeur' , $_POST['modif__id']);

   $date = date("Y-m-d H:i:s");
   $Pisteur->addPiste($_SESSION['user']->id_utilisateur, $date , $_POST['modif__id'] , ' modification de societe: ' );
   $alertModif = true;
 }
 

// Donnée transmise au template : 
echo $twig->render('societe_crea.twig',[ 
   'user'=>$user , 
   'alert' => $alert , 
   'alertSucces' => $alertSuccess , 
   'alert_modif' => $alertModif,
   'tva_list' => $tva_list, 
   'user_list' => $user_list,
   'modif' => $modif
   ]);
}