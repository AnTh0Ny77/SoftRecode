<?php

use App\Tables\Stats;

require "./vendor/autoload.php";
require "./App/twigloader.php";

session_start();


 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) 
 {
    header('location: login');
 }
 if ($_SESSION['user']->user__cmd_acces < 10 ) 
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
 $UserClass = new App\Tables\User($Database);
 $Pisteur = new App\Tables\Pistage($Database);
 $Stat = new App\Tables\Stats($Database);
 


$clientList = $Client->getAll();
$articleTypeList = $Article->getModels();
$vendeurList = $UserClass->getCommerciaux();

$alertDate = false;
$resultHt = false ;
$NombreCmd = false;
$client= 'Tous';
$vendeur= 'Tous';

//traitement requetes: 

if (empty($_POST['dateDebut']) && empty($_POST['dateFin'])) 
{
    $date = new DateTime();
    $dateDeb = $date->format('Y-m-01');
    $dateFin = $date->format('Y-m-t');

    $_POST['dateDebut'] = $dateDeb;
    $_POST['dateFin'] = $dateFin;
    $_POST['client'] = 'Tous';
    $_POST['vendeur'] = 'Tous';
}

if (!empty($_POST['dateDebut']) && !empty($_POST['dateFin'])) 
{

 
  $dateDebut = date($_POST['dateDebut'].' H:i:s');
  $dateFin = date($_POST['dateFin'].' H:i:s');

  if ($_POST['client'] != 'Tous' || $_POST['vendeur'] != 'Tous') 
  {

        //recupere les infos pour affichage des client et vendeurs 
        if ($_POST['client'] != 'Tous') 
        {
          $client = $Client->getOne($_POST['client']);
        }
        else
        {
          $client = 'Tous';
        }

        if ($_POST['vendeur'] != 'Tous') 
        {
          $vendeur = $UserClass->getByID($_POST['vendeur']);
        
        }
        else
        {
          $vendeur = 'Tous';
        }

    $cmdList = $Stat->returnCmdBetween2DatesClientVendeur($dateDebut, $dateFin, $_POST['client'] , $_POST['vendeur']);
    
  }
  else 
  {
    $cmdList = $Stat->returnCmdBetween2Dates($dateDebut, $dateFin);
    
  }

  
  $arrayResults = [];

  //si les dates corespondent et que le résultats n'est pas vide : 
  if (!empty($cmdList)) 
  {
    
    foreach ($cmdList as $cmd ) 
    {
      $results= $Stat->WLstatsGlobal($cmd->cmd__id);
      foreach ($results as $ligne) 
      {
        $total = floatval($ligne->ht) * intval($ligne->qte);
        if (!empty($ligne->htg)) 
        {
          $htg = floatval($ligne->htg) * intval($ligne->qte);
          $total = $total + $htg;
        }

        array_push($arrayResults , $total );
      }
    }

  $resultHt = array_sum($arrayResults);
  $resultHt  = number_format($resultHt , 2,',', ' ');
  $NombreCmd = count($cmdList);


  }
  else 
  {
    $alertDate = true;
  }


  
  

}

$dateFormatdebut = new DateTime($_POST['dateDebut']);
$dateFormatdebut = $dateFormatdebut->format('d/m/Y');
$dateFormatFin = new DateTime($_POST['dateFin']);
$dateFormatFin = $dateFormatFin->format('d/m/Y');

 

// Donnée transmise au template : 
echo $twig->render('statistique.twig',
[
'user' => $user,
'vendeurList' => $vendeurList, 
'clientList' =>$clientList , 
'articleList' => $articleTypeList,
'alertDate' => $alertDate ,
'resultHt' => $resultHt,
'NombreCmd' => $NombreCmd , 
'DateDeb' => $_POST['dateDebut'],
'DateFin' => $_POST['dateFin'] , 
'formatDebut' => $dateFormatdebut,
'formatFin' => $dateFormatFin , 
'clientSelect' =>  $client,
'vendeurSelect' => $vendeur
]);
 
 
  
