<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Stats($Database);
$Users = new \App\Tables\User($Database);


if (empty($_SESSION['user']->id_utilisateur)) {
    header('location: dashboard');
 }
 else {


    if (!empty($_POST['COM'])) 
    {
    $users = $Users->getCommerciaux();
    $response = [];
    
    foreach ($users as $user) {
       $devisATN = $Cmd->devisATN($user->id_utilisateur);
       $devisVLD = $Cmd->devisVLD($user->id_utilisateur);
       $devisAll = $Cmd->devisAll($user->id_utilisateur);
       if (intval($devisATN) > 0 ) {
           $res =  new stdClass;
           $res->id = $user->id_utilisateur;
           $res->nom = $user->log_nec;
           $res->ATN = $devisATN;
           $res->ALL = $devisAll;
           $res->VLD = $devisVLD;
           $res->Total = intval($devisVLD + intval($devisATN));
           array_push($response , $res);
       }
    
    echo json_encode($response);
    }
    
      
    }


    



}