<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }else{
$user = $_SESSION['user'];

if ($_SESSION['user']->user__cmd_acces < 10 ) {
    header('location: noAccess');
  }

//Connexion et requetes : 
$Database = new App\Database('devis');
$Database->DbConnect();
$Devis = new \App\Tables\Cmd($Database);



// si une validation de devis a été effectuée : 
if(!empty($_POST['devisCommande'])){
    $date = date("Y-m-d H:i:s");
    $Devis->updateStatus('CMD',$_POST['devisCommande']);
    $Devis->updateDate('cmd__date_cmd' , $date , $_POST['devisCommande'] );
    $Devis->updateAuthor('cmd__user__id_cmd' , $_SESSION['user']->id_utilisateur , $_POST['devisCommande'] );
    header('Location: commandCours');
}
// listes de commandes : 
$listOfCommand = $Devis->getFromStatusCMD();

foreach ($listOfCommand as $command) {
   $commandDate = date_create($command->cmd__date_cmd);
   $date = date_format($commandDate, 'd/m/Y');
   $command->cmd__date_cmd = $date;
}

// Donnée transmise au template : 
echo $twig->render('commandCours.twig',[
    "user"=> $user, 
    'listOfCommand' => $listOfCommand
]);


 }