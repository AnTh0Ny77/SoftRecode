<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) {
        header('location: login');
}
if ($_SESSION['user']->user__admin_acces < 50) {
        header('location: noAccess');
}

//Connexion et Entités : 
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new App\Tables\Client($Database);
$Keywords = new App\Tables\Keyword($Database);
$Article = new App\Tables\Article($Database);
$General = new App\Tables\General($Database);
$User = new \App\Tables\User($Database);
$Pistage = new App\Tables\Pistage($Database);

$Cmd = new App\Tables\Cmd($Database);


$userList =$User->getAll();

$liste_piste = $Pistage->get_last_pistes();



// Donnée transmise au template : 
echo $twig->render(
        'action_utilisateur.twig',
        [
                'user' => $_SESSION['user'],
                'user_list' => $userList,
                'liste_piste' => $liste_piste
        ]
);
