<?php
session_start();
require "./vendor/autoload.php";             // chargement des pages PHP avec use
require "./App/twigloader.php";              // gestion des pages twig
require "./App/Methods/tools_functions.php"; // fonctions Boites à outils 


 /*ooooo..o     .                 .          .oooooo.     .oooooo.   ooo        ooooo ooooooooo.   ooooooooooooo       .o.       
d8P'    `Y8   .o8               .o8         d8P'  `Y8b   d8P'  `Y8b  `88.       .888' `888   `Y88. 8'   888   `8      .888.      
Y88bo.      .o888oo  .oooo.   .o888oo      888          888      888  888b     d'888   888   .d88'      888          .8"888.     
 `"Y8888o.    888   `P  )88b    888        888          888      888  8 Y88. .P  888   888ooo88P'       888         .8' `888.    
     `"Y88b   888    .oP"888    888        888          888      888  8  `888'   888   888              888        .88ooo8888.   
oo     .d8P   888 . d8(  888    888 .      `88b    ooo  `88b    d88'  8    Y     888   888              888       .8'     `888.  
8""88888P'    "888" `Y888""8o   "888"       `Y8bood8P'   `Y8bood8P'  o8o        o888o o888o            o888o     o88o     o8888*/ 

//validation Login et droits
if (empty($_SESSION['user']->id_utilisateur)) header('location: login');
if ($_SESSION['user']->user__facture_acces < 10 ) header('location: noAccess');

//déclaration des instances nécéssaires :
$user      = $_SESSION['user'];
$Database  = new App\Database('devis');
$Database->DbConnect();
$Totoro    = new App\Totoro('euro');
$Totoro->DbConnect();
$UserClass = new App\Tables\User($Database);

//declaration des variables diverses : 
$debug_info = $tab_html = '';

// recuperations des GET ou POST
$p20     = get_post('p20', 1);
$p21     = get_post('p21', 1);
$p22     = get_post('p22', 1);
$p23     = get_post('p23', 1);
$p24     = get_post('p24', 1);
$m20     = get_post('m20', 1);
$m21     = get_post('m21', 1);
$m22     = get_post('m22', 1);
$m23     = get_post('m23', 1);
$m24     = get_post('m24', 1);
$r20     = get_post('r20', 1);
$r21     = get_post('r21', 1);
$r22     = get_post('r22', 1);
$r23     = get_post('r23', 1);
$r24     = get_post('r24', 1);
$btn_ok  = get_post('btn_ok', 2);
// dates par default 
$date = new DateTime();
$date_jour     = $date->format('Y-m-d');

/*8888 Yb  dP 88""Yb 88     88  dP""b8    db    888888 88  dP"Yb  88b 88 .dP"Y8 
88__    YbdP  88__dP 88     88 dP   `"   dPYb     88   88 dP   Yb 88Yb88 `Ybo." 
88""    dPYb  88"""  88  .o 88 Yb       dP__Yb    88   88 Yb   dP 88 Y88 o.`Y8b 
888888 dP  Yb 88     88ood8 88  YboodP dP""""Yb   88   88  YbodP  88  Y8 8bodP' 

page de mise a jour des chiffres uitilisé pour affiner les stats de marges
c'est ecrit dans la table Keyword (kw__type = carmrg)
la valeur est dans KW__lib
*/

// fonctions
function sel_lib_info($sql)
{
	// info($sql);
	global $Database; // Rend la variable accessible dans la fonction.
	$ret = array();
	$T_data    = $Database->Pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
	$kw__lib   = $T_data['kw__lib'];
	$kw__info  = $T_data['kw__info'];
	$ret[0]    = $kw__lib;
	$ret[1]    = $kw__info;
	return $ret; // renvoie le tableau avec liob et info
}

/*""Yb 888888  dP"Yb  88   88 888888 888888 888888 .dP"Y8 
88__dP 88__   dP   Yb 88   88 88__     88   88__   `Ybo." 
88"Yb  88""   Yb b dP Y8   8P 88""     88   88""   o.`Y8b 
88  Yb 888888  `"YoYo `YbodP' 888888   88   888888 8bodP*/

if ($btn_ok)
{
	// cnx table sur sosuke
	$sosuke_host      = "192.168.1.124";
	$sosuke_user      = "remote";
	$sosuke_passe     = "euro0493";
	$sosuke_database  = "devis";
	$_SOSUKE_MYSQLI   = mysqli_connect($sosuke_host, $sosuke_user, $sosuke_passe, $sosuke_database);
	if (!$_SOSUKE_MYSQLI) die('Erreur de connexion a la base (' . mysqli_connect_errno() . ') Serveur -->'.$_SERVER['SERVER_NAME']);
	mysqli_set_charset($_SOSUKE_MYSQLI, "utf8");
	// ecriture des infos
	$Q_  = "UPDATE keyword SET kw__lib='".$p20."' WHERE (kw__type='camrg') AND (kw__value='P20') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$p21."' WHERE (kw__type='camrg') AND (kw__value='P21') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$p22."' WHERE (kw__type='camrg') AND (kw__value='P22') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$p23."' WHERE (kw__type='camrg') AND (kw__value='P23') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$p24."' WHERE (kw__type='camrg') AND (kw__value='P24') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$m20."' WHERE (kw__type='camrg') AND (kw__value='M20') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$m21."' WHERE (kw__type='camrg') AND (kw__value='M21') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$m22."' WHERE (kw__type='camrg') AND (kw__value='M22') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$m23."' WHERE (kw__type='camrg') AND (kw__value='M23') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$m24."' WHERE (kw__type='camrg') AND (kw__value='M24') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$r20."' WHERE (kw__type='camrg') AND (kw__value='R20') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$r21."' WHERE (kw__type='camrg') AND (kw__value='R21') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$r22."' WHERE (kw__type='camrg') AND (kw__value='R22') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$r23."' WHERE (kw__type='camrg') AND (kw__value='R23') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$r24."' WHERE (kw__type='camrg') AND (kw__value='R24') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
	$Q_  = "UPDATE keyword SET kw__lib='".$date_jour."' WHERE (kw__type='camrg') AND (kw__value='MAJ') LIMIT 1";
	$R_  = mysqli_query($_SOSUKE_MYSQLI, $Q_);
}

// Sosuké (SQL)
$sql_select_p20   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'P20' ";
$sql_select_p21   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'P21' ";
$sql_select_p22   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'P22' ";
$sql_select_p23   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'P23' ";
$sql_select_p24   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'P24' ";
$sql_select_m20   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'M20' ";
$sql_select_m21   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'M21' ";
$sql_select_m22   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'M22' ";
$sql_select_m23   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'M23' ";
$sql_select_m24   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'M24' ";
$sql_select_r20   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'R20' ";
$sql_select_r21   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'R21' ";
$sql_select_r22   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'R22' ";
$sql_select_r23   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'R23' ";
$sql_select_r24   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'R24' ";
$sql_select_maj   = "SELECT * FROM keyword WHERE kw__type = 'camrg' AND kw__value = 'MAJ' ";

$p20_li = sel_lib_info($sql_select_p20);
$p21_li = sel_lib_info($sql_select_p21);
$p22_li = sel_lib_info($sql_select_p22);
$p23_li = sel_lib_info($sql_select_p23);
$p24_li = sel_lib_info($sql_select_p24);
$m20_li = sel_lib_info($sql_select_m20);
$m21_li = sel_lib_info($sql_select_m21);
$m22_li = sel_lib_info($sql_select_m22);
$m23_li = sel_lib_info($sql_select_m23);
$m24_li = sel_lib_info($sql_select_m24);
$r20_li = sel_lib_info($sql_select_r20);
$r21_li = sel_lib_info($sql_select_r21);
$r22_li = sel_lib_info($sql_select_r22);
$r23_li = sel_lib_info($sql_select_r23);
$r24_li = sel_lib_info($sql_select_r24);

$maj_li = sel_lib_info($sql_select_maj);

   /*    888888 888888 88  dP""b8 88  88    db     dP""b8 888888 
  dPYb   88__   88__   88 dP   `" 88  88   dPYb   dP   `" 88__   
 dP__Yb  88""   88""   88 Yb      888888  dP__Yb  Yb  "88 88""   
dP""""Yb 88     88     88  YboodP 88  88 dP""""Yb  YboodP 88888*/

$titre_stat  = 'Chiffres comptable pour Stat marges';
// Donnée transmise au template : 
echo $twig->render('statistique_compta.twig',

[
'maj'       => $maj_li,
'p20'       => $p20_li,
'p21'       => $p21_li,
'p22'       => $p22_li,
'p23'       => $p23_li,
'p24'       => $p24_li,
'm20'       => $m20_li,
'm21'       => $m21_li,
'm22'       => $m22_li,
'm23'       => $m23_li,
'm24'       => $m24_li,
'r20'       => $r20_li,
'r21'       => $r21_li,
'r22'       => $r22_li,
'r23'       => $r23_li,
'r24'       => $r24_li
]);
