<?php
 session_start();
 use App\Apiservice\ApiTest;
 require "./App/twigloader.php";

 if (empty($_SESSION['user']->id_utilisateur)) 
 {
    header('location: login');
 }

$Database = new App\Database('devis');
$Database->DbConnect();
$Api = new ApiTest();
$token =  $Api->handleSessionToken2();
$user = $Api->getMyRecodeUser($token);
$Stats = new App\Tables\Stats($Database);
$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
$_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);

$commandes_valides = $Stats->get_commande_valide();
$devis_envoyes = $Stats->get_devis_quizaine();

$planning = json_decode($Api->getPlanning( $token),true);

$planning = $planning['data'];

// tempo pour modif code sans gener les autres..
if ($_SESSION['user']->id_utilisateur == '56' or  $_SESSION['user']->id_utilisateur == '11')
{
	echo $twig->render('dashboard_dev.twig', [
	'user'=>$_SESSION['user'],
	'planning' => json_encode($planning) ,
	'commandes_valides' => $commandes_valides,
	'devis_envoyes' => $devis_envoyes
]);
}

else
{
echo $twig->render('dashboard.twig', [
	'user'=>$_SESSION['user'],
	'commandes_valides' => $commandes_valides,
	'devis_envoyes' => $devis_envoyes
]);
}

