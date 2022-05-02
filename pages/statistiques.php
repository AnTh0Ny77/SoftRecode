<?php
session_start();
require "./vendor/autoload.php";             // chargement des pages PHP avec use
require "./App/twigloader.php";              // gestion des pages twig
require "./App/Methods/tools_functions.php"; // fonctions Boites à outils 


 /*ooooo..o     .                 .    o8o               .    o8o                                            
d8P'    `Y8   .o8               .o8    `"'             .o8    `"'                                            
Y88bo.      .o888oo  .oooo.   .o888oo oooo   .oooo.o .o888oo oooo   .ooooo oo oooo  oooo   .ooooo.   .oooo.o 
 `"Y8888o.    888   `P  )88b    888   `888  d88(  "8   888   `888  d88' `888  `888  `888  d88' `88b d88(  "8 
     `"Y88b   888    .oP"888    888    888  `"Y88b.    888    888  888   888   888   888  888ooo888 `"Y88b.  
oo     .d8P   888 . d8(  888    888 .  888  o.  )88b   888 .  888  888   888   888   888  888    .o o.  )88b 
8""88888P'    "888" `Y888""8o   "888" o888o 8""888P'   "888" o888o `V8bod888   `V88V"V8P' `Y8bod8P' 8""888P' 
                                                                         888.
                                                                         8P'
                                                                         */

//validation Login et droits
if (empty($_SESSION['user']->id_utilisateur))                     header('location: login');
if ($_SESSION['user']->user__facture_acces < 10 ) header('location: noAccess');

//déclaration des instances nécéssaires :
$user      = $_SESSION['user'];
$Database  = new App\Database('devis');
$Database->DbConnect();
$Totoro    = new App\Totoro('euro');
$Totoro->DbConnect();
// $Keyword   = new App\Tables\Keyword($Database);
// $Client    = new App\Tables\Client($Database);
// $Contact   = new App\Tables\Contact($Database);
// $Cmd       = new App\Tables\Cmd($Database);
// $General   = new App\Tables\General($Database);
// $Article   = new App\Tables\Article($Database);
$UserClass = new App\Tables\User($Database);
// $Pisteur   = new App\Tables\Pistage($Database);
// $Stat      = new App\Tables\Stats($Database);


//recupération des listes nécéssaires : 
// $clientList       = $Client->getAll();      // pas pour le moment
$clientList = FALSE;
// $articleTypeList  = $Article->getModels();  // pas pour le moment
$articleTypeList = FALSE;
$vendeurList      = $UserClass->getCommerciaux();

//declaration des variables diverses : 
$alertDate = $resultHt = $NombreCmd = $chartsResponses = $chartsVendeur = $arrayPresta = FALSE;
$chiffre_cmd_fact = $maintenance_location = $cmdSearch = $abnSearch = FALSE;
$desc_stat = $type_tot = $titre_stat = $SQL_stat = $liste_fiche_lp = $liste_fiche = $liste_fiche_gm = $liste_fiche_rma = '';
$client = $vendeur = 'Tous';
$total_ca = $nb_fiche = $vs_fiche = $vs_fiche_lp = $vs_down = $vs_periode = $vs_garmaint = $tot_ca_veir = $tot_stk_deb = $tot_stk_fin = 0;

// recuperations des GET ou POST
$date_debut     = get_post('date_debut', 1, 'GETPOST');
$date_fin       = get_post('date_fin', 1, 'GETPOST');
$id_client      = get_post('id_client', 1, 'GETPOST');
$id_vendeur     = get_post('id_vendeur', 1, 'GETPOST');
$COS            = get_post('COS', 2, 'GETPOST'); // Commandes Sans Maintenance
$CAS            = get_post('CAS', 2, 'GETPOST'); // Commandes Avec Maintenances
$MNT            = get_post('MNT', 2, 'GETPOST'); // Maintenance
$CAM            = get_post('CAM', 2, 'GETPOST'); // CA SANS Maintenance
$VOL            = get_post('VOL', 2, 'GETPOST'); // Volant d'activitée
if(!$COS AND !$CAS AND !$MNT AND !$CAM AND !$VOL) // si rien de precisé je choisi par def CAS (ca Sans maintenace)
	$CAM = TRUE;

// dates par default 
$date = new DateTime();
if(!$date_debut) $date_debut = $date->format('Y-m-01');
if(!$date_fin)   $date_fin   = $date->format('Y-m-t');

// Date format calendrier
$date_debut_fr = date_format(date_create($date_debut),'d/m/Y');
$date_fin_fr   = date_format(date_create($date_fin)  ,'d/m/Y');

// filtres client / vendeur
if ($id_client)
	$client  = $Client->getOne($id_client);
if ($id_vendeur) 
	$vendeur = $UserClass->getByID($id_vendeur);

/*8888 Yb  dP 88""Yb 88     88  dP""b8    db    888888 88  dP"Yb  88b 88 .dP"Y8 
88__    YbdP  88__dP 88     88 dP   `"   dPYb     88   88 dP   Yb 88Yb88 `Ybo." 
88""    dPYb  88"""  88  .o 88 Yb       dP__Yb    88   88 Yb   dP 88 Y88 o.`Y8b 
888888 dP  Yb 88     88ood8 88  YboodP dP""""Yb   88   88  YbodP  88  Y8 8bodP' 

les cumules se fonts pour une periode, pour # critères, presenté par prestations 
avec les extentions de garanties séparé.

la base de select est la meme, le groupement aussi, le where change en fonctions
des type de demandes.

pour info : le BETWEEN doit etre utilisé avec 23:59:59 ou sans precision d'heure pour la limite de fin  
il faut ajouter un jour a la date ($date_fin_plus_un = date("Y-m-d", strtotime($Date.'+ 1 days'));)
****************************************************************************** 
Exemple de requette
SELECT
	sum(cmdl__qte_fact * cmdl__puht) + IFNULL(SUM(cmdl__qte_fact * cmdl__garantie_puht),0) AS total_fiche,
	cmdl__prestation,
	cmdl__etat
FROM
	cmd
LEFT JOIN cmd_ligne ON cmd_ligne.cmdl__cmd__id = cmd.cmd__id
WHERE
	cmd__date_fact BETWEEN '2021-04-01 00:00:00'
AND '2021-04-30 23:59:59'
AND cmd__etat IN ('VLD', 'VLA')
GROUP BY
	cmdl__prestation,
	cmdl__etat
ORDER BY
	cmdl__prestation,
	cmdl__etat
****************************************************************************** */

// Base commune pour creation requette
$sql_select_tabF  = "SELECT sum(cmdl__qte_fact*cmdl__puht) + IFNULL(SUM(cmdl__qte_fact * cmdl__garantie_puht),0) AS total_fiche, ";
$sql_select_tabF .= "cmdl__prestation, key_presta.kw__lib AS presta, key_etat.kw__lib AS etat FROM cmd ";
$sql_select_CA    = "SELECT sum(cmdl__qte_fact * cmdl__puht)  AS tot_ca_ve FROM cmd ";
$sql_select_tabC  = "SELECT sum(cmdl__qte_cmd*cmdl__puht) + IFNULL(SUM(cmdl__qte_cmd * cmdl__garantie_puht),0) AS total_fiche, ";
$sql_select_tabC .= "key_presta.kw__lib AS presta, key_etat.kw__lib AS etat FROM cmd ";
$sql_select_ct    = "SELECT COUNT(DISTINCT(cmd.cmd__id)) AS nb_fiche FROM cmd ";
$sql_select_liste = "SELECT DISTINCT(cmd__id) FROM cmd ";
$sql_select_join  = "LEFT JOIN cmd_ligne ON cmd_ligne.cmdl__cmd__id = cmd.cmd__id ";
$sql_select_join .= "INNER JOIN keyword AS key_etat    ON cmd_ligne.cmdl__etat = key_etat.kw__value AND key_etat.kw__type = 'letat' ";
$sql_select_join .= "INNER JOIN keyword AS key_presta ON cmd_ligne.cmdl__prestation = key_presta.kw__value AND key_presta.kw__type = 'pres' ";
$sql_where        = "WHERE ";
$sql_where_dt     = "BETWEEN '".$date_debut." 00:00:00' AND '".$date_fin." 23:59:59' ";
$sql_where_no_loc = "AND (cmdl__prestation <> 'LOC' AND cmdl__prestation <> 'PRE' AND cmdl__prestation <> 'PRT') ";
$sql_where_loc    = "AND (cmdl__prestation = 'LOC' OR cmdl__prestation = 'PRE') ";
$sql_tot_ca_ve    = "AND (cmdl__prestation IN ('ECH','VTE')) ";
$sql_groupby      = "GROUP BY cmdl__prestation, cmdl__etat ";
$sql_order        = "ORDER BY key_presta.kw__ordre, cmdl__etat ";

//  dP""b8    db           db    Yb    dP 888888  dP""b8        db    88""Yb  dP"Yb  
// dP   `"   dPYb         dPYb    Yb  dP  88__   dP   `"       dPYb   88__dP dP   Yb 
// Yb       dP__Yb       dP__Yb    YbdP   88""   Yb           dP__Yb  88""Yb Yb   dP 
//  YboodP dP""""Yb     dP""""Yb    YP    888888  YboodP     dP""""Yb 88oodP  YbodP  
if ($CAM)
{
	$titre_stat  = 'Chiffre d\'affaires facturé Avec Abonnements (Maint et Loc)';
	$desc_stat   = 'Chiffre d\'affaires (somme CA des fiches) facturé (date de fact.) Avec maintenance (Etat des fiches VLD & VLA) sur la période (prenant en compte les avoirs) (Ce chiffre doit etre le meme que le CA en compta)';
	$type_tot    = 'CAM';
	$cmd_date    = "cmd__date_fact ";
	$cmd_etat    = "AND cmd__etat IN ('VLD','VLA') ";
	// le tableau de data
	$T_sql       = $sql_select_tabF.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat.$sql_groupby.$sql_order;
	$T_request   = $Database->Pdo->query($T_sql);
	$T_data      = $T_request->fetchAll(PDO::FETCH_ASSOC);
	// ca vente et ech sans ext garantie
	$CA_sql      = $sql_select_CA.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat.$sql_tot_ca_ve;
	$CA_request  = $Database->Pdo->query($CA_sql);
	$CA_data     = $CA_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$tot_ca_ve   = $CA_data['tot_ca_ve'];
	// le comptage de fiche
	$C_sql       = $sql_select_ct.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat;
	$C_request   = $Database->Pdo->query($C_sql);
	$C_data      = $C_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$nb_fiche    = $C_data['nb_fiche'];
	// Liste des fiches concernées (sans les fiches avec au moins une ligne de loc ou pret)
	$L_sql       = $sql_select_liste.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$sql_where_no_loc.$cmd_etat;
	$L_request   = $Database->Pdo->query($L_sql);
	$L_data      = $L_request->fetchAll(PDO::FETCH_ASSOC);
	// Liste des fiches avec pret ou loc (les fiches avec au moins une ligne de loc ou pret)
	$LP_sql      = $sql_select_liste.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$sql_where_loc.$cmd_etat;
	$LP_request  = $Database->Pdo->query($LP_sql);
	$LP_data     = $LP_request->fetchAll(PDO::FETCH_ASSOC);
	// Somme du total_ca
	for ($i=0; $i<=count($T_data)-1; $i++) $total_ca += $T_data[$i]['total_fiche'];
}

//  dP""b8    db        .dP"Y8    db    88b 88 .dP"Y8        db    88""Yb  dP"Yb 
// dP   `"   dPYb       `Ybo."   dPYb   88Yb88 `Ybo."       dPYb   88__dP dP   Yb
// Yb       dP__Yb      o.`Y8b  dP__Yb  88 Y88 o.`Y8b      dP__Yb  88""Yb Yb   dP
//  YboodP dP""""Yb     8bodP' dP""""Yb 88  Y8 8bodP'     dP""""Yb 88oodP  YbodP 
if ($CAS)
 {
	$titre_stat  = 'Chiffre d\'affaires facturé sans Abonnement (Maint et Loc) (Fact Automatique)';
	$desc_stat   = 'Chiffre d\'affaires (somme CA des fiches) facturé (date de fact.) sans maintenance (Etat des fiches VLD) sur la période';
	$type_tot    = 'CAS';
	$cmd_date    = "cmd__date_fact ";
	$cmd_etat    = "AND cmd__etat IN ('VLD') ";
	// le tableau de data
	$T_sql       = $sql_select_tabF.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat.$sql_groupby.$sql_order;
	$T_request   = $Database->Pdo->query($T_sql);
	$T_data      = $T_request->fetchAll(PDO::FETCH_ASSOC);
	// le compmtage de fiche
	$C_sql       = $sql_select_ct.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat;
	$C_request   = $Database->Pdo->query($C_sql);
	$C_data      = $C_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$nb_fiche    = $C_data['nb_fiche'];
	// Liste des fiches concernées (sans les fiches avec au moins une ligne de loc ou pret)
	$L_sql       = $sql_select_liste.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$sql_where_no_loc.$cmd_etat;
	$L_request   = $Database->Pdo->query($L_sql);
	$L_data      = $L_request->fetchAll(PDO::FETCH_ASSOC);
	// Liste des fiches avec pret ou loc (les fiches avec au moins une ligne de loc ou pret)
	$LP_sql      = $sql_select_liste.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$sql_where_loc.$cmd_etat;
	$LP_request  = $Database->Pdo->query($LP_sql);
	$LP_data     = $LP_request->fetchAll(PDO::FETCH_ASSOC);
	 // Somme du total_ca
	 for ($i=0; $i<=count($T_data)-1; $i++) $total_ca += $T_data[$i]['total_fiche'];
}

//  dp""b8  dP"Yb  8b    d8 8b    d8    db    88b 88 8888b.  888888 .dP"Y8
// dP   `" dP   Yb 88b  d88 88b  d88   dPYb   88Yb88  8I  Yb 88__   `Ybo."
// Yb      Yb   dP 88YbdP88 88YbdP88  dP__Yb  88 Y88  8I  dY 88""   o.`Y8b
//  YboodP  YbodP  88 YY 88 88 YY 88 dP""""Yb 88  Y8 8888Y"  888888 8bodP'
if ($COS)
{
	$titre_stat  = 'Commandes signées en attente de depart ou deja expédiées';
	$desc_stat   = 'Somme CA des fiches signées (date de cmd.) (Etat des fiches CMD, IMP, VLD) sur la période';
	$type_tot    = 'COS';
	$cmd_date    = "cmd__date_cmd ";
	$cmd_etat    = "AND cmd__etat IN ('VLD','CMD','IMP') ";
	// le tableau de data
	$T_sql       = $sql_select_tabC.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat.$sql_groupby.$sql_order;
	$T_request   = $Database->Pdo->query($T_sql);
	$T_data      = $T_request->fetchAll(PDO::FETCH_ASSOC);
	// Somme du total_ca
	for ($i=0; $i<=count($T_data)-1; $i++) $total_ca += $T_data[$i]['total_fiche'];
	// le compmtage de fiche
	$C_sql       = $sql_select_ct.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat;
	$C_request   = $Database->Pdo->query($C_sql);
	$C_data      = $C_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$nb_fiche    = $C_data['nb_fiche'];
}

// Yb    dP  dP"Yb  88        db    88b 88 888888     
//  Yb  dP  dP   Yb 88       dPYb   88Yb88   88       
//   YbdP   Yb   dP 88  .o  dP__Yb  88 Y88   88       
//    YP     YbodP  88ood8 dP""""Yb 88  Y8   88       
if ($VOL)
{
	$titre_stat  = 'Commandes en production (volant activitée) (pas de date)';
	$desc_stat   = 'Somme CA des fiches signées et non fact. (Etat des fiches CMD, IMP) (Pas de periode)';
	$type_tot    = 'VOL';
	$cmd_etat    = "cmd__etat IN ('CMD','IMP') ";
	// le tableau de data
	$T_sql       = $sql_select_tabC.$sql_select_join.$sql_where.$cmd_etat.$sql_groupby.$sql_order;
	$T_request   = $Database->Pdo->query($T_sql);
	$T_data      = $T_request->fetchAll(PDO::FETCH_ASSOC);
	// Somme du total_ca
	for ($i=0; $i<=count($T_data)-1; $i++) $total_ca += $T_data[$i]['total_fiche'];
	// le compmtage de fiche
	$C_sql       = $sql_select_ct.$sql_where.$cmd_etat;
	$C_request   = $Database->Pdo->query($C_sql);
	$C_data      = $C_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$nb_fiche    = $C_data['nb_fiche'];
}

// .dP"Y8 888888  dP"Yb   dP""b8 88  dP 
// `Ybo."   88   dP   Yb dP   `" 88odP  
// o.`Y8b   88   Yb   dP Yb      88"Yb  
// 8bodP'   88    YbodP   YboodP 88  Yb 

// Stock Debut de période
$STKD_sql     = "SELECT sum(locator.pu_ht) AS tot_stk_deb FROM locator WHERE ";
$STKD_sql    .= "( id_etat in (0,1,11,31,32) AND	in_datetime < '".$date_debut."' AND (out_datetime is NULL OR out_datetime >= '".$date_debut."') ) ";
$STKD_sql    .= "OR ( id_etat in (21,22) AND down_datetime >= '".$date_debut."' AND	in_datetime < '".$date_debut."' AND (out_datetime is NULL OR out_datetime >= '".$date_debut."') ) ";
$STKD_request = $Totoro->Pdo->query($STKD_sql);
$STKD_data    = $STKD_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
$tot_stk_deb  = $STKD_data['tot_stk_deb'];
// Stock Fin de période
$STKF_sql     = "SELECT sum(locator.pu_ht) AS tot_stk_fin FROM locator WHERE ";
$STKF_sql    .= "( id_etat in (0,1,11,31,32) AND	in_datetime < '".$date_fin."' AND (out_datetime is NULL OR out_datetime >= '".$date_fin."') ) ";
$STKF_sql    .= "OR ( id_etat in (21,22) AND down_datetime >= '".$date_fin."' AND	in_datetime < '".$date_fin."' AND (out_datetime is NULL OR out_datetime >= '".$date_fin."') ) ";
$STKF_request = $Totoro->Pdo->query($STKF_sql);
$STKF_data    = $STKF_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
$tot_stk_fin  = $STKF_data['tot_stk_fin'];


// Si il y a au moins un resultat
if ($nb_fiche > 0)
{
	// creation de la liste de fiches sans les pret et loc
	foreach($L_data as $A_cmd_id)
		{ $liste_fiche .= $A_cmd_id['cmd__id'].','; }
	$liste_fiche = substr($liste_fiche, 0, -1); // Supprimer la derniere virgule de la liste de fiches

	// creation de la liste de fiches de pret ou loc
	foreach($LP_data as $A_cmd_id)
		{ $liste_fiche_lp .= $A_cmd_id['cmd__id'].','; }
	$liste_fiche_lp = substr($liste_fiche_lp, 0, -1); // Supprimer la derniere virgule de la liste de fiches


	// prix du matos sortie du locator pour les fiches facturées (matos à reconstruire ou HS non compté etat 21, 22)
	$VS_sql       = 'SELECT SUM(locator.pu_ht) AS valo_sortie FROM locator ';
	$VS_sql      .= 'where out_id_cmd IN ('.$liste_fiche.') AND (id_etat < 20 OR id_etat > 30)';
	$VS_request   = $Totoro->Pdo->query($VS_sql);
	$VS_data      = $VS_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$vs_fiche     = $VS_data['valo_sortie'];

	// prix du matos sortie du locator pour les fiches de loc et pret (matos à reconstruire ou HS non compté etat 21, 22)
	$vs_fiche_lp  = -1;
	if (strlen($liste_fiche_lp) > 6)
	{
		$VLP_sql      = 'SELECT SUM(locator.pu_ht) AS valo_sortie FROM locator ';
		$VLP_sql     .= 'where out_id_cmd IN ('.$liste_fiche_lp.') AND (id_etat < 20 OR id_etat > 30)';
		$VLP_request  = $Totoro->Pdo->query($VLP_sql);
		$VLP_data     = $VLP_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
		$vs_fiche_lp  = $VLP_data['valo_sortie'];
	}

	// prix du matos sortie sur la periode non destiné a des fiches (matos à reconstruire ou HS non compté etat 21, 22)
	$VP_sql       = "SELECT SUM(locator.pu_ht) AS valo_periode FROM locator ";
	$VP_sql      .= "WHERE out_datetime ".$sql_where_dt." ";
	$VP_sql      .= "AND (out_id_cmd > '4000000' or out_id_cmd < '3000000') AND (id_etat < 20 OR id_etat > 30) ";
	$VP_request   = $Totoro->Pdo->query($VP_sql);
	$VP_data      = $VP_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$vs_periode   = $VP_data['valo_periode'];

	// liste des fiches de garantie et Maintenance (non fact, etat ARH)
	// pour info numero de client speciaux : 2 GARANTIE RECODE, 3 MAINTENANCE RECODE,5 RELIQUAT LIVRAISON Déja Facturé
	$cmd_date    = "cmd__date_envoi ";
	$cmd_etat    = "AND cmd__etat = 'ARH' AND cmd__client__id_fact <= 5 ";
	$LGM_sql     = $sql_select_liste.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat;
	$LGM_request = $Database->Pdo->query($LGM_sql);
	$LGM_data    = $LGM_request->fetchAll(PDO::FETCH_ASSOC);
	// creation de la liste de fiches garantie et maintenance
	foreach($LGM_data as $A_cmd_id)
		{ $liste_fiche_gm .= $A_cmd_id['cmd__id'].','; }
	$liste_fiche_gm = substr($liste_fiche_gm, 0, -1); // Supprimer la derniere virgule de la liste de fiches

	// liste des fiches de RMA (evidement non fact, etat ARH)
	// pour info numero de client speciaux : 6 RMA fournisseur
	$cmd_date    = "cmd__date_envoi ";
	$cmd_etat    = "AND cmd__etat = 'ARH' AND cmd__client__id_fact = 6 ";
	$LRMA_sql    = $sql_select_liste.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat;
	$LRMA_request= $Database->Pdo->query($LRMA_sql);
	$LRMA_data   = $LRMA_request->fetchAll(PDO::FETCH_ASSOC);
	// creation de la liste de fiches de rma
	foreach($LRMA_data as $A_cmd_id)
		{ $liste_fiche_rma .= $A_cmd_id['cmd__id'].','; }
	$liste_fiche_rma = substr($liste_fiche_rma, 0, -1); // Supprimer la derniere virgule de la liste de fiches

	// prix du matos sortie du locator pour les fiches Garantie et maintenance
	$vs_garmaint     = 0;
	if (strlen($liste_fiche_gm) > 0)
	{
		$VS_gm_sql       = "SELECT SUM(locator.pu_ht) AS valo_sortie FROM locator ";
		$VS_gm_sql      .= "where out_id_cmd IN (".$liste_fiche_gm.") AND (id_etat < 20 OR id_etat > 30) ";
		$VS_gm_request   = $Totoro->Pdo->query($VS_gm_sql);
		$VS_gm_data      = $VS_gm_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
		$vs_garmaint     = $VS_gm_data['valo_sortie'];
	}
	
	// Valeur du matos declassifié sur la periode sans le matos en RMA (je ne m'occupe pas du matos sortie, car si il est sortie je ne le compte pas avant donc pas de double comptage)
	$VD_sql       = "SELECT SUM(locator.pu_ht) AS valo_down FROM locator ";
	$VD_sql      .= "WHERE ( down_datetime ".$sql_where_dt." ) ";
	if (strlen($liste_fiche_rma) > 6)
		$VD_sql      .= "AND locator.out_id_cmd not in (".$liste_fiche_rma.") ";
	$VD_request   = $Totoro->Pdo->query($VD_sql);
	$VD_data      = $VD_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$vs_down      = $VD_data['valo_down'];

	// // calcul du cumul de CA (VTE, ECH, INT ? , REP ? )
	foreach ($T_data as $lg_ca ) 
	{
		switch ($lg_ca['cmdl__prestation']) 
		{  // total avec ext garantie
			case 'VTE':
			case 'ECH':
			case 'INT':
			case 'REP':
				$tot_ca_veir += $lg_ca['total_fiche'];
			break;
		}
	}

}

// Donnée transmise au template : 
echo $twig->render('statistique.twig',
[
'user'             => $user,
'nb_fiche'         => $nb_fiche , 
'total_ca'         => $total_ca,
'date_debut'       => $date_debut, 
'date_fin'         => $date_fin,
'date_debut_fr'    => $date_debut_fr, 
'date_fin_fr'      => $date_fin_fr,
'type_tot'         => $type_tot,
'titre_stat'       => $titre_stat,
'desc_stat'        => $desc_stat,
'sql_stat'         => $T_sql ,
't_data'           => $T_data,
'vs_fiche'         => $vs_fiche,
'vs_fiche_lp'      => $vs_fiche_lp,
'vs_periode'       => $vs_periode,
'vs_garmaint'      => $vs_garmaint,
'vs_down'          => $vs_down,
'tot_ca_ve'        => $tot_ca_ve,
'tot_ca_veir'      => $tot_ca_veir, // pas bien sur que ca serve  ...
'vendeurList'      => $vendeurList, 
'vendeurSelect'    => $vendeur,
'arrayPresta'      => $arrayPresta , 
'abnSearch'        => $abnSearch,
'cmdSearch'        => $cmdSearch,
'tot_stk_deb'      => $tot_stk_deb,
'tot_stk_fin'      => $tot_stk_fin
]);


// //tableau des resultats afin d'alimenter les charts : 
// $arrayResults = [];
// //si les dates corespondent et que le résultats n'est pas vide : 
// if (!empty($cmdList)) 
// { 
// 	//pour chaque commande présente dans ma liste de commande: 
// 	foreach ($cmdList as $cmd ) 
// 	{
// 		if ($maintenance_location == true) 
// 		{
// 			$results= $Stat->get_ligne_maintenance_stat($cmd->cmd__id);
// 		}
// 		else 
// 		{
// 			$results= $Stat->WLstatsGlobal($cmd->cmd__id ,$cmd->cmd__etat );
// 		}
		
// 		foreach ($results as $ligne) 
// 		{
// 			$total = floatval($ligne->ht) * intval($ligne->qte);
// 			if (!empty($ligne->htg)) 
// 			{
// 				$htg = floatval($ligne->htg) * intval($ligne->qte);
// 				$total = $total + $htg;
// 			}
// 			array_push($arrayResults , $total );
// 		}
// 	}

// $resultHt = array_sum($arrayResults);
// $resultHt  = number_format($resultHt , 2,',', ' ');
// $NombreCmd = count($cmdList);
// $arrayJson = [];

// //traite la liste de commande commandes pour le camenbert des prestation:
// $prestaList = $Keyword->getPresta();
// $arrayPresta = []; 
// $headerPresta = [['Presation'] , ['Chiffre']];
// array_push($arrayPresta , $headerPresta);
// foreach($prestaList as $presta) 
// {
// $arrayTemp = [];
// $arrayTemp[0] = $presta->kw__lib;
// $totalParPresta = [];
// 	foreach ($cmdList as $cmd ) 
// 	{
// 			$temp = [];
// 			if ($maintenance_location == true) 
// 			{
// 				$results= $Stat->get_ligne_maintenance_stat($cmd->cmd__id);
// 			}
// 			else 
// 			{
// 				$results= $Stat->WLstatsGlobal($cmd->cmd__id ,$cmd->cmd__etat);
// 			}
// 			foreach ($results as $ligne) 
// 			{
// 				$total = floatval($ligne->ht) * intval($ligne->qte);
// 				if (!empty($ligne->htg)) 
// 				{
// 					$htg = floatval($ligne->htg) * intval($ligne->qte);
// 					$total = $total + $htg;
// 				}

// 				if ($ligne->presta == $presta->kw__value) 
// 				{
// 					array_push($temp, $total);
// 				}
// 			}
// 			$temp = array_sum($temp);
// 			array_push($totalParPresta , $temp);
// 	}
// 	$totalParPresta = array_sum($totalParPresta) ;
// 	$arrayTemp[1] = $totalParPresta;
// 	$arrayTemp[0] = $presta->kw__lib . ' ' . number_format($totalParPresta , 2,',', ' ') . ' € ';
// 	if ($arrayTemp[1] > 0) 
// 	{
// 		array_push($arrayPresta , $arrayTemp);
// 	} 
// }
// $arrayPresta = json_encode($arrayPresta);
// // fin du camenbert prestation 

// //traite la liste de commande pour le commanbert commercial:
// $vendeurList = $UserClass->getAll();
// $arrayGlobal = [];
// $arrayheader = [['Vendeur'],['Chiffre']];
// array_push($arrayGlobal ,$arrayheader);
// 		foreach ($vendeurList as $vendeurN) 
// 		{
// 		$array[$vendeurN->id_utilisateur][0] = [$vendeurN->nom];
// 		$totalParVendeur = [] ;
// 				foreach ($cmdList as $cmd) 
// 				{
// 					if ($vendeurN->id_utilisateur == $cmd->client__id_vendeur) 
// 					{
// 								$tempCmd = [];
// 								if ($maintenance_location == true) 
// 								{
// 									$results= $Stat->get_ligne_maintenance_stat($cmd->cmd__id);
// 								}
// 								else 
// 								{
// 									$results= $Stat->WLstatsGlobal($cmd->cmd__id , $cmd->cmd__etat);
// 								}
// 								$temp = [];

// 								foreach ($results as $ligne) 
// 								{
									
// 										$total = floatval($ligne->ht) * intval($ligne->qte);

// 										if (!empty($ligne->htg)) 
// 										{
// 											$htg = floatval($ligne->htg) * intval($ligne->qte);
// 											$total = $total + $htg;
// 										}

// 										array_push($temp, $total);
// 										$total = array_sum($temp);
// 										array_push($tempCmd , $total);
// 								}
// 								$tempCmd =  array_sum($tempCmd);
// 								array_push($totalParVendeur, $tempCmd);    
// 					}
// 				}
// 				$totalParVendeur = array_sum($totalParVendeur);
// 				if (!empty($totalParVendeur)) 
// 				{
// 					$tempsarrayVendeur = [];
// 					$tempsarrayVendeur[0] = $vendeurN->nom . ' : ' . $totalParVendeur . '€' ;
// 					$tempsarrayVendeur[1] = $totalParVendeur;
// 					array_push($arrayGlobal ,$tempsarrayVendeur );
// 				}    
// 		}
// $chartsVendeur = json_encode($arrayGlobal);
// // fin du camembert : 
//formattage des dates pour l'affichage à l'utilisateur : 
// array_push($arrayJson , $resultHt );
// array_push($arrayJson , $dateFormatFin);
// $chartsResponses = json_encode($arrayJson);
