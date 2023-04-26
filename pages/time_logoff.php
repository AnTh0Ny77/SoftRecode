<?php
session_start();
require "./vendor/autoload.php";             // chargement des pages PHP avec use
require "./App/twigloader.php";              // gestion des pages twig
require "./App/Methods/tools_functions.php"; // fonctions Boites à outils 

/*ooooooooooo  o8o                                   ooooo                               .oooooo.    .o88o.  .o88o. 
8'   888   `8  `"'                                   `888'                              d8P'  `Y8b   888 `"  888 `" 
     888      oooo  ooo. .oo.  .oo.    .ooooo.        888          .ooooo.   .oooooooo 888      888 o888oo  o888oo  
     888      `888  `888P"Y88bP"Y88b  d88' `88b       888         d88' `88b 888' `88b  888      888  888     888    
     888       888   888   888   888  888ooo888       888         888   888 888   888  888      888  888     888    
     888       888   888   888   888  888    .o       888       o 888   888 `88bod8P'  `88b    d88'  888     888    
    o888o     o888o o888o o888o o888o `Y8bod8P'      o888ooooood8 `Y8bod8P' `8oooooo.   `Y8bood8P'  o888o   o888o   
                                                                            d"     YD                               
                                                                            "Y88888P*/

// Verif du user (si pas de cnx je simule un faux user)
if (! isset($_SESSION['user']->id_utilisateur))
{ // c'est que le user n'existe pas donc pas de log donc machine autonome
	$_SESSION['user'] = (object)array();
	$_SESSION['user']->id_utilisateur = 999;
	$_SESSION['user']->prenom = '';
	$_SESSION['user']->nom = 'Badgeuse';
	$user_cnx = $_SESSION['user']->id_utilisateur;
}

//declaration des variables diverses : 
$twig_info = $twig_titre = '';
$nb_no_delog = 0;

// dates par default 
$semaine       = array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","vendredi","samedi");
$mois          = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
$date_fr_long  = $semaine[date('w')]." ".date('j')." ".$mois[date('n')]." ".date('Y');
$date_time     = date('Y-m-d H:i:s');
$date          = date('Y-m-d');
$time          = date('H:i:s');
$thm           = date('Hi'); // time heure minutes colés pour conparaison
$jour_sem      = date('N');
$am_pm = 'NO'; // si ce n'est pas l'heure, pas de sortie automatique 
if ($thm >= 1250 AND $thm <= 1310) $am_pm = 'AM'; // le log off du matin doit etre lancé à 13h (donc acepté entre 12:50 et 13:10)
if ($thm >= 1950 AND $thm <= 2010) $am_pm = 'PM'; // le log off du soir doit etre lancé à 20h (donc acepté entre 19:50 et 20:10)
// titre
$twig_titre = 'Sortie Automatique impossible, à executer à 13h ou 20h.';
if ($am_pm == 'AM')
	$twig_titre = 'Sortie Automatique de la pose déjeuné';
if ($am_pm == 'PM')
	$twig_titre = 'Sortie Automatique du soir';



/*8888 Yb  dP 88""Yb 88     88  dP""b8    db    888888 88  dP"Yb  88b 88 .dP"Y8 
88__    YbdP  88__dP 88     88 dP   `"   dPYb     88   88 dP   Yb 88Yb88 `Ybo." 
88""    dPYb  88"""  88  .o 88 Yb       dP__Yb    88   88 Yb   dP 88 Y88 o.`Y8b 
888888 dP  Yb 88     88ood8 88  YboodP dP""""Yb   88   88  YbodP  88  Y8 8bodP*/
/* 
  recherche les elements encore en IN pour les sortir automatiquement 
  en fin de journée ou en millieu de journée (13h00)
  - execution en automatique (via sosuké dans le planificateur de taches) à 13h00 et 20h00.
  - si c'est le matin (AM) je delog avec l'heure logic de fin de matinée.
  - idem pour PM
*/


/*""Yb 888888  dP"Yb  88   88 888888 888888 888888 .dP"Y8 
88__dP 88__   dP   Yb 88   88 88__     88   88__   `Ybo." 
88"Yb  88""   Yb b dP Y8   8P 88""     88   88""   o.`Y8b 
88  Yb 888888  `"YoYo `YbodP' 888888   88   888888 8bodP*/

if ($am_pm == 'AM' OR $am_pm == 'PM') // pour verifier que nous somme bien dans le bon timing et non en 'NO'
{
	$sosuke_host      = "192.168.1.124";
	$sosuke_user      = "remote";
	$sosuke_passe     = "euro0493";
	$sosuke_database  = "devis";
	$_SOSUKE_MYSQLI   = mysqli_connect($sosuke_host, $sosuke_user, $sosuke_passe, $sosuke_database);
	if (!$_SOSUKE_MYSQLI) die('Erreur de connexion a la base (' . mysqli_connect_errno() . ') Serveur -->'.$_SERVER['SERVER_NAME']);
	mysqli_set_charset($_SOSUKE_MYSQLI, "utf8");

	// boule sur les utilisateurs (qui ont un user__time_plan non NULL) 
	$Q_  = "SELECT id_utilisateur, user__time_plan, prenom, nom, tp__am_out, tp__pm_out FROM utilisateur ";
	$Q_ .= "LEFT JOIN time_plan ON utilisateur.user__time_plan = time_plan.tp__name AND time_plan.tp__jour = ".$jour_sem." ";
	$Q_ .= "WHERE user__time_plan IS NOT NULL order by id_utilisateur "; // print $Q_.'<br>';
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	while ($A_  = mysqli_fetch_array($R_, MYSQLI_ASSOC) )
	{ 
		$user_id = $A_['id_utilisateur'];
		// recheche si l'utilisateur est present. 
		$Q_u  = "SELECT tt__time, tt__move FROM time_track WHERE tt__user = ".$user_id." ORDER BY tt__time DESC LIMIT 1";
		$R_u  = mysqli_query($_SOSUKE_MYSQLI, $Q_u);
		$A_u  = mysqli_fetch_array($R_u, MYSQLI_ASSOC);
		$user_last_time   = $A_u['tt__time'];
		$user_last_move   = $A_u['tt__move'];
		if ($user_last_move == 'IN')
		{ // l'utilisateur est present (il a oublié de se débadger) je le débadge a l'heure logic de sortie
			// son heure logique de sortie est : 
			if ($am_pm == 'AM') // je prend l'eure de fin de travail le matin
				$time_out = $date.' '.$A_['tp__am_out'];
			else
				$time_out = $date.' '.$A_['tp__pm_out'];
			// Info
			$twig_info .= 'Sortie auto de USER_ID: '.$user_id.' - '.$time_out.' ( '.$A_['prenom'].' '.$A_['nom'].' )<br>';
			// requette de débadge.
			$Q_out  = "INSERT INTO time_track (tt__user, tt__time, tt__move, tt__info, tt__poste) ";
			$Q_out .= "VALUES ('$user_id', '$time_out', 'OUT_A', '', '(990) ".substr($date_time,2)."') "; // 990 est le user crone automatique
			$R_out  = mysqli_query($_SOSUKE_MYSQLI, $Q_out); // var_dump($Q_);
			$nb_no_delog += 1;
		}
	}
	if ($nb_no_delog == 0)
		$twig_info .= 'Tout le monde était bien sortie. - '.$date_time;
}

   /*    888888 888888 88  dP""b8 88  88    db     dP""b8 888888 
  dPYb   88__   88__   88 dP   `" 88  88   dPYb   dP   `" 88__   
 dP__Yb  88""   88""   88 Yb      888888  dP__Yb  Yb  "88 88""   
dP""""Yb 88     88     88  YboodP 88  88 dP""""Yb  YboodP 88888*/

// Donnée transmise au template : 
echo $twig->render('basic_html.twig',
[
'titre'     => $twig_titre,
'info'      => $twig_info,
]);

// supprime le user si c'est 999 (qui est un faux user)
if ( $_SESSION['user']->id_utilisateur == 999 )
	unset($_SESSION['user']);
