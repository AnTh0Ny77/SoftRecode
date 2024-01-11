<?php
 session_start();
 require "./App/twigloader.php";

 if (empty($_SESSION['user']->id_utilisateur)) 
 {
    header('location: login');
 }

$Database = new App\Database('devis');
$Database->DbConnect();
$Stats = new App\Tables\Stats($Database);
$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
$_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);

$commandes_valides = $Stats->get_commande_valide();
$devis_envoyes = $Stats->get_devis_quizaine();

// tempo pour modif code sans gener les autres..
if ($_SESSION['user']->id_utilisateur == '56')
{
	echo $twig->render('dashboard_dev.twig', [
	'user'=>$_SESSION['user'],
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

