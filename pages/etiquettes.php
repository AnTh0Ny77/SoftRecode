<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) {
    header('location: login');
} else {

    if ($_SESSION['user']->user__devis_acces < 10) {
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
    $Contact = new App\Tables\Contact($Database);
    $Stats = new App\Tables\Stats($Database);
    $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
    $_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
    
    $alert = false;
    $alertSuccess = false;
    

   



    // Donnée transmise au template : 
    echo $twig->render('etiquettes.twig', [
        'user' => $user,
        'alert' => $alert,
        'alertSucces' => $alertSuccess
    ]);
}
