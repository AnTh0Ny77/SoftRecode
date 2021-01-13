<?php
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

//Connexion et Entités : 
$Database = new App\Database('devis');
$Database->DbConnect();
$Commande  = new App\Tables\Cmd($Database);

$gogol = $Commande->test();
$tableau = [];
foreach ($gogol as  $facture) 
{

    
   if (!empty($facture->cmd__nom_devis)) 
   {
       array_push($tableau, $facture );

   }
}



// Donnée transmise au template : 
echo $twig->render('test.twig',
[
   'user'=>$_SESSION['user'],
   'tableau' => $tableau
  
]);;
