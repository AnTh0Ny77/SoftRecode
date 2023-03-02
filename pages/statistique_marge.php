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
	$v11  = $v12  = $v13  = $v14  = $v15  = $v16  = $v17  = $v18  = $v19  = array();
	$a01b = $a01c = $a01d = $a01f = $a01g = $a01x = array();
	$a02b = $a02c = $a02d = $a02f = $a02g = $a02x = $a02e = array();
	$s51b = $s51c = $s51d = $s51f = $s51g = array();
	$s52b = $s52c = $s52d = $s52f = $s52g = $s54a = $s55a = array();


/*8888  dP"Yb  88b 88  dP""b8 888888 88  dP"Yb  88b 88 .dP"Y8 
88__   dP   Yb 88Yb88 dP   `"   88   88 dP   Yb 88Yb88 `Ybo." 
88""   Yb   dP 88 Y88 Yb        88   88 Yb   dP 88 Y88 o.`Y8b 
88      YbodP  88  Y8  YboodP   88   88  YbodP  88  Y8 8bodP*/ 

function sql_1_an($sql)
{ 
	$search  = array('2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030');
	$replace = array('2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029');
	$sql_1   = str_replace($search, $replace, $sql);
	return $sql_1; // indique la valeur à renvoyer 
} 


function stat_sql($sql, $base='sosuke')
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

function stock_sql($sql)
{
	global $Totoro;
	global $date_debut, $date_fin;
	$ret = array();
	$sql_deb = str_replace('##DT##',$date_debut.' 00:00:00', $sql);
	$sql_fin = str_replace('##DT##',$date_fin.' 23:59:59', $sql);
	$lg_tab    = '';
	$T_data    = $Totoro->Pdo->query($sql_deb)->fetch(PDO::FETCH_ASSOC);
	$val_stk_deb = $T_data['SOMME'];
	$T_data    = $Totoro->Pdo->query($sql_fin)->fetch(PDO::FETCH_ASSOC);
	$val_stk_fin = $T_data['SOMME'];
	$ret[0] = $val_stk_deb;
	$ret[1] = $val_stk_fin;
	return $ret; // renvoie le tableau avec chiffre n et n-1
} 



function lg_tab_html($nn1, $prestation, $etat, $option=FALSE, $variation=FALSE)
{
	$somme   = $nn1[0];
	$somme_1 = $nn1[1];
	if (isset($nn1[2]))
		$mc  = $nn1[2];
	else 
		$mc  = FALSE;
	$mc_pc = ' ';
	if ($variation) // variation en valeur et non en %
		{ $evol = $somme_1 - $somme; $signe_var = ''; }
	else
	{ 
		$signe_var = '%'; 
		if ($somme_1 > 0) // pour eviter le divizion par 0
			$evol = (($somme-$somme_1)/$somme_1)*100;
		else
			$evol = '0';
	}
	$bold = FALSE;
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
	$lg_tab   .= '<td align=right>'.$bold_on.number_format($evol, 2, ',', ' ').$bold_off.' '.$signe_var.'</td> ';
	$lg_tab   .= '<td align=right>';
	if ($mc !== FALSE)
	{
		$lg_tab   .= $bold_on.number_format($mc, 2, ',', ' ').$bold_off;
		if ($somme > 0) // pour eviter Division by zero
			$mc_pc = $bold_on.number_format(($mc / $somme)*100, 2, ',', ' ').' %'.$bold_off;
		else
			$mc_pc = '-';
	}
	$lg_tab   .= '</td> ';
	$lg_tab   .= '<td align=right>'.$mc_pc.'</td> ';
	$lg_tab   .= '</tr> ';

	return $lg_tab; // renvoie la ligne complette de tableau en html
} 

function lg_tab_desc($t1,$t2,$t3,$t4,$t5,$t6,$t7)
{
	$lg_tab    = '<tr> ';
	$lg_tab   .= '<td style="text-align:left "><b>'.$t1.'</b></td> ';
	$lg_tab   .= '<td style="text-align:left "><b>'.$t2.'</b></td> ';
	$lg_tab   .= '<td style="text-align:right"><b>'.$t3.'</b></td> ';
	$lg_tab   .= '<td style="text-align:right"><b>'.$t4.'</b></td> ';
	$lg_tab   .= '<td style="text-align:right"><b>'.$t5.'</b></td> ';
	$lg_tab   .= '<td style="text-align:right"><b>'.$t6.'</b></td> ';
	$lg_tab   .= '<td style="text-align:right"><b>'.$t7.'</b></td> ';
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


function lg_tab_titre($nn1, $titre, $class, $variation=FALSE)
{
	$lg_tab    = '';
	$somme   = $nn1[0];
	$somme_1 = $nn1[1];
	if (isset($nn1[2]))
		$mc  = $nn1[2];
	else 
		$mc  = FALSE;
	if ($variation) // variation en valeur et non en %
		{ $evol = $somme_1 - $somme; $signe_var = ''; }
	else
		{ $evol = (($somme - $somme_1)/ $somme_1 ) * 100; $signe_var = '%'; }
	$lg_tab   .= '
	<thead>
	<tr class="table-'.$class.' h4" >
		<th>'.$titre.'</th>
		<th></th>
		<th style="text-align:right">'.number_format($somme, 2, ',', ' ').'</th>
		<th style="text-align:right">'.number_format($somme_1, 2, ',', ' ').'</th>
		<th style="text-align:right">'.number_format($evol, 2, ',', ' ').' '.$signe_var.'</th>
		<th style="text-align:right">';
		if ($mc !== FALSE)
			$lg_tab   .= $bold_on.number_format($mc, 2, ',', ' ').$bold_off;
		$lg_tab .= '
		</th>
		<th style="text-align:right"></th>
	</tr>
	</thead>';

	return $lg_tab; // renvoie la ligne complette de tableau en html
}

function lg_tab_separateur($titre, $class='info')
{
	$lg_tab    = '';
	$lg_tab   .= '<tr class="table-'.$class.'"><td colspan=7><b>'.$titre.'</b></td></tr> ';

	return $lg_tab; // renvoie la ligne complette de tableau en html
} 


//declaration des variables diverses : 
$debug_info = $tab_html = '';

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

$v01   = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_no_con);
$v01b  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_cdb);
$v01c  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_imp);
$v01d  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_mic);
$v01f  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_pid);
$v01g  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_acc);
$v01h  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_neu.$sql_w_ser);
$v02   = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_no_con);
$v02b  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_cdb);
$v02c  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_imp);
$v02d  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_mic);
$v02f  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_pid);
$v02g  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_acc);
$v02h  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_occ.$sql_w_ser);
$v01e  = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_con);
$v03   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_ech.$sql_w_neu);
$v04   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_ech.$sql_w_occ);
$v05   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_rpr.$sql_w_neu);
$v05b  = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_rpr.$sql_w_occ);
$v06   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_rem);
$v07b  = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_pre);
$v07c  = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_bro);
//$v11   = stat_sql($sql_select_vente.$sql_from.$sql_famille.$sql_where.$sql_w_vte.$sql_w_ser); // les services pas une bonne idéede le déplacer ... c'est une vente en mode vente et non en mode service. composé de v01h et v02h
$v12   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_rep);
$v13   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_loc.$sql_w_neu);
$v14   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_loc.$sql_w_occ);
$v15   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_mnt);
$v16   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_int);
$v17   = stat_sql($sql_select_v_gar.$sql_from.$sql_where.$sql_w_neu);
$v18   = stat_sql($sql_select_v_gar.$sql_from.$sql_where.$sql_w_occ);
$v19   = stat_sql($sql_select_vente.$sql_from.$sql_where.$sql_w_prt);
$a01b  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_cdb.$tql_w_neuf,"totoro");
$a01c  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_imp.$tql_w_neuf,"totoro");
$a01d  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_micro.$tql_w_neuf,"totoro");
$a01f  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_pce.$tql_w_neuf,"totoro");
$a01g  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_acc.$tql_w_neuf,"totoro");
$a01x  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_autre.$tql_w_neuf,"totoro");
$a02b  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_cdb.$tql_w_occ,"totoro");
$a02c  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_imp.$tql_w_occ,"totoro");
$a02d  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_micro.$tql_w_occ,"totoro");
$a02f  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_pce.$tql_w_occ,"totoro");
$a02g  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_acc.$tql_w_occ,"totoro");
$a02x  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_autre.$tql_w_occ,"totoro");
$a01e  = stat_sql($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_sf_conso.$tql_w_conso,"totoro");

$s51b  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'CDB'");
$s51c  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'IMP'");
$s51d  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'MICRO'");
$s51f  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'PCE'");
$s51g  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_neuf.$tql_stk_type."'ACC'");
$s52b  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'CDB'");
$s52c  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'IMP'");
$s52d  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'MICRO'");
$s52f  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'PCE'");
$s52g  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_occ.$tql_stk_type."'ACC'");
$s54a  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_mrk.$tql_stk_type."'CONSO'");
$s55a  = stock_sql($tql_stk_sql.$tql_stk_dt.$tql_stk_com.$tql_stk_type."'CONSO'");



 /*""b8    db    88      dP""b8 88   88 88     
dP   `"   dPYb   88     dP   `" 88   88 88     
Yb       dP__Yb  88  .o Yb      Y8   8P 88  .o 
 YboodP dP""""Yb 88ood8  YboodP `YbodP' 88ood*/
$gras = $variation = 1;
// Ventes Marchandise
$tot_vm = array();
$tot_vm[0] = $v01[0]+$v02[0]+$v01e[0]+$v03[0]+$v04[0]+$v05[0]+$v05b[0]+$v06[0]+$v07b[0]+$v07c[0];
$tot_vm[1] = $v01[1]+$v02[1]+$v01e[1]+$v03[1]+$v04[1]+$v05[1]+$v05b[1]+$v06[1]+$v07b[1]+$v07c[1];
// production
$tot_pr = array();
$tot_pr[0] = $v12[0]+$v13[0]+$v14[0]+$v15[0]+$v16[0]+$v17[0]+$v18[0]+$v19[0];
$tot_pr[1] = $v12[1]+$v13[1]+$v14[1]+$v15[1]+$v16[1]+$v17[1]+$v18[1]+$v19[1];
// total CA
$tot_ca = array();
$tot_ca[0] = $tot_vm[0]+$tot_pr[0];
$tot_ca[1] = $tot_vm[1]+$tot_pr[1];
// achat Marchandise neuf
$tot_hn = array();
$tot_hn[0] = $a01b[0]+$a01c[0]+$a01d[0]+$a01f[0]+$a01g[0]+$a01x[0];
$tot_hn[1] = $a01b[1]+$a01c[1]+$a01d[1]+$a01f[1]+$a01g[1]+$a01x[1];
// achat Marchandise occase
$tot_ho = array();
$tot_ho[0] = $a02b[0]+$a02c[0]+$a02d[0]+$a02f[0]+$a02g[0]+$a02x[0];
$tot_ho[1] = $a02b[1]+$a02c[1]+$a02d[1]+$a02f[1]+$a02g[1]+$a02x[1];
// achat total
$tot_ha = array();
$tot_ha[0] = $tot_hn[0]+$tot_ho[0]+$a01e[0];
$tot_ha[1] = $tot_hn[1]+$tot_ho[1]+$a01e[1];
// stock Marchandise neuf
$tot_sn = array();
$tot_sn[0] = $s51b[0]+$s51c[0]+$s51d[0]+$s51f[0]+$s51g[0];
$tot_sn[1] = $s51b[1]+$s51c[1]+$s51d[1]+$s51f[1]+$s51g[1];
// stock Marchandise occasion
$tot_so = array();
$tot_so[0] = $s52b[0]+$s52c[0]+$s52d[0]+$s52f[0]+$s52g[0];
$tot_so[1] = $s52b[1]+$s52c[1]+$s52d[1]+$s52f[1]+$s52g[1];
// stock Marchandise consommables
$tot_sc = array();
$tot_sc[0] = $s54a[0]+$s55a[0];
$tot_sc[1] = $s54a[1]+$s55a[1];
// stock Marchandise total
$tot_st = array();
$tot_st[0] = $tot_sc[0]+$tot_sn[0]+$tot_so[0];
$tot_st[1] = $tot_sc[1]+$tot_sn[1]+$tot_so[1];

/*    d8    db    88""Yb  dP""b8 888888      dP""b8  dP"Yb  8b    d8 
88b  d88   dPYb   88__dP dP   `" 88__       dP   `" dP   Yb 88b  d88 
88YbdP88  dP__Yb  88"Yb  Yb  "88 88""       Yb      Yb   dP 88YbdP88 
88 YY 88 dP""""Yb 88  Yb  YboodP 888888      YboodP  YbodP  88 YY 8*/ 

$v01e[2] = $v01e[0] - $a01e[0] + ($tot_sc[1] - $tot_sc[0]);
$v02g[2] = $v02g[0] - $a02g[0] + ($s52g[1] - $s52g[0]);
$v02f[2] = $v02f[0] - $a02f[0] + ($s52f[1] - $s52f[0]);
$v02d[2] = $v02d[0] - $a02d[0] + ($s52d[1] - $s52d[0]);
$v02c[2] = $v02c[0] - $a02c[0] + ($s52c[1] - $s52c[0]);
$v02b[2] = $v02b[0] - $a02b[0] + ($s52b[1] - $s52b[0]);
$v02[2]  = $v02[0]  - $tot_ho[0] + ($tot_so[1] - $tot_so[0]);
$v01g[2] = $v01g[0] - $a01g[0] + ($s51g[1] - $s51g[0]);
$v01f[2] = $v01f[0] - $a01f[0] + ($s51f[1] - $s51f[0]);
$v01d[2] = $v01d[0] - $a01d[0] + ($s51d[1] - $s51d[0]);
$v01c[2] = $v01c[0] - $a01c[0] + ($s51c[1] - $s51c[0]);
$v01b[2] = $v01b[0] - $a01b[0] + ($s51b[1] - $s51b[0]);
$v01[2]  = $v01[0]  - $tot_hn[0] + ($tot_sn[1] - $tot_sn[0]);
$tot_vm[2] = $v01[2] + $v02[2] + $v01e[2];

// debug
// print '<br>v02g.2<br>'; var_dump($v02g); print '<br>';


   /*    888888 888888 88  dP""b8 88  88    db     dP""b8 888888 
  dPYb   88__   88__   88 dP   `" 88  88   dPYb   dP   `" 88__   
 dP__Yb  88""   88""   88 Yb      888888  dP__Yb  Yb  "88 88""   
dP""""Yb 88     88     88  YboodP 88  88 dP""""Yb  YboodP 88888*/

$titre_stat  = 'Chiffre d\'affaires facturé pour etude de marges';

$tab_html .= lg_tab_titre($tot_ca, 'Total CA', 'success');
$tab_html .= lg_tab_desc('Prestations','Etat','N','N -1','Evolution %','MC','MC en %');

$tab_html .= lg_tab_html($tot_vm ,"Ventes de marchandises","TOTAL", $gras);
$tab_html .= lg_tab_separateur('Vente de marchandises');
	$tab_html .= lg_tab_html($v01    ,"01 Ventes (Tout)","Neuf",$gras);
	$tab_html .= lg_tab_html($v01b   ,"01b Ventes CDB","Neuf");
	$tab_html .= lg_tab_html($v01c   ,"01c Ventes IMPRIMANTES","Neuf");
	$tab_html .= lg_tab_html($v01d   ,"01d Ventes MICRO","Neuf");
	$tab_html .= lg_tab_html($v01f   ,"01f Ventes Pieces détachées","Neuf");
	$tab_html .= lg_tab_html($v01g   ,"01g Ventes Accessoires","Neuf");
	$tab_html .= lg_tab_html($v01h   ,"01h Ventes Services","Neuf");
	$tab_html .= lg_tab_html($v02    ,"02 Ventes (Tout)","Occasion",$gras);
	$tab_html .= lg_tab_html($v02b   ,"02b Ventes CDB","Occasion");
	$tab_html .= lg_tab_html($v02c   ,"02c Ventes IMPRIMANTES","Occasion");
	$tab_html .= lg_tab_html($v02d   ,"02d Ventes MICRO","Occasion");
	$tab_html .= lg_tab_html($v02f   ,"02f Ventes Pieces détachées","Occasion");
	$tab_html .= lg_tab_html($v02g   ,"02g Ventes Accessoires","Occasion");
	$tab_html .= lg_tab_html($v02h   ,"02h Ventes Services","Occasion");
	$tab_html .= lg_tab_html($v01e   ,"01e Ventes CONSO","Marque et comp",$gras);
	$tab_html .= lg_tab_html($v03    ,"03 Echange matériels","Neuf",$gras);
	$tab_html .= lg_tab_html($v04    ,"04 Echange matériels","Occasion",$gras);
	$tab_html .= lg_tab_html($v05    ,"05 Reprise","Neuf",$gras);
	$tab_html .= lg_tab_html($v05b   ,"05b Reprise","Occasion",$gras);
	$tab_html .= lg_tab_html($v06    ,"06 Remise","NC.",$gras);
	$tab_html .= lg_tab_html($v07b   ,"07b Prêt","Tout état",$gras);
	$tab_html .= lg_tab_html($v07c   ,"07c Broke","Tout état",$gras);
$tab_html .= lg_tab_separateur('Production');
//	$tab_html .= lg_tab_html($v11,"11 Service","NC.",$gras);
	$tab_html .= lg_tab_html($v12,"12 Réparation","NC.",$gras);
	$tab_html .= lg_tab_html($v13,"13 Location","Neuf",$gras);
	$tab_html .= lg_tab_html($v14,"14 Location","Occasion",$gras);
	$tab_html .= lg_tab_html($v15,"15 Maintenance","NC.",$gras);
	$tab_html .= lg_tab_html($v16,"16 Intervention et Dep.","NC.",$gras);
	$tab_html .= lg_tab_html($v17,"17 Extension de garantie","Neuf",$gras);
	$tab_html .= lg_tab_html($v18,"18 Extension de garantie","Occasion ou NC.",$gras);
$tab_html .= lg_tab_separateur('Transport sur vente');
	$tab_html .= lg_tab_html($v19,"19 Port facturé","NC.",$gras);
$tab_html .= lg_tab_titre($tot_ca, 'Total CA', 'success');


$tab_html .= lg_tab_separateur('<br><br><br>','light');

$tab_html .= lg_tab_titre($tot_ha, 'Total Achat', 'warning');
$tab_html .= lg_tab_desc('Prestations','Etat','N','N -1','Evolution %','MC','MC en %');

$tab_html .= lg_tab_separateur('Achats de marchandises');
	$tab_html .= lg_tab_html($a01b,"01b Achats CDB","Neuf");
	$tab_html .= lg_tab_html($a01c,"01c Achats Imprimante","Neuf");
	$tab_html .= lg_tab_html($a01d,"01d Achats Micro","Neuf");
	$tab_html .= lg_tab_html($a01f,"01f Achats Pieces","Neuf");
	$tab_html .= lg_tab_html($a01g,"01g Achats Accessoires","Neuf");
	$tab_html .= lg_tab_html($a01x,"01x Achats Autre (NC, Embal, Gar)","Neuf");
	$tab_html .= lg_tab_html($tot_hn ,"Achats (tout)","Neuf", $gras);
	$tab_html .= lg_tab_html($a02b,"02b Achats CDB","Occasion");
	$tab_html .= lg_tab_html($a02c,"02c Achats Imprimante","Occasion");
	$tab_html .= lg_tab_html($a02d,"02d Achats Micro","Occasion");
	$tab_html .= lg_tab_html($a02f,"02f Achats Pieces","Occasion");
	$tab_html .= lg_tab_html($a02g,"02gxx Achats Accessoires","Occasion");
	$tab_html .= lg_tab_html($a02x,"02x Achats Autre (NC, Embal, Gar)","Occasion");
	$tab_html .= lg_tab_html($tot_ho ,"Achats (tout)","Occasion", $gras);
	$tab_html .= lg_tab_html($a01e,"01e Achats Conso","Marque & comp.");
$tab_html .= lg_tab_titre($tot_ha, 'Total Achat', 'warning');


$tab_html .= lg_tab_separateur('<br><br><br>','light');

$tab_html .= lg_tab_titre($tot_st, 'Total Stock', 'primary', $variation);
$tab_html .= lg_tab_desc('Matériel','Etat','Stock Début période','Stock Fin Période','Variation','','');

$tab_html .= lg_tab_separateur('Stock de marchandises');
	$tab_html .= lg_tab_html($s51b, "51b Stock CDB","Neuf",FALSE,$variation);
	$tab_html .= lg_tab_html($s51c, "51c Stock Imprimante","Neuf",FALSE,$variation);
	$tab_html .= lg_tab_html($s51d, "51d Stock Micro","Neuf",FALSE,$variation);
	$tab_html .= lg_tab_html($s51f, "51f Stock Pièces détachées","Neuf",FALSE,$variation);
	$tab_html .= lg_tab_html($s51g, "51g Stock Accessoires","Neuf",FALSE,$variation);
	$tab_html .= lg_tab_html($tot_sn, "Stock (tout)","Neuf",$gras,$variation);
	$tab_html .= lg_tab_html($s52b, "52b Stock CDB","Occasion",FALSE,$variation);
	$tab_html .= lg_tab_html($s52c, "52c Stock Imprimante","Occasion",FALSE,$variation);
	$tab_html .= lg_tab_html($s52d, "52d Stock Micro","Occasion",FALSE,$variation);
	$tab_html .= lg_tab_html($s52f, "52f Stock Pièces détachées","Occasion",FALSE,$variation);
	$tab_html .= lg_tab_html($s52g, "52g Stock Accessoires","Occasion",FALSE,$variation);
	$tab_html .= lg_tab_html($tot_so, "Stock (tout)","Occasion",$gras,$variation);
	$tab_html .= lg_tab_html($s54a, "54a Stock Consommable","Marque",FALSE,$variation);
	$tab_html .= lg_tab_html($s55a, "55a Stock Consommable","Compatible",FALSE,$variation);
	$tab_html .= lg_tab_html($tot_sc, "Stock consommables","Marque & Comp.",$gras,$variation);
$tab_html .= lg_tab_titre($tot_st, 'Total Stock', 'primary', $variation);



$debug_info = '<br>';
$somme_stk_evol = $somme_stk_fin - $somme_stk_deb;


// en reserve...
// $tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_neuf,"08 Achats matériels","Neuf","totoro");
// $tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_occ,"09 Achats matériels","Occasion ou n.a.","totoro");
// $tab_html .= lg_tab_html($tql_select_achat.$tql_from.$tql_where.$tql_w_achat.$tql_w_conso,"09b Achats matériels","Consommables","totoro");
// $tab_html .= lg_tab_html($tql_sel_po_port.$tql_from_po.$tql_where_po,"10 & 11 Transport matériels","Tout état","totoro");
// $tab_html .= lg_tab_separateur('Stock');
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_deb_neuf,"21 Stock début","Neuf","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_deb_occ,"22 Stock début","Occasion","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_fin_neuf,"23 Stock fin","Neuf","totoro");
// $tab_html .= lg_tab_html($tql_stk_sql.$tql_stk_fin_occ,"24 Stock fin","Occasion","totoro");




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
'tab_html'         => $tab_html
]);
