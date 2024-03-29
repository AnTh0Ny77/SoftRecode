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
$tot_delta     = 0;
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
		if (getWorkingDays($ce_jour,date('Y-m-d', strtotime($ce_jour. ' + 1 days'))) AND $date->format('w') > 0) // si c'est un jour travaillé
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
			$delta = $diff_sec_jour/60;
			$tot_delta += $delta;
			if ($delta <> 0)
				$T_data[$T_i]['delta'] = '('.round($delta).' Min.)';
		
			// Information sur les abs déclaré prévue
			$Q4_  = "SELECT * FROM time_out WHERE to__user = '".$badgeur['id']."' AND to__out < '".$ce_jour." 24:00:00' AND to__in > '".$ce_jour."' ";
			$Q4_ .= "AND to__abs_etat <> 'ANNUL' ORDER BY to__out";
			$R4_  = mysqli_query($_SOSUKE_MYSQLI, $Q4_);
			$abs_en_cours = FALSE;
			$info = '';
			// print $Q4_.'<br>';
			while ($A4_ = mysqli_fetch_array($R4_, MYSQLI_ASSOC) )
			{	
				$time_stamp = mktime(0,0,0,substr($A4_['to__out'],5,2),substr($A4_['to__out'],8,2),substr($A4_['to__out'],0,4));
				$info        .= '<b>'.$A4_['to__motif'].'</b> - ';
				// si le jour de debut de time_out = ce jour j'ecrit "de 99:99"
				if(substr($A4_['to__out'],0,10) == $ce_jour)
					$info        .= '<em>de '.substr($A4_['to__out'],11,5).'</em> ';
				// si le jour de fin de time_out = ce jour j'ecrit "à 99:99"
				if(substr($A4_['to__in'],0,10) == $ce_jour)
					$info        .= '<em>à '.substr($A4_['to__in'],11,5).'</em> ';
				// si il y a un commentaire sur l'abscence je le met
				if (strlen($A4_['to__info']) > 0)
					$info        .= '<small><em>('.$A4_['to__info'].')</em></small> ';
				// $info        .= $A4_['to__abs_etat']; // etat de la demande (demande ou accepté)
				if (substr($A4_['to__abs_dt'],0,10) == substr($A4_['to__out'],0,10))
					$info        .= '<b>! info du jour</b> ';
				$info .='<br>';
				$abs_en_cours = TRUE;
			}
			if ($t_horaire[$num_jour]['tp__type'] == 'TT')
				$info        .= '<b>TT</b>';
			if ($t_horaire[$num_jour]['tp__type'] == 'OUT')
				$info        .= '<b>Jour OFF</b>';

			$T_data[$T_i]['info']  = $info;

			$T_i ++;
		}
	}



	// print '<br><br>'.$date_debut.'<br><br>';
	// var_dump($t_horaire);

}

   /*    888888 888888 88  dP""b8 88  88    db     dP""b8 888888 
  dPYb   88__   88__   88 dP   `" 88  88   dPYb   dP   `" 88__   
 dP__Yb  88""   88""   88 Yb      888888  dP__Yb  Yb  "88 88""   
dP""""Yb 88     88     88  YboodP 88  88 dP""""Yb  YboodP 88888*/

// Donnée transmise au template : 
echo $twig->render('time_recap.twig',
[
'titre'            => $msg_titre,
'info'             => $msg_info,
'badgeurList'      => $badgeurList, 
'badgeurSelect'    => $badgeur,
'date_debut'       => $date_debut, 
'date_fin'         => $date_fin,
'date_debut_fr'    => $date_debut_fr, 
'date_fin_fr'      => $date_fin_fr,
't_data'           => $T_data,
'tot_delta'        => round($tot_delta)
]);

// supprime le user si c'est 999 (qui est un faux user)
if ( $_SESSION['user']->id_utilisateur == 999 )
	unset($_SESSION['user']);

