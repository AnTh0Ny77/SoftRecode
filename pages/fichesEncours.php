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
 $User = new App\Tables\User($Database);


 $userList = $User->getAll();

 $vueFiltre = null;


//nombre des fiches dans la liste 
if (!empty($_POST['selectUser'])) 
{
  if ($_POST['selectUser'] == 'tous' && empty($_POST['filtre'])) 
  {
    $devisList = $Cmd->getFromStatusCMD();
  } 
  elseif ($_POST['selectUser'] == 'tous' && !empty($_POST['filtre'])) 
  {
    $devisList = $Cmd->magicRequestCMD($_POST['filtre']);
    $vueFiltre = $_POST['filtre'];
  }
  elseif ($_POST['selectUser'] != 'tous' ) 
  {
   $devisList = $Cmd->magicRequestUserCMD( $_POST['filtre'],$_POST['selectUser']);
  }
}
else $devisList = $Cmd->getFromStatusCMD();





$NbDevis = count($devisList);

//formatte la date pour l'utilisateur:
 foreach ($devisList as $devis) 
 {
   $devisDate = date_create($devis->cmd__date_cmd);
   $date = date_format($devisDate, 'Y/m/d');
   $devis->devis__date_crea = $date;

   if (!empty($devis->cmd__date_envoi)) 
   {
    $expeDate = date_create($devis->cmd__date_envoi);
    $expe = date_format($expeDate , 'Y/m/d');
    $devis->cmd__date_envoi = $expe;
   }
   $devis->cmd__note_client = $Cmd->devisLigne($devis->devis__id);
 }

 // Donnée transmise au template : 
echo $twig->render('fichesEnCours.twig',
[
'user'=>$user,
'devisList'=>$devisList,
'NbDevis'=>$NbDevis,
'userList'=>$userList,
'vueFiltre'=>$vueFiltre
]);