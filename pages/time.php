<?php
session_start();
require "./vendor/autoload.php";             // chargement des pages PHP avec use
require "./App/twigloader.php";              // gestion des pages twig
require "./App/Methods/tools_functions.php"; // fonctions Boites à outils 


/*ooooooooooo                              oooo        ooooooooooooo  o8o                              
8'   888   `8                              `888        8'   888   `8  `"'                              
     888      oooo d8b  .oooo.    .ooooo.   888  oooo       888      oooo  ooo. .oo.  .oo.    .ooooo.  
     888      `888""8P `P  )88b  d88' `"Y8  888 .8P'        888      `888  `888P"Y88bP"Y88b  d88' `88b 
     888       888      .oP"888  888        888888.         888       888   888   888   888  888ooo888 
     888       888     d8(  888  888   .o8  888 `88b.       888       888   888   888   888  888    .o 
    o888o     d888b    `Y888""8o `Y8bod8P' o888o o888o     o888o     o888o o888o o888o o888o `Y8bod8P*/ 

// if (empty($_SESSION['user']->id_utilisateur)) header('location: login');

// Verif du user (si pas de cnx je simule un faux user)
if (! isset($_SESSION['user']->id_utilisateur))
{ // c'est que le user n'existe pas donc pas de log donc machine autonome
	$_SESSION['user'] = (object)array();
	$_SESSION['user']->id_utilisateur = 999;
	$_SESSION['user']->prenom = '';
	$_SESSION['user']->nom = 'Badgeuse';
	$user_cnx = $_SESSION['user']->id_utilisateur;
}
else
{
	$user_cnx = $_SESSION['user']->id_utilisateur;
}

//déclaration des instances nécéssaires :
$Database  = new App\Database('devis');
$Database->DbConnect();
$Totoro    = new App\Totoro('euro');
$Totoro->DbConnect();
$UserClass = new App\Tables\User($Database);

//declaration des variables diverses : 
$msg_info = '';

// recuperations des GET ou POST
$pin     = get_post('pincode', 1);
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
  enregistre les mouvements des user (arrivé et sortie)
  - a ala saisie du code PIN le systeme verifie si le user existe et si il est IN ou OUT et ajoute une ligne de mouvement.

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
	// remplace les faux chiffres (sasisie facon clavier US (donc par exemple un è a la place d'un 7))
	$pin = mb_strtolower($pin, 'UTF-8');
	$pin = str_replace(array('&','é','"',"'",'(','-','è','_','ç','à'),array('1','2','3','4','5','6','7','8','9','0'),$pin);
	$pin = mb_strtoupper($pin, 'UTF-8');
	// recherche du code PIN dans la table user (si le code pin existe retour du USER_ID si non retour FALSE)
	$user_id = FALSE;
	$Q_  = "SELECT * FROM utilisateur WHERE user__time_pin = '".$pin."' OR user__time_rfid = '".$pin."' OR user__time_rfid2 = '".$pin."' ";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$msg_info = 'Code PIN Non valide';
	if ($user_cnx == 56)
		$msg_info .= ' -> '.$pin;

	if (strlen($pin) == 0)
		$msg_info = 'Bonjour, informations de cette page mise à jour à '.$time;
	if ($R_ !== false AND mysqli_num_rows($R_))
	{
		$A_               = mysqli_fetch_array($R_, MYSQLI_ASSOC);
		$user_move_id     = $A_['id_utilisateur'];
		$user_move_prenom = $A_['prenom'];
		$user_cnx = '('.$_SESSION['user']->id_utilisateur.') '.substr($_SESSION['user']->prenom,0,1).' '.$_SESSION['user']->nom;
		// recherche de derniere action pour ce user
		$Q_  = "SELECT tt__id, tt__time, tt__move FROM time_track WHERE tt__user = ".$user_move_id." ORDER BY tt__time DESC LIMIT 1";
		$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
		$A_  = mysqli_fetch_array($R_, MYSQLI_ASSOC);
		$user_last_time   = $A_['tt__time'];
		$user_last_move   = $A_['tt__move'];
		$move             = 'IN'; // par defaut c'est une entrée
		$msg_info_1       = 'Bonjour '.$user_move_prenom;
		$msg_info_2       = ', bienvenue chez Recode.<br>';
		$msg_info_3       = 'Ton arrivé est bien enregistré à '.$time.'.';
		// special apres midi
		if($thm > 1200)
		{
			$msg_info_1       = 'Bonne aprés midi '.$user_move_prenom.' ';
			$msg_info_2       = '.<br>';
		}
		if ($user_last_move == 'IN') // le dernier mouvement est une entré, donc cette saisie est pour une sortie (OUT)
		{
			$move = 'OUT';
			$msg_info_1       = 'Au revoir '.$user_move_prenom;
			$msg_info_2       = '.<br>';
			$msg_info_3       = 'Ton départ est bien enregistré à '.$time.'.';
			if($thm > 1200)
				$msg_info_2       = ', bon appetit.<br>';
			if($thm > 1400)
				$msg_info_2       = ', bonne aprés midi.<br>';
			if($thm > 1700)
				$msg_info_2       = ', bonne soirée.<br>';
		}
		// verification de double saisie ( efface la derniere) pour annuler
		$sec_last = strtotime($user_last_time);
		$sec_now  = strtotime($date_time);
		$sec_dif  = $sec_now - $sec_last;
		if ($sec_dif < 20) // si il y a moins de 20 secondes entre 2 code je n'enregistre rien.
		{
			$msg_info_1       = '! Double saisie ! '.$user_move_prenom;
			$msg_info_2       = '.<br>';
			$msg_info_3       = '2eme action non enregistré';
		}
		else
		{
			// ecriture des infos
			$info_user = '('.$_SESSION['user']->id_utilisateur.') '.$_SESSION['user']->prenom.' '.$_SESSION['user']->nom;
			$Q_  = "INSERT INTO time_track (tt__user, tt__time, tt__move, tt__info, tt__poste) ";
			$Q_ .= "VALUES ('$user_move_id', '$date_time', '$move', '$info', '$user_cnx') ";
			$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_); // var_dump($Q_);
		}
		// Message pour utilisateur
		$msg_info = $msg_info_1.$msg_info_2.$msg_info_3;
	}
}
// recherches des presence et abscence
// SELECT id_utilisateur, user__time_plan FROM utilisateur where user__time_plan IS NOT NULL order by prenom
$Q_  = "SELECT id_utilisateur, user__time_plan, user__time_pin, user__time_rfid, user__time_rfid2, prenom, nom, icone FROM utilisateur where user__time_plan IS NOT NULL order by prenom";
$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
$grid_present = $grid_pasla = $grid_abs = 1; // numero de la case pour le tableau (de 1 à 12)
$html_present = $html_pasla = $html_abs = '<div class="text-center"><div class="row">';
while ($A_  = mysqli_fetch_array($R_, MYSQLI_ASSOC) )
{ // boucle sur les user avec user__time_plan non NULL
	// recherche de l'etat de la derniere action de chaque user
	$user_id         = $A_['id_utilisateur'];
	$user_time_plan  = $A_['user__time_plan'];
	$user_prenom     = $A_['prenom'];
	$user_nom        = $A_['nom'];
	$user_icone      = $A_['icone'];
	// derniere action
	$Q2_  = "SELECT tt__time, tt__move FROM time_track WHERE tt__user = ".$user_id." ORDER BY tt__time DESC LIMIT 1";
	$R2_  = mysqli_query($_SOSUKE_MYSQLI, $Q2_);
	$A2_  = mysqli_fetch_array($R2_, MYSQLI_ASSOC);
	$user_last_dt     = $A2_['tt__time'];
	$user_last_move   = $A2_['tt__move'];
	// planing  du jour.
	$Q3_  = "SELECT * FROM time_plan WHERE tp__name = '".$user_time_plan."' AND tp__jour = ".$jour_sem;
	$R3_  = mysqli_query($_SOSUKE_MYSQLI, $Q3_);
	$A3_  = mysqli_fetch_array($R3_, MYSQLI_ASSOC);
	$tp_am_in    = $A3_['tp__am_in'];
	$tp_am_out   = $A3_['tp__am_out'];
	$tp_pm_in    = $A3_['tp__pm_in'];
	$tp_pm_out   = $A3_['tp__pm_out'];
	// Abscence prevue.
	$Q4_  = "SELECT * FROM time_out WHERE to__user = '".$user_id."' AND to__out < '".$date_time."' AND to__in > '".$date_time."' ";
	$Q4_ .= "AND to__abs_etat <> 'ANNUL' ORDER BY to__out LIMIT 1 ";
	$R4_  = mysqli_query($_SOSUKE_MYSQLI, $Q4_);
	$abs_en_cours = $to_etat = $to_in = $to_motif = $to_out = FALSE;
	if (mysqli_num_rows($R4_) == 1)
	{	
		$A4_  = mysqli_fetch_array($R4_, MYSQLI_ASSOC);
		$to_out       = $A4_['to__out'];
		$to_in        = $A4_['to__in'];
		$to_motif     = $A4_['to__motif'];
		$to_etat      = $A4_['to__abs_etat'];
		$abs_en_cours = TRUE;
	}
	// etude du motif pour la décoration du bonhome  :-)
	$motif_cp = $motif_malad = $motif_perso = FALSE;
	if(strpos($to_motif, 'CP') !== FALSE OR strpos($to_motif, 'CONGE') !== FALSE OR strpos($to_motif, 'VACANCE') !== FALSE ) $motif_cp = TRUE;
	if(strpos($to_motif, 'MALAD') !== FALSE) $motif_malad = TRUE;
	if(strpos($to_motif, 'PERSO') !== FALSE) $motif_perso = TRUE;
	// AM ou PM ? pour connaitre les retard avant 13h c'est matin , apres c'est Apres midi...
	if ($am_pm == 'AM')
		{ $tp_in = $tp_am_in; $tp_out = $tp_am_out; }
	else
		{ $tp_in = $tp_pm_in; $tp_out = $tp_pm_out; }
	$user_last_time   = substr($user_last_dt,11,5);
	$user_last_time_j = substr($user_last_dt,8,2).'/'.substr($user_last_dt,5,2).' '.substr($user_last_dt,11,5);
	$color_sign = 'seagreen'; $color_user = '#698569';
	$dif_time = dif2time($user_last_time, $tp_in);
	$secret_info = $abs_info = '';
	if ($dif_time > $tolerance_retard_in)
		{ $color_sign = 'tomato'; $color_user = '#906969';}
	if ($user_last_move <> 'IN') // le dernier mouvement n'est pas une entrée
		$color_user = 'CadetBlue';
	if ($abs_en_cours)
	{
		$abs_info = '<br>'.$to_motif.'<br><i class="fad fa-house-return"></i> '.dt2dts($to_in);
		$color_user = 'Orange';
	}
	if ($user_cnx == 56) // c'est francois lemoine
		$secret_info = ' data-toggle="tooltip" data-placement="top" title="pin : '.$A_['user__time_pin'].'<br>rfid : '.$A_['user__time_rfid'].'<br>rfid2 : '.$A_['user__time_rfid2'].'"';
	// Creation du personage pour page info present / pas la 
	$html_user  = '<div class="col-1">';
	$html_user .= '<div'.$secret_info.'><i class="fad fa-user fa-2x" style="color:'.$color_user.';"></i></div>';
	$html_user .= '<span class=h6>'.$user_prenom.' '.substr($user_nom,0,1).'.</span><br>';
	if ($user_last_move == 'IN') // le dernier mouvement est une entré, donc cette personne est là
	{
		$html_user .= '<span style="color:'.$color_sign.';"><i class="fad fa-sign-in"></i></span> <em>'.$user_last_time.'</em>';
	}
	else // cette presonne n'est pas là
	{
		$html_user .= '<i class="fad fa-sign-out"></i> <em>'.$user_last_time_j.'</em>'.$abs_info;
	}
	$html_user .= '</div>';

	if ($user_last_move == 'IN') // le dernier mouvement est une entré, donc cette personne est là
	{ 
		$html_present .= $html_user; $grid_present += 1; 
	}
	else
	{
		if ($abs_en_cours)
		{
			$html_abs .= $html_user; $grid_abs +=1; 
		}
		else
		{
			$html_pasla .= $html_user; $grid_pasla +=1; 
		}
	}
}

$empty_grid = '<div class="col"> </div>'; // case vide pour completer si pas 12 cases
$fin_grid   = '</div></div>'; // fin de ROW et fin de CLASS Container

if ($grid_present < 12) $html_present .= $empty_grid;
if ($grid_pasla   < 12) $html_pasla   .= $empty_grid;
if ($grid_abs     < 12) $html_abs     .= $empty_grid;
$html_present .= $fin_grid;
$html_pasla   .= $fin_grid;
$html_abs     .= $fin_grid;

if ($grid_pasla == 1) $html_pasla = ''; // pour ne pas afficher la ligne si il n'y a personne dedans. (1 c'est qu'il n'a personne ! (2 il y a une personne ...))
if ($grid_abs   == 1) $html_abs   = '';

// Sosuké (SQL)
$sql_select_maj   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'MAJ' ";

   /*    888888 888888 88  dP""b8 88  88    db     dP""b8 888888 
  dPYb   88__   88__   88 dP   `" 88  88   dPYb   dP   `" 88__   
 dP__Yb  88""   88""   88 Yb      888888  dP__Yb  Yb  "88 88""   
dP""""Yb 88     88     88  YboodP 88  88 dP""""Yb  YboodP 88888*/

// Donnée transmise au template : 
echo $twig->render('time.twig',
[
'dt'        => $date_fr_long,
'msg_info'  => $msg_info,
'present'   => $html_present,
'pasla'     => $html_pasla,
'abs'       => $html_abs
]);

// supprime le user si c'est 999 (qui est un faux user)
if ( $_SESSION['user']->id_utilisateur == 999 )
	unset($_SESSION['user']);

