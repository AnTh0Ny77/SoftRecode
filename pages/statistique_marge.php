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

// Variables
	$somme_ca = $somme_ca_1 = 0;
	$somme_achat = $somme_achat_1 = 0;
	$somme_stk_deb = $somme_stk_fin = 0;
	$stv = $stp = $stv_1 = $stp_1 = 0;
	$v01  = $v01b = $v01c = $v01d = $v01e = $v01f = $v01g = $v01h = array();
	$v02  = $v02b = $v02c = $v02d = $v02e = $v02f = $v02g = $v02h = array();
	$v03  = $v04  = $v05  = $v05b = $v06  = $v07b = $v07c = array();
	$v12  = $v13  = $v14  = $v15  = $v16  = $v17  = $v18  = $v19  = array();
	$a01b = $a01c = $a01d = $a01f = $a01g = $a01x = array();
	$a02b = $a02c = $a02d = $a02f = $a02g = $a02x = $a02e = array();
	$s51b = $s51c = $s51d = $s51f = $s51g = array();
	$s52b = $s52c = $s52d = $s52f = $s52g = $s54a = $s55a = array();


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


function stat_vente($sql, $base='sosuke')
{
	// info($sql);
	global $Database; // Rend la variable accessible dans la fonction.
	global $Totoro;
	$ret = array();
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
	$ret[0] = $somme;
	$ret[1] = $somme_1;
	return $ret; // renvoie le tableau avec chiffre n et n-1
}


function lg_tab_html_2($nn1, $prestation, $etat, $option=FALSE)
{
	$somme   = $nn1[0];
	$somme_1 = $nn1[1];
	if (isset($nn1[2]))
		$mc  = $nn1[2];
	else 
		$mc  = FALSE;
	if ($somme_1 > 0) // pour eviter le divizion par 0
		$variation = (($somme-$somme_1)/$somme_1)*100;
	else
		$variation = '0';
	$bold = $achat = FALSE;
	if ($option == 1) $bold = TRUE;
	if ($bold)
		{ $bold_on = '<b>'; $bold_off = '</b>';}
	else
		{ $bold_on = $bold_off = '';}
	$lg_tab    = '<tr> ';
	$lg_tab   .= '<td>'.$bold_on.$prestation.$bold_off.'</td> ';
	$lg_tab   .= '<td>'.$bold_on.$etat.$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($somme, 2, ',', ' ').$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($somme_1, 2, ',', ' ').$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($variation, 2, ',', ' ').$bold_off.' %</td> ';
	$lg_tab   .= '<td align=right>';
	if ($mc !== FALSE)
		$lg_tab   .= $bold_on.number_format($mc, 2, ',', ' ').$bold_off;
	$lg_tab   .= '</td> ';
	$lg_tab   .= '<td align=right> </td> ';
	$lg_tab   .= '</tr> ';

	return $lg_tab; // renvoie la ligne complette de tableau en html
} 



function lg_tab_html($sql, $prestation, $etat, $base='sosuke', $option=FALSE)
{
	// info($sql);
	global $Database; // Rend la variable accessible dans la fonction.
	global $Totoro;
	global $somme_ca, $somme_ca_1;
	global $somme_achat, $somme_achat_1;
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
	if ($somme_1 > 0) // pour eviter le divizion par 0
		$variation = (($somme-$somme_1)/$somme_1)*100;
	else
		$variation = '0';
	$bold = $achat = FALSE;
	if ($option == 1) $bold = TRUE;
	if ($option == 2) $achat = TRUE;
	if ($bold)
		{ $bold_on = '<b>'; $bold_off = '</b>'; $somme_ca += $somme; $somme_ca_1 += $somme_1;}
	else
		{ $bold_on = $bold_off = '';}
	if ($achat)
		{ $somme_achat += $somme; $somme_achat_1 += $somme_1;}
	$lg_tab   .= '<tr> ';
	$lg_tab   .= '<td>'.$bold_on.$prestation.$bold_off.'</td> ';
	$lg_tab   .= '<td>'.$bold_on.$etat.$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($somme, 2, ',', ' ').$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($somme_1, 2, ',', ' ').$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($variation, 2, ',', ' ').$bold_off.' %</td> ';
	$lg_tab   .= '<td align=right> </td> ';
	$lg_tab   .= '<td align=right> </td> ';
	$lg_tab   .= '</tr> ';

	return $lg_tab; // renvoie la ligne complette de tableau en html
} 

function lg_tab_stock_html($sql, $prestation, $etat)
{
	global $Totoro;
	global $date_debut, $date_fin;
	global $somme_stk_deb, $somme_stk_fin;
	
	$sql_deb = str_replace('##DT##',$date_debut.' 00:00:00', $sql);
	$sql_fin = str_replace('##DT##',$date_fin.' 23:59:59', $sql);
	// var_dump($sql_fin);
	$lg_tab    = '';
	$T_data    = $Totoro->Pdo->query($sql_deb)->fetch(PDO::FETCH_ASSOC);
	$val_stk_deb = $T_data['SOMME'];
	$T_data    = $Totoro->Pdo->query($sql_fin)->fetch(PDO::FETCH_ASSOC);
	$val_stk_fin = $T_data['SOMME'];
	$variation = $val_stk_fin - $val_stk_deb;
	$lg_tab   .= '<tr> ';
	$lg_tab   .= '<td>'.$prestation.'</td> ';
	$lg_tab   .= '<td>'.$etat.'</td> ';
	$lg_tab   .= '<td align=right>'.number_format($val_stk_deb, 2, ',', ' ').'</td> ';
	$lg_tab   .= '<td align=right>'.number_format($val_stk_fin, 2, ',', ' ').'</td> ';
	$lg_tab   .= '<td align=right>'.number_format($variation  , 2, ',', ' ').'</td> ';
	$lg_tab   .= '<td align=right> </td> ';
	$lg_tab   .= '<td align=right> </td> ';
	$lg_tab   .= '</tr> ';
	$somme_stk_deb += $val_stk_deb;
	$somme_stk_fin += $val_stk_fin;

	return $lg_tab; // renvoie la ligne complette de tableau en html
} 

function lg_tab_separateur($titre, $option=false)
{
	$lg_tab    = '';
	$lg_tab   .= '<tr class="table-info"><td colspan=7><b>'.$titre.'</b></td></tr> ';

	return $lg_tab; // renvoie la ligne complette de tableau en html
} 

function lg_tab_sous_tot_html($titre, $etat, $ca, $ca_1,$mc)
{
	$lg_tab    = '';
	$bold_on = '<b>'; $bold_off = '</b>';
	$lg_tab   .= '<tr> ';
	$lg_tab   .= '<td>'.$bold_on.$titre.$bold_off.'</td> ';
	$lg_tab   .= '<td>'.$bold_on.$etat.$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($ca, 2, ',', ' ').$bold_off.'</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($ca_1, 2, ',', ' ').$bold_off.'</td> ';
	$var = (($ca/$ca_1)-1) * 100;
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($var, 2, ',', ' ').$bold_off.' %</td> ';
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($mc, 2, ',', ' ').$bold_off.' %</td> ';
	$mcp = $mc/$ca;
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($mcp, 2, ',', ' ').$bold_off.' %</td> ';
	$lg_tab   .= '</tr> ';
	return $lg_tab; // renvoie la ligne complette de tableau en html
} 


//declaration des variables diverses : 
$debug_info = $tab_html = $tab_html_1 = $tab_html_2 = $tab_html_3 = $tab_html_4 = $tab_html_achat = $tab_html_stock = '';

// recuperations des GET ou POST
$date_debut     = get_post('date_debut', 1, 'GETPOST');
$date_fin       = get_post('date_fin', 1, 'GETPOST');

// dates par default 
$date = new DateTime();
$date_debut_mec = $date->format('Y-m-01');  // mec c'est mois en cours
$date_fin_mec   = $date->format('Y-m-d');
if(!$date_debut) $date_debut = $date_debut_mec;
if(!$date_fin)   $date_fin   = $date_fin_mec;
$date_jour     = $date->format('Y-m-d');

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
$sql_w_occ        = "AND cmdl__etat <> 'NEU' ";
$sql_w_cdb        = "AND keyword.kw__info = 'CDB' ";
$sql_w_imp        = "AND keyword.kw__info IN ('ILM', 'IT') ";
$sql_w_mic        = "AND keyword.kw__info = 'MICRO' ";
$sql_w_con        = "AND keyword.kw__info = 'CONSO' ";
$sql_w_pid        = "AND keyword.kw__info = 'PID' ";
$sql_w_acc        = "AND keyword.kw__info = 'ACC' ";
$sql_w_ser        = "AND keyword.kw__info IN ('SER','XX') ";
$sql_w_no_con     = "AND keyword.kw__info <> 'CONSO' ";
// Totoro (TQL) c'est bien du sql mais c'est pour les differencier
$tql_select_achat = "SELECT SUM(pu_ht) AS SOMME ";
$tql_from         = "FROM locator ";
$tql_from        .= "LEFT JOIN articles2 ON locator.article = articles2.art_model ";
$tql_from        .= "LEFT JOIN keyword ON articles2.art_type = keyword.keyword AND keyword.type = 'supfa' ";
$tql_where        = "WHERE ";
$tql_where       .= "in_datetime BETWEEN '".$date_debut." 00:00:00' AND '".$date_fin." 23:59:59' ";
$tql_in_neuf      = "id_etat IN (1) ";
$tql_in_occ       = "id_etat IN (0,11,21,22) ";
$tql_in_conso     = "id_etat IN (31,32,1) ";
$tql_w_achat      = "AND in_prest = 'Achat' ";
$tql_w_neuf       = "AND ".$tql_in_neuf;
$tql_w_occ        = "AND ".$tql_in_occ;
$tql_w_conso      = "AND ".$tql_in_conso;
$tql_w_sf_acc     = "AND keyword.`value` = 'ACC' ";
$tql_w_sf_cdb     = "AND keyword.`value` = 'CDB' ";
$tql_w_sf_conso   = "AND keyword.`value` = 'CONSO' ";
$tql_w_sf_autre   = "AND ( keyword.`value` IN ('GARAN','EMBAL') OR keyword.`value` IS NULL ) ";
$tql_w_sf_imp     = "AND keyword.`value` = 'IMP' ";
$tql_w_sf_micro   = "AND keyword.`value` = 'MICRO' ";
$tql_w_sf_pce     = "AND keyword.`value` = 'PCE' ";
$tql_sel_po_port  = "SELECT SUM(po_frais_port_euro) AS SOMME ";
$tql_from_po      = "FROM po ";
$tql_where_po     = "WHERE ";
$tql_where_po    .= "po_dt_cmd BETWEEN '".$date_debut."' AND '".$date_fin."' ";
$tql_stk_sql      = "SELECT sum(locator.pu_ht) AS SOMME FROM locator ";
$tql_stk_sql     .= "LEFT JOIN articles2 ON locator.article = articles2.art_model ";
$tql_stk_sql     .= "LEFT JOIN keyword ON articles2.art_type = keyword.keyword AND keyword.type = 'supfa' ";
$tql_stk_sql     .= "WHERE ";
$tql_stk_dt       = "( in_datetime < '##DT##' AND (out_datetime is NULL OR out_datetime >= '##DT##') ) "; // les ##DT## sont remplacé par date fin et debut dans la fonction lg_tab_stock_html
$tql_stk_neuf     = "AND ( id_etat = 1 ) ";
$tql_stk_occ      = "AND ( id_etat in (0,11) ) ";
$tql_stk_mrk      = "AND ( id_etat = 31 ) ";
$tql_stk_com      = "AND ( id_etat = 32 ) ";
$tql_stk_type     = "AND keyword.`value` = ";


/*ooooooo.                                                  .                            .oooooo..o            oooo 
`888   `Y88.                                              .o8                           d8P'    `Y8            `888 
 888   .d88'  .ooooo.   .ooooo oo oooo  oooo   .ooooo.  .o888oo  .ooooo.   .oooo.o      Y88bo.       .ooooo oo  888 
 888ooo88P'  d88' `88b d88' `888  `888  `888  d88' `88b   888   d88' `88b d88(  "8       `"Y8888o.  d88' `888   888 
 888`88b.    888ooo888 888   888   888   888  888ooo888   888   888ooo888 `"Y88b.            `"Y88b 888   888   888 
 888  `88b.  888    .o 888   888   888   888  888    .o   888 . 888    .o o.  )88b      oo     .d8P 888   888   888 
o888o  o888o `Y8bod8P' `V8bod888   `V88V"V8P' `Y8bod8P'   "888" `Y8bod8P' 8""888P'      8""88888P'  `V8bod888  o888o
                             888.                                                                         888.
                             8P'                                                                          8P'
                             "                                                                            */

$v01   = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_no_con);
$v01b  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_cdb);
$v01c  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_imp);
$v01d  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_mic);
$v01f  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_pid);
$v01g  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_acc);
$v01h  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_ser);
$v02   = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_no_con);
$v02b  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_cdb);
$v02c  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_imp);
$v02d  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_mic);
$v02f  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_pid);
$v02g  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_acc);
$v02h  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_ser);
$v01e  = stat_vente($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_con);
$v03   = stat_vente($sql_select_vente.$sql_from.$sql_where.$sql_w_ech.$sql_w_neu);
$v04   = stat_vente($sql_select_vente.$sql_from.$sql_where.$sql_w_ech.$sql_w_occ);
$v05   = stat_vente($sql_select_vente.$sql_from.$sql_where.$sql_w_rpr.$sql_w_neu);
$v05b  = stat_vente($sql_select_vente.$sql_from.$sql_where.$sql_w_rpr.$sql_w_occ);
$v06   = stat_vente($sql_select_vente.$sql_from.$sql_where.$sql_w_rem);
$v07b  = stat_vente($sql_select_vente.$sql_from.$sql_where.$sql_w_pre);
$v07c  = stat_vente($sql_select_vente.$sql_from.$sql_where.$sql_w_bro);

// debug
// print '<br>v01<br>'; var_dump($v01); print '<br>';



//  dP""b8    db
// dP   `"   dPYb
// Yb       dP__Yb
//  YboodP dP""""Yb

$titre_stat  = 'Chiffre d\'affaires facturé pour etude de marges';
$gras = 1;
$achat = 2;
$tab_html_1 .= lg_tab_separateur('Ventes de marchandises');
$tab_html_3 .= lg_tab_html_2($v01  ,"01 Ventes (Tout)","Neuf",$gras);
$tab_html_3 .= lg_tab_html_2($v01b ,"01b Ventes CDB","Neuf");
$tab_html_3 .= lg_tab_html_2($v01c ,"01c Ventes IMPRIMANTES","Neuf");
$tab_html_3 .= lg_tab_html_2($v01d ,"01d Ventes MICRO","Neuf");
$tab_html_3 .= lg_tab_html_2($v01f ,"01f Ventes Pieces détachées","Neuf");
$tab_html_3 .= lg_tab_html_2($v01g ,"01g Ventes Accessoires","Neuf");
$tab_html_3 .= lg_tab_html_2($v01h ,"01h Ventes Services","Neuf");
$tab_html_3 .= lg_tab_html_2($v02  ,"02 Ventes (Tout)","Occasion",$gras);
$tab_html_3 .= lg_tab_html_2($v02b ,"02b Ventes CDB","Occasion");
$tab_html_3 .= lg_tab_html_2($v02c ,"02c Ventes IMPRIMANTES","Occasion");
$tab_html_3 .= lg_tab_html_2($v02d ,"02d Ventes MICRO","Occasion");
$tab_html_3 .= lg_tab_html_2($v02f ,"02f Ventes Pieces détachées","Occasion");
$tab_html_3 .= lg_tab_html_2($v02g ,"02g Ventes Accessoires","Occasion");
$tab_html_3 .= lg_tab_html_2($v02h ,"02h Ventes Services","Occasion");
$tab_html_3 .= lg_tab_html_2($v01e ,"01e Ventes CONSO","Marque et comp",$gras);
$tab_html_3 .= lg_tab_html_2($v03  ,"03 Echange matériels","Neuf",$gras);
$tab_html_3 .= lg_tab_html_2($v04  ,"04 Echange matériels","Occasion",$gras);
$tab_html_3 .= lg_tab_html_2($v05  ,"05 Reprise","Neuf",$gras);
$tab_html_3 .= lg_tab_html_2($v05b ,"05b Reprise","Occasion",$gras);
$tab_html_3 .= lg_tab_html_2($v06  ,"06 Remise","NC.",$gras);
$tab_html_3 .= lg_tab_html_2($v07b ,"07b Prêt","Tout état",$gras);
$tab_html_3 .= lg_tab_html_2($v07c ,"07c Broke","Tout état",$gras);


$tab_html_1 .= lg_tab_sous_tot_html('Ventes de marchandises', 'TOTAL', $stv, $stv_1 ,$mc);
$tab_html   .= $tab_html_1.$tab_html_2.$tab_html_3;

// $tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_neuf,"08 Achats matériels","Neuf","totoro");
// $tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_occ,"09 Achats matériels","Occasion ou n.a.","totoro");
// $tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_conso,"09b Achats matériels","Consommables","totoro");
// $tab_html .= lg_tab_html($tql_sel_po_port.$tql_from_po.$tql_where_po,"10 & 11 Transport matériels","Tout état","totoro");
$tab_html .= lg_tab_separateur('Production');
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_rep,"12 Réparation","NC.","sosuke",$gras);
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_loc.$sql_w_neu,"13 Location","Neuf","sosuke",$gras);
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_loc.$sql_w_occ,"14 Location","Occasion","sosuke",$gras);
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_mnt,"15 Maintenance","NC.","sosuke",$gras);
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_int,"16 Intervention et Dep.","NC.","sosuke",$gras);
$tab_html .= lg_tab_html($sql_select_v_gar.$sql_from.$sql_where.$sql_w_neu,"17 Extension de garantie","Neuf","sosuke",$gras);
$tab_html .= lg_tab_html($sql_select_v_gar.$sql_from.$sql_where.$sql_w_occ,"18 Extension de garantie","Occasion ou NC.","sosuke",$gras);
$tab_html .= lg_tab_separateur('Transport sur vente');
$tab_html .= lg_tab_html($sql_select_vente.$sql_from.$sql_where.$sql_w_prt,"19 Port facturé","NC.","sosuke",$gras);
// $tab_html .= lg_tab_separateur('Stock');
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_deb_neuf,"21 Stock début","Neuf","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_deb_occ,"22 Stock début","Occasion","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_fin_neuf,"23 Stock fin","Neuf","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_fin_occ,"24 Stock fin","Occasion","totoro");

$tab_html_achat .= lg_tab_separateur('Achats de marchandises');
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_cdb.$tql_w_neuf,"01b Achats CDB","Neuf","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_imp.$tql_w_neuf,"01c Achats Imprimante","Neuf","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_micro.$tql_w_neuf,"01d Achats Micro","Neuf","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_pce.$tql_w_neuf,"01f Achats Pieces","Neuf","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_acc.$tql_w_neuf,"01g Achats Accessoires","Neuf","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_autre.$tql_w_neuf,"01x Achats Autre (NC, Embal, Gar)","Neuf","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_cdb.$tql_w_occ,"02b Achats CDB","Occasion","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_imp.$tql_w_occ,"02c Achats Imprimante","Occasion","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_micro.$tql_w_occ,"02d Achats Micro","Occasion","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_pce.$tql_w_occ,"02f Achats Pieces","Occasion","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_acc.$tql_w_occ,"02gxx Achats Accessoires","Occasion","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_autre.$tql_w_occ,"02x Achats Autre (NC, Embal, Gar)","Occasion","totoro",$achat);
$tab_html_achat .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_conso.$tql_w_conso,"01e Achats Conso","Marque & comp.","totoro",$achat);

/*P"Y8 888888  dP"Yb   dP""b8 88  dP 
`Ybo."   88   dP   Yb dP   `" 88odP  
o.`Y8b   88   Yb   dP Yb      88"Yb  
8bodP'   88    YbodP   YboodP 88  Y*/ 
$tab_html_stock .= lg_tab_separateur('Stock de marchandise');
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'CDB'",  "51b Stock CDB","Neuf");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'IMP'",  "51c Stock Imprimante","Neuf");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'MICRO'","51d Stock Micro","Neuf");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'PCE'",  "51f Stock Pièces détaché","Neuf");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'ACC'",  "51g Stock Accessoires","Neuf");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'CDB'",  "52b Stock CDB","Occasion");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'IMP'",  "52c Stock Imprimante","Occasion");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'MICRO'","52d Stock Micro","Occasion");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'PCE'",  "52f Stock Pièces détaché","Occasion");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'ACC'",  "52g Stock Accessoires","Occasion");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_mrk.$tql_stk_type."'CONSO'","54a Stock Consomable","Marque");
$tab_html_stock .= lg_tab_stock_html($tql_stk_sql.$tql_stk_dt.$tql_stk_com.$tql_stk_type."'CONSO'","55a Stock Consomable","Compatible");



$debug_info = '<br>';
$debug_info = $tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'CDB'";
$somme_ca_evol = (($somme_ca-$somme_ca_1)/$somme_ca_1)*100;
$somme_achat_evol = (($somme_achat-$somme_achat_1)/$somme_achat_1)*100;
$somme_stk_evol = $somme_stk_fin - $somme_stk_deb;

// Donnée transmise au template : 
echo $twig->render('statistique_marge_v2.twig',
[
'date_debut'       => $date_debut, 
'date_fin'         => $date_fin,
'date_debut_mec'   => $date_debut_mec, 
'date_fin_mec'     => $date_fin_mec,
'date_jour'        => $date_jour,
'date_debut_fr'    => $date_debut_fr, 
'date_fin_fr'      => $date_fin_fr,
'titre_stat'       => $titre_stat,
'debug_info'       => $debug_info,
'tab_html'         => $tab_html,
'tab_html_achat'   => $tab_html_achat,
'tab_html_stock'   => $tab_html_stock,
'somme_ca'         => number_format($somme_ca, 2, ',', ' '),
'somme_ca_1'       => number_format($somme_ca_1, 2, ',', ' '),
'somme_ca_evol'    => number_format($somme_ca_evol, 2, ',', ' '),
'somme_achat'      => number_format($somme_achat, 2, ',', ' '),
'somme_achat_1'    => number_format($somme_achat_1, 2, ',', ' '),
'somme_achat_evol' => number_format($somme_achat_evol, 2, ',', ' '),
'somme_stk_deb'    => number_format($somme_stk_deb, 2, ',', ' '),
'somme_stk_fin'    => number_format($somme_stk_fin, 2, ',', ' '),
'somme_stk_evol'   => number_format($somme_stk_evol, 2, ',', ' ')
]);
