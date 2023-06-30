<?php
session_start();
require "./vendor/autoload.php";             // chargement des pages PHP avec use
require "./App/twigloader.php";              // gestion des pages twig
require "./App/Methods/tools_functions.php"; // fonctions Boites à outils 


   /*    o8o                                                                                            
  .o8    `"'                                                                                            
.o888oo oooo  ooo. .oo.  .oo.    .ooooo.              oooo d8b  .ooooo.   .ooooo.   .oooo.   oo.ooooo.  
  888   `888  `888P"Y88bP"Y88b  d88' `88b             `888""8P d88' `88b d88' `"Y8 `P  )88b   888' `88b 
  888    888   888   888   888  888ooo888              888     888ooo888 888        .oP"888   888   888 
  888 .  888   888   888   888  888    .o              888     888    .o 888   .o8 d8(  888   888   888 
  "888" o888o o888o o888o o888o `Y8bod8P' ooooooooooo d888b    `Y8bod8P' `Y8bod8P' `Y888""8o  888bod8P' 
                                                                                              888       
                                                                                             o888*/


//déclaration des instances nécéssaires :
// SOSUKE
$Database  = new App\Database('devis');
$Database->DbConnect();
// TOTORO
$Totoro    = new App\Totoro('euro');
$Totoro->DbConnect();
$UserClass = new App\Tables\User($Database);

$sosuke_host      = "192.168.1.124";
$sosuke_user      = "remote";
$sosuke_passe     = "euro0493";
$sosuke_database  = "devis";
$_SOSUKE_MYSQLI   = mysqli_connect($sosuke_host, $sosuke_user, $sosuke_passe, $sosuke_database);
if (!$_SOSUKE_MYSQLI) die('Erreur de connexion a la base (' . mysqli_connect_errno() . ') Serveur -->'.$_SERVER['SERVER_NAME']);
mysqli_set_charset($_SOSUKE_MYSQLI, "utf8");

//validation Login et droits
if (empty($_SESSION['user']->id_utilisateur)) header('location: login');
// $Keyword   = new App\Tables\Keyword($Database);
$UserClass = new App\Tables\User($Database);

//recupération des listes nécéssaires : 
$badgeurList      = $UserClass->getBadgeur();

// recuperations des GET ou POST
$date_debut     = get_post('date_debut', 1, 'GETPOST');
$date_fin       = get_post('date_fin', 1, 'GETPOST');
$badgeur        = get_post('badgeur', 1, 'GETPOST');
$btn_verif      = get_post('btn_verif', 2);

// Dates
$date = new DateTime();
if(!$date_debut) $date_debut = $date->format('Y-m-01');
if(!$date_fin)   $date_fin   = $date->format('Y-m-d');

$date_debut_fr = date_format(date_create($date_debut),'d/m/Y');
$date_fin_fr   = date_format(date_create($date_fin)  ,'d/m/Y');

$semaine       = array("Dim","Lun","Mar","Mer","Jeu","Ven","Sam");
$mois          = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
$date_fr_long  = $semaine[date('w')]." ".date('j')." ".$mois[date('n')]." ".date('Y');
$date_time     = date('Y-m-d H:i:s');
$time          = date('H:i:s');
$thm           = date('Hi'); // time heure minutescolé pour conparaison
$jour_sem      = date('N');
$am_pm = 'AM'; if ($thm >= 1300) $am_pm = 'PM';
$T_data        = array();
$mid_time      = '13:00:00';
$diffEnSecondes= '';
$SecondesRetardMax = -300;  //5 minutes
$color_logo    = '';
$color_logo_ok = 'style="--fa-primary-color: limegreen;"';
$color_logo_no = 'style="--fa-primary-color: red;"';
$color_logo_mid= 'style="--fa-primary-color: darkorange;"';

//declaration des variables diverses : 
$msg_info = $msg_titre = '';
$msg_titre = 'Récap de temp';

// constantes
$tolerance_retard_in = 5; // en minutes

/*""Yb 888888  dP"Yb  88   88 888888 888888 888888 .dP"Y8 
88__dP 88__   dP   Yb 88   88 88__     88   88__   `Ybo." 
88"Yb  88""   Yb b dP Y8   8P 88""     88   88""   o.`Y8b 
88  Yb 888888  `"YoYo `YbodP' 888888   88   888888 8bodP*/

if ($btn_verif)
{
	// récuperation des info sur le badgeur
	$Q_  = "SELECT id_utilisateur, prenom, nom, postefix, user__time_plan FROM utilisateur where id_utilisateur = $badgeur";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$A_  = mysqli_fetch_array($R_, MYSQLI_ASSOC);
	$badgeur            = [];
	$badgeur['id']      = $A_['id_utilisateur'];
	$badgeur['nom']     = $A_['nom'];
	$badgeur['prenom']  = $A_['prenom'];
	$badgeur['time_plan']  = $A_['user__time_plan'];

	// récuperation des heures a faire en fonction du time_plan
	$t_horaire = [];
	$Q_  = "SELECT tp__jour, tp__type, tp__am_in, tp__am_out, tp__pm_in, tp__pm_out FROM time_plan where tp__name = '".$badgeur['time_plan']."' ORDER BY tp__jour";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	while ($A_  = mysqli_fetch_array($R_, MYSQLI_ASSOC) )
	{
		$t_horaire[$A_['tp__jour']] = $A_; // tableau des horaires (de 1 à 5 soit Lundi a vendredi)
	}

	// compte les jours de travail
	$badgeur['wd'] = getWorkingDays($date_debut,date('Y-m-d', strtotime($date_fin. ' + 1 days')));

	// boucle sur les jours a vérifier
	$date_fin_plus = date('Y-m-d', strtotime($date_fin. ' + 1 days')); // ajoute 1 jour a la date de fin
	$interval = new DateInterval('P1D'); // Interval d'un jour
	$dateRange = new DatePeriod(new DateTime($date_debut), $interval, new DateTime($date_fin_plus));
	$T_i = 1;
	foreach ($dateRange as $date) 
	{
		$ce_jour = $date->format('Y-m-d');
		if (getWorkingDays($ce_jour,date('Y-m-d', strtotime($ce_jour. ' + 1 days')))) // si c'est un jour travaillé
		{
			// jour
			$num_jour               = $date->format('w');
			$T_data[$T_i]['jour']   = $semaine[$date->format('w')].' '.$date->format('d-m');
			// H Prévue
			$T_data[$T_i]['prev']   = substr($t_horaire[$num_jour]['tp__am_in'],0,5).'-'.substr($t_horaire[$num_jour]['tp__am_out'],0,5).' ';
			$T_data[$T_i]['prev']  .= substr($t_horaire[$num_jour]['tp__pm_in'],0,5).'-'.substr($t_horaire[$num_jour]['tp__pm_out'],0,5);
			// H Efféctif
			$t_present = [];
			$diff_sec_jour = 0;
			$Q_  = "SELECT * FROM time_track WHERE tt__user = ".$badgeur['id']." AND tt__time LIKE '".$ce_jour."%' ORDER BY tt__time";
			$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
			$T_data[$T_i]['effect'] = '';
			while ($A_  = mysqli_fetch_array($R_, MYSQLI_ASSOC) )
			{
				$t_present[] = $A_; // tableau des in out (qui doit contenir normalement 4 mouvement par jour)
				$color_logo  = '';
				$xtime       = substr($A_['tt__time'],11);
				// verif des IN AM
				if (substr($A_['tt__move'],0,2) == 'IN' AND $xtime < $mid_time)
				{
					$timestamp_in   = strtotime($xtime);
					$timestamp_prev = strtotime($t_horaire[$num_jour]['tp__am_in']);
					$diffEnSecondes = $timestamp_prev - $timestamp_in;
					$color_logo = $color_logo_ok;
					if ($diffEnSecondes <= $SecondesRetardMax) $color_logo = $color_logo_no;
					if ($diffEnSecondes < 0 AND $diffEnSecondes > $SecondesRetardMax) $color_logo = $color_logo_mid;
					$diff_sec_jour += $diffEnSecondes;
				}
				// verif des OUT AM
				if (substr($A_['tt__move'],0,3) == 'OUT' AND $xtime < $mid_time)
				{
					$timestamp_in   = strtotime($xtime);
					$timestamp_prev = strtotime($t_horaire[$num_jour]['tp__am_out']);
					$diffEnSecondes = $timestamp_in - $timestamp_prev;
					$color_logo = $color_logo_ok;
					if ($diffEnSecondes <= $SecondesRetardMax) $color_logo = $color_logo_no;
					if ($diffEnSecondes < 0 AND $diffEnSecondes > $SecondesRetardMax) $color_logo = $color_logo_mid;
					$diff_sec_jour += $diffEnSecondes;
				}
				// verif des IN PM
				if (substr($A_['tt__move'],0,2) == 'IN' AND $xtime >= $mid_time)
				{
					$timestamp_in   = strtotime($xtime);
					$timestamp_prev = strtotime($t_horaire[$num_jour]['tp__pm_in']);
					$diffEnSecondes = $timestamp_prev - $timestamp_in;
					$color_logo = $color_logo_ok;
					if ($diffEnSecondes <= $SecondesRetardMax) $color_logo = $color_logo_no;
					if ($diffEnSecondes < 0 AND $diffEnSecondes > $SecondesRetardMax) $color_logo = $color_logo_mid;
					$diff_sec_jour += $diffEnSecondes;
				}
				// verif des OUT PM
				if (substr($A_['tt__move'],0,3) == 'OUT' AND $xtime >= $mid_time)
				{
					$timestamp_in   = strtotime($xtime);
					$timestamp_prev = strtotime($t_horaire[$num_jour]['tp__pm_out']);
					$diffEnSecondes = $timestamp_in - $timestamp_prev;
					$color_logo = $color_logo_ok;
					if ($diffEnSecondes <= $SecondesRetardMax) $color_logo = $color_logo_no;
					if ($diffEnSecondes < 0 AND $diffEnSecondes > $SecondesRetardMax) $color_logo = $color_logo_mid;
					$diff_sec_jour += $diffEnSecondes;
				}
				// logo pour in ou out
				$i_move = '<i class="fad fa-sign-out-alt" '.$color_logo.'></i>';
				if (substr($A_['tt__move'],0,2) == 'IN') $i_move = '<i class="fad fa-sign-in-alt" '.$color_logo.'></i>';
				// construction de la chaine d'affichage.
				$T_data[$T_i]['effect'] .= $i_move.$xtime.' ';
			}
			// Delta
			$T_data[$T_i]['delta']  = $diff_sec_jour.'.';
			// Ponctualité
			$T_data[$T_i]['ponct']  = '.';

			$T_i ++;
		}
	}



	// print '<br><br>'.$date_debut.'<br><br>';
	// var_dump($t_horaire);

}

// recherches des presence et abscence
$nb_colonnes = 8;
// liste des user concernés par le pointage
$Q_  = "SELECT id_utilisateur, user__time_plan, user__time_pin, user__time_rfid, user__time_rfid2, prenom, nom, icone FROM utilisateur where user__time_plan IS NOT NULL order by prenom";
$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
$grid_present = $grid_pasla = $grid_abs = 1; // numero de la case pour le tableau (de 1 à x)
$html_present = $html_pasla = $html_abs = '<table><tr class="text-center">';
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
	$tp_type     = $A3_['tp__type'];
	// Abscence prevue.
	$Q4_  = "SELECT * FROM time_out WHERE to__user = '".$user_id."' AND to__out < '".$date_time."' AND to__in > '".$date_time."' ";
	$Q4_ .= "AND to__abs_etat <> 'ANNUL' ORDER BY to__out LIMIT 1 ";
	$R4_  = mysqli_query($_SOSUKE_MYSQLI, $Q4_);
	$abs_en_cours = $to_etat = $to_in = $to_motif = $to_out = FALSE;
	// print $Q4_.'<br>';
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
	$fa_user = 'fa-user fa-lg';
	$motif_cp = $motif_malad = $motif_perso = FALSE;
	if(stripos($to_motif, 'CP')       !== FALSE) $motif_cp    = TRUE;
	if(stripos($to_motif, 'CONGE')    !== FALSE) $motif_cp    = TRUE;
	if(stripos($to_motif, 'VACANCE')  !== FALSE) $motif_cp    = TRUE;
	if(stripos($to_motif, 'MALAD')    !== FALSE) $motif_malad = TRUE;
	if(stripos($to_motif, 'MEDIC')    !== FALSE) $motif_malad = TRUE;
	if(stripos($to_motif, 'PERSO')    !== FALSE) $motif_perso = TRUE;
	if($motif_cp == TRUE)    $fa_user = 'fa-user-astronaut fa-2x';
	if($motif_malad == TRUE) $fa_user = 'fa-user-injured fa-2x';
	if($motif_perso == TRUE) $fa_user = 'fa-user-secret fa-2x';

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
	if (substr($user_last_move,0,2) <> 'IN') // le dernier mouvement n'est pas une entrée
		$color_user = 'CadetBlue';
	$fa_logo_in = 'fa-sign-in';
	if ($user_last_move == 'IN_TT') $fa_logo_in = 'fa-house'; // logo spécial Télétravail
	if ($abs_en_cours)
	{
		$abs_info = $to_motif.'<br><i class="fad fa-house-return"></i> '.dt2dts($to_in);
		$color_user = 'Orange';
	}
	// Creation du personage
	$html_user  = '<td width=12.5%>';
	$html_user .= '<div'.$secret_info.'><i class="fad '.$fa_user.' " style="color:'.$color_user.';"></i></div>';
	$html_user .= '<span class=h6>'.$user_prenom.' '.substr($user_nom,0,1).'.</span><br>';
	if (substr($user_last_move,0,2) == 'IN') // le dernier mouvement est une entré, donc cette personne est là
	{
		$html_user .= '<span style="color:'.$color_sign.';"><i class="fad '.$fa_logo_in.'"></i></span> <em>'.$user_last_time.'</em>';
	}
	else // cette presonne n'est pas là
	{
		if ($abs_en_cours)
		{
			$html_user .= $abs_info;
		}
		else
		{
			$html_user .= '<i class="fad fa-sign-out"></i> <em>'.$user_last_time_j.'</em>'.$abs_info;
		}
	}
	$html_user .= '</td>';

	if (substr($user_last_move,0,2) == 'IN') // le dernier mouvement est une entré, donc cette personne est là
	{ 
		if ($grid_present == $nb_colonnes+1) 
		{ $grid_present = 1; $html_present .= '</tr><tr class="text-center">';}
		$html_present .= $html_user; $grid_present += 1; 
	}
	else
	{
		if ($abs_en_cours)
		{
			if ($grid_abs == $nb_colonnes+1) 
			{ $grid_abs = 1; $html_abs .= '</tr><tr class="text-center">';}
			$html_abs .= $html_user; $grid_abs +=1; 
		}
		else
		{
			if ($tp_type <> 'OUT') // pour ne pas afficher dans pasla ceux qui sont out (ne travail pas ce jour)
			{
				if ($grid_pasla == $nb_colonnes+1) 
				{ $grid_pasla = 1; $html_pasla .= '</tr><tr class="text-center">'; }
				$html_pasla .= $html_user; $grid_pasla += 1;
			}
		}
	}

}

$empty_grid = '<td width=12.5%> </td>'; // case vide pour completer si pas x cases
$fin_grid   = '</tr></table>'; // fin de ROW et fin de CLASS Container

if ($grid_present < $nb_colonnes) $html_present .= $empty_grid;
if ($grid_pasla   < $nb_colonnes) $html_pasla   .= $empty_grid;
if ($grid_abs     < $nb_colonnes) $html_abs     .= $empty_grid;
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
echo $twig->render('time_recap.twig',
[
'titre'     => $msg_titre,
'info'      => $msg_info,
'badgeurList'      => $badgeurList, 
'badgeurSelect'    => $badgeur,
'date_debut'       => $date_debut, 
'date_fin'         => $date_fin,
'date_debut_fr'    => $date_debut_fr, 
'date_fin_fr'      => $date_fin_fr,
't_data'           => $T_data
]);

// supprime le user si c'est 999 (qui est un faux user)
if ( $_SESSION['user']->id_utilisateur == 999 )
	unset($_SESSION['user']);

