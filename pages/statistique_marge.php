<?php
session_start();
require "./vendor/autoload.php";             // chargement des pages PHP avec use
require "./App/twigloader.php";              // gestion des pages twig
require "./App/Methods/tools_functions.php"; // fonctions Boites à outils 


 /*ooooo..o     .                 .        ooo        ooooo                                         
d8P'    `Y8   .o8               .o8        `88.       .888'                                         
Y88bo.      .o888oo  .oooo.   .o888oo       888b     d'888   .oooo.   oooo d8b  .oooooooo  .ooooo.  
 `"Y8888o.    888   `P  )88b    888         8 Y88. .P  888  `P  )88b  `888""8P 888' `88b  d88' `88b 
     `"Y88b   888    .oP"888    888         8  `888'   888   .oP"888   888     888   888  888ooo888 
oo     .d8P   888 . d8(  888    888 .       8    Y     888  d8(  888   888     `88bod8P'  888    .o 
8""88888P'    "888" `Y888""8o   "888"      o8o        o888o `Y888""8o d888b    `8oooooo.  `Y8bod8P' 
                                                                               d"     YD            
                                                                               "Y88888*/            

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

/*8888  dP"Yb  88b 88  dP""b8 888888 88  dP"Yb  88b 88 .dP"Y8 
88__   dP   Yb 88Yb88 dP   `"   88   88 dP   Yb 88Yb88 `Ybo." 
88""   Yb   dP 88 Y88 Yb        88   88 Yb   dP 88 Y88 o.`Y8b 
88      YbodP  88  Y8  YboodP   88   88  YbodP  88  Y8 8bodP*/ 

function sql_1_an($sql, $option=false)
{ 
	$search  = array('2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030');
	$replace = array('2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029');
	$sql_1   = str_replace($search, $replace, $sql);
	
	return $sql_1; // indique la valeur à renvoyer 
} 

function lg_tab_html($sql, $prestation, $etat, $base='sosuke', $bold=FALSE)
{
	global $Database; // Rend la variable accessible dans la fonction.
	global $Totoro;
	$lg_tab    = '';
	if ($base == 'totoro')
	{
		$T_data    = $Totoro->Pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
		$somme     = $T_data['SOMME'];
		$sql_1     = sql_1_an($sql);
		$T_data    = $Totoro->Pdo->query($sql_1)->fetch(PDO::FETCH_ASSOC);
		$somme_1   = $T_data['SOMME'];
	}
	else // pour sosuké par default
	{
		$T_data    = $Database->Pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
		$somme     = $T_data['SOMME'];
		$sql_1     = sql_1_an($sql);
		$T_data    = $Database->Pdo->query($sql_1)->fetch(PDO::FETCH_ASSOC);
		$somme_1   = $T_data['SOMME'];
	}
	if ($somme_1) // pour eviter le divizion par 0
		$variation = (($somme-$somme_1)/$somme_1)*100;
	else
		$variation = '0';
	if ($bold)
		{ $bold_on = '<b>'; $bold_off = '</b>';}
	else
		{ $bold_on = $bold_off = '';}
	$lg_tab   .= '<tr> ';
	$lg_tab   .= '<td>'.$bold_on.$prestation.$bold_off.'</td> ';
	$lg_tab   .= '<td>'.$bold_on.$etat.$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($somme, 2, ',', ' ').$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($somme_1, 2, ',', ' ').$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($variation, 2, ',', ' ').$bold_off.' %</td> ';
	$lg_tab   .= '</tr> ';

	return $lg_tab; // renvoie la ligne complette de tableau en html
} 

function lg_tab_separateur($titre, $option=false)
{
	$lg_tab    = '';
	$lg_tab   .= '<tr class="table-info"><td colspan=5><b>'.$titre.'</b></td></tr> ';

	return $lg_tab; // renvoie la ligne complette de tableau en html
} 


//declaration des variables diverses : 
$debug_info = $tab_html = '';

// recuperations des GET ou POST
$date_debut     = get_post('date_debut', 1, 'GETPOST');
$date_fin       = get_post('date_fin', 1, 'GETPOST');

// dates par default 
$date = new DateTime();
if(!$date_debut) $date_debut = $date->format('Y-m-01');
if(!$date_fin)   $date_fin   = $date->format('Y-m-t');

// Date format calendrier
$date_debut_fr = date_format(date_create($date_debut),'d/m/Y');
$date_fin_fr   = date_format(date_create($date_fin)  ,'d/m/Y');

/*8888 Yb  dP 88""Yb 88     88  dP""b8    db    888888 88  dP"Yb  88b 88 .dP"Y8 
88__    YbdP  88__dP 88     88 dP   `"   dPYb     88   88 dP   Yb 88Yb88 `Ybo." 
88""    dPYb  88"""  88  .o 88 Yb       dP__Yb    88   88 Yb   dP 88 Y88 o.`Y8b 
888888 dP  Yb 88     88ood8 88  YboodP dP""""Yb   88   88  YbodP  88  Y8 8bodP' 

les cumules se fonts pour une periode, pour # critères, presenté par prestations 
avec les extentions de garanties séparé.

la base de select est la meme, le groupement aussi, le where change en fonctions
des type de demandes.

pour info : le BETWEEN doit etre utilisé avec 23:59:59 ou sans precision d'heure pour la limite de fin  
il faut ajouter un jour a la date ($date_fin_plus_un = date("Y-m-d", strtotime($Date.'+ 1 days'));) */

// Base commune pour creation requette
// Sosuké (SQL)
$sql_select_vente = "SELECT SUM(cmdl__qte_fact * cmdl__puht) AS SOMME ";
$sql_select_v_gar = "SELECT SUM(cmdl__qte_fact * cmdl__garantie_puht) AS SOMME ";
$sql_from         = "FROM cmd LEFT JOIN cmd_ligne ON cmd_ligne.cmdl__cmd__id = cmd.cmd__id ";
$sql_famille      = "LEFT JOIN art_fmm ON cmd_ligne.cmdl__id__fmm = art_fmm.afmm__id AND cmd_ligne.cmdl__id__fmm = art_fmm.afmm__id ";
$sql_famille     .= "LEFT JOIN keyword ON art_fmm.afmm__famille = keyword.kw__value AND keyword.kw__type = 'famil' ";
$sql_where        = "WHERE ";
$sql_where       .= "cmd__date_fact BETWEEN '".$date_debut." 00:00:00' AND '".$date_fin." 23:59:59' ";
$sql_where       .= "AND cmd__etat IN ('VLD', 'VLA') ";
$sql_w_vte        = "AND cmdl__prestation = 'VTE' ";
$sql_w_ech        = "AND cmdl__prestation = 'ECH' ";
$sql_w_rpr        = "AND cmdl__prestation = 'RPR' ";
$sql_w_rem        = "AND cmdl__prestation = 'REM' ";
$sql_w_rep        = "AND cmdl__prestation = 'REP' ";
$sql_w_loc        = "AND cmdl__prestation = 'LOC' ";
$sql_w_int        = "AND cmdl__prestation IN ('INT','DEP') ";
$sql_w_mnt        = "AND cmdl__prestation = 'MNT' ";
$sql_w_prt        = "AND cmdl__prestation = 'PRT' ";
$sql_w_pre        = "AND cmdl__prestation = 'PRE' ";
$sql_w_bro        = "AND cmdl__prestation = 'BRO' ";
$sql_w_neu        = "AND cmdl__etat = 'NEU' ";
$sql_w_occ        = "AND cmdl__etat = 'OCC' ";
$sql_w_cdb        = "AND keyword.kw__info = 'CDB' ";
$sql_w_imp        = "AND keyword.kw__info IN ('ILM', 'IT') ";
$sql_w_mic        = "AND keyword.kw__info = 'MICRO' ";
$sql_w_con        = "AND keyword.kw__info = 'CONSO' ";
$sql_w_pid        = "AND keyword.kw__info = 'PID' ";
$sql_w_acc        = "AND keyword.kw__info = 'ACC' ";
$sql_w_ser        = "AND keyword.kw__info IN ('SER','XX') ";
// Totoro (TQL) c'est bien du sql mais c'est pour les differencier
$tql_select_achat = "SELECT SUM(pu_ht) AS SOMME ";
$tql_from         = "FROM locator ";
$tql_where        = "WHERE ";
$tql_where       .= "in_datetime BETWEEN '".$date_debut." 00:00:00' AND '".$date_fin." 23:59:59' ";
$tql_w_achat      = "AND in_prest = 'Achat' ";
$tql_w_neuf       = "AND id_etat = 1 ";
$tql_w_occ        = "AND id_etat IN (0, 11) ";
$tql_w_conso      = "AND id_etat IN (31, 32) ";
$tql_sel_po_port  = "SELECT SUM(po_frais_port_euro) AS SOMME ";
$tql_from_po      = "FROM po ";
$tql_where_po     = "WHERE ";
$tql_where_po    .= "po_dt_cmd BETWEEN '".$date_debut."' AND '".$date_fin."' ";
$tql_stk_sql      = "SELECT sum(locator.pu_ht) AS SOMME FROM locator WHERE ";
$tql_stk_deb      = "( id_etat in (0,1,11,31,32) AND in_datetime < '".$date_debut."' AND (out_datetime is NULL OR out_datetime >= '".$date_debut."') ) ";
$tql_stk_deb     .= "OR ( id_etat in (21,22) AND down_datetime >= '".$date_debut."' AND	in_datetime < '".$date_debut."' AND (out_datetime is NULL OR out_datetime >= '".$date_debut."') ) ";
$tql_stk_deb_neuf = "( id_etat in (1,31,32) AND in_datetime < '".$date_debut."' AND (out_datetime is NULL OR out_datetime >= '".$date_debut."') ) ";
$tql_stk_deb_occ  = "( id_etat in (0,11) AND in_datetime < '".$date_debut."' AND (out_datetime is NULL OR out_datetime >= '".$date_debut."') ) ";
$tql_stk_deb_occ .= "OR ( id_etat in (21,22) AND down_datetime >= '".$date_debut."' AND	in_datetime < '".$date_debut."' AND (out_datetime is NULL OR out_datetime >= '".$date_debut."') ) ";
$tql_stk_fin      = "( id_etat in (0,1,11,31,32) AND in_datetime < '".$date_fin."' AND (out_datetime is NULL OR out_datetime >= '".$date_fin."') ) ";
$tql_stk_fin     .= "OR ( id_etat in (21,22) AND down_datetime >= '".$date_fin."' AND	in_datetime < '".$date_fin."' AND (out_datetime is NULL OR out_datetime >= '".$date_fin."') ) ";
$tql_stk_fin_neuf = "( id_etat in (1,31,32) AND in_datetime < '".$date_fin."' AND (out_datetime is NULL OR out_datetime >= '".$date_fin."') ) ";
$tql_stk_fin_occ  = "( id_etat in (0,11) AND in_datetime < '".$date_fin."' AND (out_datetime is NULL OR out_datetime >= '".$date_fin."') ) ";
$tql_stk_fin_occ .= "OR ( id_etat in (21,22) AND down_datetime >= '".$date_fin."' AND	in_datetime < '".$date_fin."' AND (out_datetime is NULL OR out_datetime >= '".$date_fin."') ) ";


//  dP""b8    db
// dP   `"   dPYb
// Yb       dP__Yb
//  YboodP dP""""Yb

$titre_stat  = 'Chiffre d\'affaires facturé pour etude de marges';

$tab_html .= lg_tab_separateur('Ventes de marchandises');
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_vte.$sql_w_neu,"01 Ventes (Tout)","Neuf","sosuke",TRUE);
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_cdb,"01b Ventes CDB","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_imp,"01c Ventes IMPRIMANTES","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_mic,"01d Ventes MICRO","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_con,"01e Ventes CONSO","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_pid,"01f Ventes Pieces détachées","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_acc,"01g Ventes Accessoires","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_ser,"01h Ventes Services","Neuf");
$debug_info .= "01h -------------- <br>".$sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_ser.'<br>';
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_vte.$sql_w_occ,"02 Ventes (Tout)","Occasion","sosuke",TRUE);
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_cdb,"02b Ventes CDB","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_imp,"02c Ventes IMPRIMANTES","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_mic,"02d Ventes MICRO","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_con,"02e Ventes CONSO","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_pid,"02f Ventes Pieces détachées","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_acc,"02g Ventes Accessoires","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_ser,"02h Ventes Services","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_ech.$sql_w_neu,"03 Echange matériels","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_ech.$sql_w_occ,"04 Echange matériels","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_rpr.$sql_w_neu,"05 Reprise","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_rpr.$sql_w_occ,"05b Reprise","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_rem,"06 & 07 Remise","NC.");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_pre,"07b Prêt","Tout état");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_bro,"07c Broke","Tout état");
$tab_html .= lg_tab_separateur('Achats de marchandises');
$tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat,"00 Achats matériels","Tout état","totoro");
$tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_neuf,"08 Achats matériels","Neuf","totoro");
$tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_occ,"09 Achats matériels","Occasion ou n.a.","totoro");
$tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_conso,"09b Achats matériels","Consommables","totoro");
$tab_html .= lg_tab_html($tql_sel_po_port.$tql_from_po.$tql_where_po,"10 & 11 Transport matériels","Tout état","totoro");
$tab_html .= lg_tab_separateur('Production');
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_rep,"12 Réparation","NC.");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_loc.$sql_w_neu,"13 Location","Neuf");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_loc.$sql_w_occ,"14 Location","Occasion");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_mnt,"15 Maintenance","NC.");
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_int,"16 Intervention et Dep.","NC.");
$debug_info .= "16 --------------- <br>".$sql_select_vente.$sql_from.$sql_where.$sql_w_int.'<br>';
$tab_html .= lg_tab_html($sql_select_v_gar.$sql_from.$sql_where.$sql_w_neu,"17 Extension de garantie","Neuf");
$tab_html .= lg_tab_html($sql_select_v_gar.$sql_from.$sql_where.$sql_w_occ,"18 Extension de garantie","Occasion");
$tab_html .= lg_tab_separateur('Transport sur vente');
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_prt,"19 Port facturé","NC.");
// $tab_html .= lg_tab_separateur('Stock');
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_deb_neuf,"21 Stock début","Neuf","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_deb_occ,"22 Stock début","Occasion","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_fin_neuf,"23 Stock fin","Neuf","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_fin_occ,"24 Stock fin","Occasion","totoro");

$debug_info = '';

// Donnée transmise au template : 
echo $twig->render('statistique_marge.twig',
[
'date_debut'       => $date_debut, 
'date_fin'         => $date_fin,
'date_debut_fr'    => $date_debut_fr, 
'date_fin_fr'      => $date_fin_fr,
'titre_stat'       => $titre_stat,
'debug_info'       => $debug_info,
'tab_html'         => $tab_html
]);
