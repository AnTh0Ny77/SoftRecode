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
 $Contact = new App\Tables\Contact($Database);
 $General = new App\Tables\General($Database);
 
 $keywordList = $Keywords->get2_icon();

 $alert = false ;
 $alertSuccess = false ;
 $alert_modif = false ;
 $modif = false ;
 $preselected = false ;

 if (!empty($_POST['fonctionContact']) && !empty($_POST['sociteContact']) && empty($_POST['modif_id'])) 
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
      //redirection vers la page de consultation : 
      $_SESSION['search_switch'] = $_POST['sociteContact'];
      header('location: search_switch');
     }

     else 
     {
         $alert = true;
     }
 }

 //si une mise a jour a été effectuée :
 if (!empty($_POST['modif_id']) && !empty($_POST['fonctionContact'])) 
 {
   //je met à jour dans sossuke puis totoro 
   $General->updateAll('contact' , $_POST['fonctionContact'] , 'contact__fonction' , 'contact__id' , $_POST['modif_id']);
   $General->updateAll('contact' , $_POST['civiliteContact'] , 'contact__civ' , 'contact__id' , $_POST['modif_id']);
   $General->updateAll('contact' , $_POST['nomContact'] , 'contact__nom' , 'contact__id' , $_POST['modif_id']);
   $General->updateAll('contact' , $_POST['prenomContact'] , 'contact__prenom' , 'contact__id' , $_POST['modif_id']);
   $General->updateAll('contact' , $_POST['telContact'] , 'contact__telephone' , 'contact__id' , $_POST['modif_id']);
   $General->updateAll('contact' , $_POST['faxContact'] , 'contact__fax' , 'contact__id' , $_POST['modif_id']);
   $General->updateAll('contact' , $_POST['mailContact'] , 'contact__email' , 'contact__id' , $_POST['modif_id']);
   
   $Totoro = new App\Totoro('euro');
   $Totoro->DbConnect();
   $ContactTotoro = new App\Tables\ContactTotoro($Totoro);
   $ContactTotoro->updateAll('crm_contact' , $_POST['fonctionContact'] , 'interet_contact' , 'id_contact' , $_POST['modif_id']);
   $ContactTotoro->updateAll('crm_contact' , $_POST['civiliteContact'] , 'civ' , 'id_contact' , $_POST['modif_id']);
   $ContactTotoro->updateAll('crm_contact' , $_POST['nomContact'] , 'nom' , 'id_contact' , $_POST['modif_id']);
   $ContactTotoro->updateAll('crm_contact' , $_POST['prenomContact'] , 'prenom' , 'id_contact' , $_POST['modif_id']);
   $ContactTotoro->updateAll('crm_contact' , $_POST['telContact'] , 'gsm' , 'id_contact' , $_POST['modif_id']);
   $ContactTotoro->updateAll('crm_contact' , $_POST['faxContact'] , 'fax' , 'id_contact' , $_POST['modif_id']);
   $ContactTotoro->updateAll('crm_contact' , $_POST['mailContact'] , 'email' , 'id_contact' , $_POST['modif_id']);

    //redirection vers la page de consultation : 
    $id_societe = $Contact->retrieve_client($_POST['modif_id']);
    
    $_SESSION['search_switch'] = $id_societe->liaison__client__id;
    
    header('location: search_switch');
  
  $alert_modif = true;
   
 }

 if (!empty($_POST['contact_select'])) 
 {
  $modif = $Contact->getOne($_POST['contact_select']);
  
 }

 if (!empty($_POST['hidden_client_2'])) 
 {
  $preselected = $_POST['hidden_client_2'] ;
 }

 $clientList = $Client->getAll();

// Donnée transmise au template : 
echo $twig->render('contactCrea.twig',[ 
    'user'=>$user , 
    'keywordList' => $keywordList, 
    'alert' => $alert , 
    'alertSucces' => $alertSuccess , 
    'clientList' => $clientList , 
    'modif' => $modif ,
    'alert_modif' => $alert_modif,
    'preselected' => $preselected
   ]);
}