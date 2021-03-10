<?php
 session_start();
 require "./App/twigloader.php";
 if (empty($_SESSION['user'])) 
 {
   header('location: login');
 }

 $Database = new App\Database
 ('devis');
 $Database->DbConnect();
 $Stats = new App\Tables\Stats($Database);
 $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);

$commandes_valides = $Stats->get_commande_valide();
$devis_envoyes = $Stats->get_devis_quizaine();


 echo $twig->render('dashboard.twig', [
    'user'=>$_SESSION['user'],
    'commandes_valides' => $commandes_valides,
    'devis_envoyes' => $devis_envoyes
 ]);
