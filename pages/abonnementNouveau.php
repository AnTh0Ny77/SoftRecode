<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) {
  header('location: login');
}
if ($_SESSION['user']->user__facture_acces  < 10) {
  header('location: noAccess');
}

//déclaration des instances nécéssaires :
$user = $_SESSION['user'];
$Database = new App\Database('devis');
$Database->DbConnect();
$Keyword = new App\Tables\Keyword($Database);
$Client = new App\Tables\Client($Database);
$Contact = new \App\Tables\Contact($Database);
$Cmd = new App\Tables\Cmd($Database);
$General = new App\Tables\General($Database);
$Abonnement = new App\Tables\Abonnement($Database);
$Stats = new App\Tables\Stats($Database);
$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
$_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
$prestaList = $Keyword->getPrestaABN();
$moisList = $Keyword->getGaranties();
$date = date("Y-m-01");
$alert = false;

//si un abonnement a été crée:
if (!empty($_POST['numCmd'])) {
  $valid = $Cmd->getById($_POST['numCmd']);
  if (!empty($valid)) {
    if (!empty($_POST['idClient'])) {
		$client = $Client->getOne($_POST['idClient']);
		if (!empty($client)) {
			$id = $_POST['idClient'];
		} else {
			$id = null;
		}
    }
    $verif = $Abonnement->getById($_POST['numCmd']);
    if (empty($verif)) {
		$abn = $Abonnement->createOne($_POST['numCmd'], $valid->client__id, 1, $_POST['facturationAuto'], $_POST['prestation'], $_POST['comAbn'], $_POST['mois']);
		$updat =  $General->updateAll('abonnement', $_POST['start'], 'ab__date_anniv', 'ab__cmd__id', $_POST['numCmd']);
		header('location: abonnement');
    } else {
      	$alert = true;
    }
  } else {
    	$alert = true;
  }
}

// Donnée transmise au template : 
echo $twig->render(
  'abonnementNouveau.twig',
  [
    'user' => $user,
    'prestaList' => $prestaList,
    'moisList' => $moisList,
    'alert' => $alert,
    'date' => $date



  ]
);
