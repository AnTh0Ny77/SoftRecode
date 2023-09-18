<?php
session_start();
require "./vendor/autoload.php";             // chargement des pages PHP avec use
require "./App/twigloader.php";              // gestion des pages twig
require "./App/Methods/tools_functions.php"; // fonctions Boites à outils 


/*ooo                                                     .        ooooooooo.                                
`888'                                                   .o8        `888   `Y88.                              
 888  ooo. .oo.  .oo.   oo.ooooo.   .ooooo.  oooo d8b .o888oo       888   .d88'  .oooo.   oooo d8b  .ooooo.  
 888  `888P"Y88bP"Y88b   888' `88b d88' `88b `888""8P   888         888ooo88P'  `P  )88b  `888""8P d88' `"Y8 
 888   888   888   888   888   888 888   888  888       888         888          .oP"888   888     888       
 888   888   888   888   888   888 888   888  888       888 .       888         d8(  888   888     888   .o8 
o888o o888o o888o o888o  888bod8P' `Y8bod8P' d888b      "888"      o888o        `Y888""8o d888b    `Y8bod8P' 
						 888                                                                                 
						o888*/

if (empty($_SESSION['user']->id_utilisateur)) header('location: login');

$user_cnx = $_SESSION['user']->id_utilisateur;

//déclaration des instances nécéssaires :
$Database  = new App\Database('devis');
$Database->DbConnect();
$Totoro    = new App\Totoro('euro');
$Totoro->DbConnect();
$UserClass = new App\Tables\User($Database);

//declaration des variables diverses : 
$msg_info = '<font color=red><b>Page en cour de développement</b></font>';
$html     = '';

// recuperations des GET ou POST
$info    = get_post('info', 1);
$btn_ok  = get_post('btn_ok', 2);

// constantes
$tolerance_retard_in = 5; // en minutes

// dates par default 
$semaine       = array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","vendredi","samedi");
$mois          = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
$date_fr_long  = $semaine[date('w')]." ".date('j')." ".$mois[date('n')]." ".date('Y');
$date_time     = date('Y-m-d H:i:s');
$time          = date('H:i:s');
$thm           = date('Hi'); // time heure minutescolé pour conparaison
$jour_sem      = date('N');
$am_pm = 'AM'; if ($thm >= 1300) $am_pm = 'PM';

/*8888 Yb  dP 88""Yb 88     88  dP""b8    db    888888 88  dP"Yb  88b 88 .dP"Y8 
88__    YbdP  88__dP 88     88 dP   `"   dPYb     88   88 dP   Yb 88Yb88 `Ybo." 
88""    dPYb  88"""  88  .o 88 Yb       dP__Yb    88   88 Yb   dP 88 Y88 o.`Y8b 
888888 dP  Yb 88     88ood8 88  YboodP dP""""Yb   88   88  YbodP  88  Y8 8bodP*/
/* 
	Vérification du fichier
	coherence des champs
	integration ligne par ligne dans base
*/

/*""Yb 888888  dP"Yb  88   88 888888 888888 888888 .dP"Y8 
88__dP 88__   dP   Yb 88   88 88__     88   88__   `Ybo." 
88"Yb  88""   Yb b dP Y8   8P 88""     88   88""   o.`Y8b 
88  Yb 888888  `"YoYo `YbodP' 888888   88   888888 8bodP*/

$sosuke_host      = "192.168.1.124";
$sosuke_user      = "remote";
$sosuke_passe     = "euro0493";
$sosuke_database  = "devis";
$_SOSUKE_MYSQLI   = mysqli_connect($sosuke_host, $sosuke_user, $sosuke_passe, $sosuke_database);
if (!$_SOSUKE_MYSQLI) die('Erreur de connexion a la base (' . mysqli_connect_errno() . ') Serveur -->'.$_SERVER['SERVER_NAME']);
mysqli_set_charset($_SOSUKE_MYSQLI, "utf8");

if ($btn_ok)
{
	// // recherche du code PIN dans la table user (si le code pin existe retour du USER_ID si non retour FALSE)
	// $user_id = FALSE;
	// $Q_  = "SELECT * FROM utilisateur WHERE user__time_pin = '".$pin."' OR user__time_rfid = '".$pin."' OR user__time_rfid2 = '".$pin."' ";
	// $R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	// $msg_info = 'Code PIN Non valide';
	// if ($user_cnx == 56)
	// 	$msg_info .= ' -> '.$pin;

	// if (strlen($pin) == 0)
	// 	$msg_info = 'Bonjour, informations de cette page mise à jour à '.$time;
	// if ($R_ !== false AND mysqli_num_rows($R_))
	// {
	// 	$A_               = mysqli_fetch_array($R_, MYSQLI_ASSOC);
	// 	$user_move_id     = $A_['id_utilisateur'];
	// 	$user_move_prenom = $A_['prenom'];
	// 	$nom_user_cnx = '('.$_SESSION['user']->id_utilisateur.') '.substr($_SESSION['user']->prenom,0,1).' '.$_SESSION['user']->nom;
	// 	// recherche de derniere action pour ce user
	// 	$Q_  = "SELECT tt__id, tt__time, tt__move FROM time_track WHERE tt__user = ".$user_move_id." ORDER BY tt__time DESC LIMIT 1";
	// 	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	// 	$A_  = mysqli_fetch_array($R_, MYSQLI_ASSOC);
	// 	$user_last_time   = $A_['tt__time'];
	// 	$user_last_move   = $A_['tt__move'];
	// 	$move             = 'IN'; // par defaut c'est une entrée
	// 	// ecriture des infos
	// 	$info_user = '('.$_SESSION['user']->id_utilisateur.') '.$_SESSION['user']->prenom.' '.$_SESSION['user']->nom;
	// 	$Q_  = "INSERT INTO time_track (tt__user, tt__time, tt__move, tt__info, tt__poste) ";
	// 	$Q_ .= "VALUES ('$user_move_id', '$date_time', '$move', '$info', '$nom_user_cnx') ";
	// 	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_); // var_dump($Q_);
	// 	// Message pour utilisateur
	// 	$msg_info = $msg_info_1.$msg_info_2.$msg_info_3;
	// }
}
// // liste des user concernés par le pointage
// $Q_  = "SELECT id_utilisateur, user__time_plan, user__time_pin, user__time_rfid, user__time_rfid2, prenom, nom, icone FROM utilisateur where user__time_plan IS NOT NULL order by prenom";
// $R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
// while ($A_  = mysqli_fetch_array($R_, MYSQLI_ASSOC) )
// { // boucle sur les user avec user__time_plan non NULL
// 	// recherche de l'etat de la derniere action de chaque user
// 	$user_id         = $A_['id_utilisateur'];
// 	$user_time_plan  = $A_['user__time_plan'];
// 	$user_prenom     = $A_['prenom'];
// 	$user_nom        = $A_['nom'];
// 	$user_icone      = $A_['icone'];
// 	// derniere action
// 	$Q2_  = "SELECT tt__time, tt__move FROM time_track WHERE tt__user = ".$user_id." ORDER BY tt__time DESC LIMIT 1";
// 	$R2_  = mysqli_query($_SOSUKE_MYSQLI, $Q2_);
// 	$A2_  = mysqli_fetch_array($R2_, MYSQLI_ASSOC);
// 	$user_last_dt     = $A2_['tt__time'];
// 	$user_last_move   = $A2_['tt__move'];
// }

   /*    888888 888888 88  dP""b8 88  88    db     dP""b8 888888 
  dPYb   88__   88__   88 dP   `" 88  88   dPYb   dP   `" 88__   
 dP__Yb  88""   88""   88 Yb      888888  dP__Yb  Yb  "88 88""   
dP""""Yb 88     88     88  YboodP 88  88 dP""""Yb  YboodP 88888*/

// Donnée transmise au template : 
echo $twig->render('import_parc.twig',
[
'msg_info'  => $msg_info,
'html'      => $html
]);

