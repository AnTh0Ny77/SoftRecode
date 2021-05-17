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
$desc_stat = $type_tot = $titre_stat = $SQL_stat = '';
$client = $vendeur = 'Tous';
$total_ca = $nb_fiche = 0;

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
$sql_select_tabF .= "key_presta.kw__lib AS presta, key_etat.kw__lib AS etat FROM cmd ";
$sql_select_tabC  = "SELECT sum(cmdl__qte_cmd*cmdl__puht) + IFNULL(SUM(cmdl__qte_cmd * cmdl__garantie_puht),0) AS total_fiche, ";
$sql_select_tabC .= "key_presta.kw__lib AS presta, key_etat.kw__lib AS etat FROM cmd ";
$sql_select_ct   = "SELECT count(cmd.cmd__id) AS nb_fiche FROM cmd ";
$sql_select_join = "LEFT JOIN cmd_ligne ON cmd_ligne.cmdl__cmd__id = cmd.cmd__id ";
$sql_select_join.= "INNER JOIN keyword AS key_etat    ON cmd_ligne.cmdl__etat = key_etat.kw__value AND key_etat.kw__type = 'letat' ";
$sql_select_join.= "INNER JOIN keyword AS key_presta ON cmd_ligne.cmdl__prestation = key_presta.kw__value AND key_presta.kw__type = 'pres' ";
$sql_where       = "WHERE ";
$sql_where_dt    = "BETWEEN '".$date_debut." 00:00:00' AND '".$date_fin." 23:59:59' ";
$sql_groupby     = "GROUP BY cmdl__prestation, cmdl__etat ";
$sql_order       = "ORDER BY key_presta.kw__ordre, cmdl__etat ";


//  dP""b8    db           db    Yb    dP 888888  dP""b8        db    88""Yb  dP"Yb  
// dP   `"   dPYb         dPYb    Yb  dP  88__   dP   `"       dPYb   88__dP dP   Yb 
// Yb       dP__Yb       dP__Yb    YbdP   88""   Yb           dP__Yb  88""Yb Yb   dP 
//  YboodP dP""""Yb     dP""""Yb    YP    888888  YboodP     dP""""Yb 88oodP  YbodP  
if ($CAM)
{
	$titre_stat  = 'Chiffre d\'affaire facturé Avec Abonnements (Maint et Loc)';
	$desc_stat   = 'Chiffre d\'affaire (somme CA des fiches) facturé (date de fact.) Avec maintenance (Etat des fiches VLD & VLA) sur la période (prenant en compte les avoirs) (Ce chiffre doit etre le meme que le CA en compta)';
	$type_tot    = 'CAM';
	$cmd_date    = "cmd__date_fact ";
	$cmd_etat    = "AND cmd__etat IN ('VLD','VLA') ";
	// le tableau de data
	$T_sql       = $sql_select_tabF.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat.$sql_groupby.$sql_order;
	$T_request   = $Database->Pdo->query($T_sql);
	$T_data      = $T_request->fetchAll(PDO::FETCH_ASSOC);
	// le compmtage de fiche
	$C_sql       = $sql_select_ct.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat;
	$C_request   = $Database->Pdo->query($C_sql);
	$C_data      = $C_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$nb_fiche    = $C_data['nb_fiche'];
	// Somme du total_ca
	for ($i=0; $i<=count($T_data)-1; $i++) $total_ca += $T_data[$i]['total_fiche'];
}

//  dP""b8    db        .dP"Y8    db    88b 88 .dP"Y8        db    88""Yb  dP"Yb 
// dP   `"   dPYb       `Ybo."   dPYb   88Yb88 `Ybo."       dPYb   88__dP dP   Yb
// Yb       dP__Yb      o.`Y8b  dP__Yb  88 Y88 o.`Y8b      dP__Yb  88""Yb Yb   dP
//  YboodP dP""""Yb     8bodP' dP""""Yb 88  Y8 8bodP'     dP""""Yb 88oodP  YbodP 
if ($CAS)
 {
	 $titre_stat  = 'Chiffre d\'affaire facturé sans Abonnement (Maint et Loc) (Fact Automatique)';
	 $desc_stat   = 'Chiffre d\'affaire (somme CA des fiches) facturé (date de fact.) sans maintenance (Etat des fiches VLD) sur la période';
	 $type_tot    = 'CAS';
	 $cmd_date    = "cmd__date_fact ";
	 $cmd_etat    = "AND cmd__etat IN ('VLD') ";
	 // le tableau de data
	 $T_sql       = $sql_select_tabF.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat.$sql_groupby.$sql_order;
	 $T_request   = $Database->Pdo->query($T_sql);
	 $T_data      = $T_request->fetchAll(PDO::FETCH_ASSOC);
	 // le compmtage de fiche
	 $C_sql       = $sql_select_ct.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat;
	 $C_request   = $Database->Pdo->query($C_sql);
	 $C_data      = $C_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	 $nb_fiche    = $C_data['nb_fiche'];
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
	$C_sql       = $sql_select_ct.$sql_select_join.$sql_where.$cmd_date.$sql_where_dt.$cmd_etat;
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
	$C_sql       = $sql_select_ct.$sql_select_join.$sql_where.$cmd_etat;
	$C_request   = $Database->Pdo->query($C_sql);
	$C_data      = $C_request->fetch(PDO::FETCH_ASSOC); // 1 seul ligne
	$nb_fiche    = $C_data['nb_fiche'];
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
'sql_stat'         => $T_sql,
't_data'           => $T_data,

'vendeurList'    => $vendeurList, 
'vendeurSelect'  => $vendeur,
'arrayPresta'    => $arrayPresta , 
'abnSearch'      => $abnSearch,
'cmdSearch'      => $cmdSearch 
]);






//'clientList'     => $clientList , 
//'articleList'    => $articleTypeList,
//'alertDate'      => $alertDate ,

// // si les statistiques des commandes en cour à été demandée
// 	if (!empty($_POST['check_commande'])) 
// 	{
// 		//si il faut inclure les commandes deja facturées : 
// 		if (!empty($_POST['check_commande_facture'])) 
// 		{
// 					$cmdList = $Stat->return_commande_client_vendeur_chiffre($date_debut, $date_fin, $_POST['client'], $_POST['vendeur']);
// 					$description_recherche = 'Les résultats concernent : les commandes passés entre les 2 dates et incluent les celles qui ont déja été facturées ';
// 					//si je consulte uniquement la maintenance et la location je le rejoute à la description : 
// 					if ($maintenance_location == true) 
// 					{
// 						$description_recherche .= ' et prennent en compte uniquement les prestantions de maintenance et de location';
// 					}
// 					$abnSearch = false;
// 					$chiffre_cmd_fact = true;
// 		}
// 		else 
// 		{
// 					$cmdList = $Stat->return_commande_client_vendeur($date_debut, $date_fin, $_POST['client'], $_POST['vendeur']);
// 					$description_recherche = 'Les résultats concernent : les commandes passés entre les 2 dates ( commandées ou expédiées) mais n incluent PAS les commandes déja facturées ';
// 					if ($maintenance_location == true) 
// 					{
// 						$description_recherche .= ' et prennent en compte uniquement les prestantions de maintenance et de location';
// 					}
// 					$abnSearch = false;
// 					$chiffre_cmd_fact = false;
// 		}
		
// 	} 
// 	//sinon appel de la methode classique du chiffre d'affaire entre 2 dates : 
// 	else 
// 	{
// 		$cmdList = $Stat->returnCmdBetween2DatesClientVendeur($date_debut, $date_fin, $_POST['client'], $_POST['vendeur'], $abnSearch);
// 			if ($abnSearch == true ) 
// 			{
// 					$description_recherche = 'Les résultats concernent : les commandes facturée entre  les 2 dates et incluent les chiffres des abonnements de maintenance et de location ';
// 			}
// 			else 
// 			{
// 					$description_recherche = 'Les résultats concernent : les commandes facturée entre  les 2 dates mais n incluent pas les chiffres des abonnements de maintenance et de location ';
// 			}
	
// 	}


// //si aucun filtre client vendeur n'est doné : ( code à optimiser par la suite )
// else 
// {
// 	//si le chiffre des commandes en cours à été demandé : 
// 	if (!empty($_POST['check_commande'])) 
// 	{
// 			//si je dois inclure le chiffre deja facturé 
// 			if(!empty($_POST['check_commande_facture']))
// 			{
// 					$cmdList = $Stat->return_commandes_chiffre($date_debut, $date_fin);
// 					$description_recherche = 'Les résultats concernent : les commandes passés entre les 2 dates et incluent les celles qui ont déja été facturées';
// 					if ($maintenance_location == true) 
// 							{
// 								$description_recherche .= ' et prennent en compte uniquement les prestations de maintenance et de location';
// 							}
// 					$abnSearch = false;
// 					$chiffre_cmd_fact = true;
// 			}
// 			else
// 			{
// 					$cmdList = $Stat->return_commandes($date_debut, $date_fin);
// 					$description_recherche = 'Les résultats concernent : les commandes passés entre les 2 dates ( commandées ou expédiées) mais n incluent PAS les commandes déja facturées';
// 					if ($maintenance_location == true) 
// 							{
// 								$description_recherche .= ' et prennent en compte uniquement les prestations de maintenance et de location';
// 							}
// 					$abnSearch = false;
// 					$chiffre_cmd_fact = false;
// 			}
			
// 	}
// 	//sinon je retourne la liste de commandes classique : 
// 	else 
// 	{
// 			$cmdList = $Stat->returnCmdBetween2Dates($date_debut, $date_fin, $abnSearch);
// 			if ($abnSearch == true) 
// 			{
// 					$description_recherche = 'Les résultats concernent : les commandes facturée entre  les 2 dates et incluent les chiffres des abonnements de maintenance et de location ';
// 			} 
// 			else 
// 			{
// 					$description_recherche = 'Les résultats concernent : les commandes facturée entre  les 2 dates mais n incluent pas les chiffres des abonnements de maintenance et de location ';
// 			}
// 	}
	
// }

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
