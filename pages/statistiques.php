<?php
session_start();
use App\Tables\Stats;
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
if (empty($_SESSION['user']))                     header('location: login');
if ($_SESSION['user']->user__facture_acces < 10 ) header('location: noAccess');

//déclaration des instances nécéssaires :
$user      = $_SESSION['user'];
$Database  = new App\Database('devis');
$Database->DbConnect();
$Keyword   = new App\Tables\Keyword($Database);
$Client    = new App\Tables\Client($Database);
$Contact   = new App\Tables\Contact($Database);
$Cmd       = new App\Tables\Cmd($Database);
$General   = new App\Tables\General($Database);
$Article   = new App\Tables\Article($Database);
$UserClass = new App\Tables\User($Database);
$Pisteur   = new App\Tables\Pistage($Database);
$Stat      = new App\Tables\Stats($Database);

//recupération des listes nécéssaires : 
$clientList = $Client->getAll();
$articleTypeList = $Article->getModels();
$vendeurList = $UserClass->getCommerciaux();

//declaration des variables diverses : 
$alertDate = $resultHt = $NombreCmd = $chartsResponses = $chartsVendeur = $arrayPresta = FALSE;
$chiffre_cmd_fact = $maintenance_location = $cmdSearch = $abnSearch = FALSE;
$description_recherche = '';
$client = 'Tous';
$vendeur = 'Tous';

if (empty($_POST['dateDebut'])) 
	$_POST['dateDebut'] = date("Y-m-01");

if (empty($_POST['dateFin'])) 
	$_POST['dateFin'] = date("Y-m-t");

if (empty($_POST['client']))
	$_POST['client'] = 'Tous';

if (empty($_POST['vendeur']))
	$_POST['vendeur'] = 'Tous';


// recuperations des GET ou POST
$date_debut     = get_post('dateDebut', 1, 'GETPOST');
$date_fin       = get_post('dateFin', 1, 'GETPOST');

//dates et filtres par default au mois en cours: 
if (!$date_debut OR !$date_fin) // c'est vide donc c'est un premier appael a la page je met les date 1er et fin de mois en cours
{
	$date = new DateTime();
	$date_debut = $date->format('Y-m-01');
	$date_fin   = $date->format('Y-m-t');
	$_POST['client'] = 'Tous';
	$_POST['vendeur'] = 'Tous';
}
//si la recherche inclus les abonnements : 
if (!empty($_POST['checkAbn'])) 
	$abnSearch = true;
if (!empty($_POST['check_commande'])) 
	$cmdSearch = true ;
if (!empty($_POST['check_maintenance'])) 
	$maintenance_location = true ;

//si les 2 dates sont là
if ($date_debut AND $date_fin)
{
	//si les filtres client et vendeur sont demandés : 
	if ($_POST['client'] != 'Tous' || $_POST['vendeur'] != 'Tous') 
	{
		//recupere les infos pour affichage des client et vendeurs 
		if ($_POST['client'] != 'Tous') 
		{
			$client = $Client->getOne($_POST['client']);
		}
		else
		{
			$client = 'Tous';
		}
		if ($_POST['vendeur'] != 'Tous') 
		{
			$vendeur = $UserClass->getByID($_POST['vendeur']);
		}
		else
		{
			$vendeur = 'Tous';
		}
		//si les statistiques des commandes en cour à été demandée
		if (!empty($_POST['check_commande'])) 
		{
			//si il faut inclure les commandes deja facturées : 
			if (!empty($_POST['check_commande_facture'])) 
			{
						$cmdList = $Stat->return_commande_client_vendeur_chiffre($date_debut, $date_fin, $_POST['client'], $_POST['vendeur']);
						$description_recherche = 'Les résultats concernent : les commandes passés entre les 2 dates et incluent les celles qui ont déja été facturées ';
						//si je consulte uniquement la maintenance et la location je le rejoute à la description : 
						if ($maintenance_location == true) 
						{
							$description_recherche .= ' et prennent en compte uniquement les prestantions de maintenance et de location';
						}
						$abnSearch = false;
						$chiffre_cmd_fact = true;
			}
			else 
			{
						$cmdList = $Stat->return_commande_client_vendeur($date_debut, $date_fin, $_POST['client'], $_POST['vendeur']);
						$description_recherche = 'Les résultats concernent : les commandes passés entre les 2 dates ( commandées ou expédiées) mais n incluent PAS les commandes déja facturées ';
						if ($maintenance_location == true) 
						{
							$description_recherche .= ' et prennent en compte uniquement les prestantions de maintenance et de location';
						}
						$abnSearch = false;
						$chiffre_cmd_fact = false;
			}
			
		} 
		//sinon appel de la methode classique du chiffre d'affaire entre 2 dates : 
		else 
		{
			$cmdList = $Stat->returnCmdBetween2DatesClientVendeur($date_debut, $date_fin, $_POST['client'], $_POST['vendeur'], $abnSearch);
				if ($abnSearch == true ) 
				{
						$description_recherche = 'Les résultats concernent : les commandes facturée entre  les 2 dates et incluent les chiffres des abonnements de maintenance et de location ';
				}
				else 
				{
						$description_recherche = 'Les résultats concernent : les commandes facturée entre  les 2 dates mais n incluent pas les chiffres des abonnements de maintenance et de location ';
				}
		
		}
	
	}
	//si aucun filtre client vendeur n'est doné : ( code à optimiser par la suite )
	else 
	{
		//si le chiffre des commandes en cours à été demandé : 
		if (!empty($_POST['check_commande'])) 
		{
				//si je dois inclure le chiffre deja facturé 
				if(!empty($_POST['check_commande_facture']))
				{
						$cmdList = $Stat->return_commandes_chiffre($date_debut, $date_fin);
						$description_recherche = 'Les résultats concernent : les commandes passés entre les 2 dates et incluent les celles qui ont déja été facturées';
						if ($maintenance_location == true) 
								{
									$description_recherche .= ' et prennent en compte uniquement les prestations de maintenance et de location';
								}
						$abnSearch = false;
						$chiffre_cmd_fact = true;
				}
				else
				{
						$cmdList = $Stat->return_commandes($date_debut, $date_fin);
						$description_recherche = 'Les résultats concernent : les commandes passés entre les 2 dates ( commandées ou expédiées) mais n incluent PAS les commandes déja facturées';
						if ($maintenance_location == true) 
								{
									$description_recherche .= ' et prennent en compte uniquement les prestations de maintenance et de location';
								}
						$abnSearch = false;
						$chiffre_cmd_fact = false;
				}
				
		}
		//sinon je retourne la liste de commandes classique : 
		else 
		{
				$cmdList = $Stat->returnCmdBetween2Dates($date_debut, $date_fin, $abnSearch);
				if ($abnSearch == true) 
				{
						$description_recherche = 'Les résultats concernent : les commandes facturée entre  les 2 dates et incluent les chiffres des abonnements de maintenance et de location ';
				} 
				else 
				{
						$description_recherche = 'Les résultats concernent : les commandes facturée entre  les 2 dates mais n incluent pas les chiffres des abonnements de maintenance et de location ';
				}
		}
		
	}

	//tableau des resultats afin d'alimenter les charts : 
	$arrayResults = [];
	//si les dates corespondent et que le résultats n'est pas vide : 
	if (!empty($cmdList)) 
	{ 
		//pour chaque commande présente dans ma liste de commande: 
		foreach ($cmdList as $cmd ) 
		{
			if ($maintenance_location == true) 
			{
				$results= $Stat->get_ligne_maintenance_stat($cmd->cmd__id);
			}
			else 
			{
				$results= $Stat->WLstatsGlobal($cmd->cmd__id ,$cmd->cmd__etat );
			}
			
			foreach ($results as $ligne) 
			{
				$total = floatval($ligne->ht) * intval($ligne->qte);
				if (!empty($ligne->htg)) 
				{
					$htg = floatval($ligne->htg) * intval($ligne->qte);
					$total = $total + $htg;
				}
				array_push($arrayResults , $total );
			}
		}

	$resultHt = array_sum($arrayResults);
	$resultHt  = number_format($resultHt , 2,',', ' ');
	$NombreCmd = count($cmdList);
	$arrayJson = [];

//traite la liste de commande commandes pour le camenbert des prestation:
$prestaList = $Keyword->getPresta();
$arrayPresta = []; 
$headerPresta = [['Presation'] , ['Chiffre']];
array_push($arrayPresta , $headerPresta);
foreach($prestaList as $presta) 
{
	$arrayTemp = [];
	$arrayTemp[0] = $presta->kw__lib;
	$totalParPresta = [];
		foreach ($cmdList as $cmd ) 
		{
				$temp = [];
				if ($maintenance_location == true) 
				{
					$results= $Stat->get_ligne_maintenance_stat($cmd->cmd__id);
				}
				else 
				{
					$results= $Stat->WLstatsGlobal($cmd->cmd__id ,$cmd->cmd__etat);
				}
				foreach ($results as $ligne) 
				{
					$total = floatval($ligne->ht) * intval($ligne->qte);
					if (!empty($ligne->htg)) 
					{
						$htg = floatval($ligne->htg) * intval($ligne->qte);
						$total = $total + $htg;
					}

					if ($ligne->presta == $presta->kw__value) 
					{
						array_push($temp, $total);
					}
				}
				$temp = array_sum($temp);
				array_push($totalParPresta , $temp);
		}
		$totalParPresta = array_sum($totalParPresta) ;
		$arrayTemp[1] = $totalParPresta;
		$arrayTemp[0] = $presta->kw__lib . ' ' . number_format($totalParPresta , 2,',', ' ') . ' € ';
		if ($arrayTemp[1] > 0) 
		{
			array_push($arrayPresta , $arrayTemp);
		} 
}
$arrayPresta = json_encode($arrayPresta);
// fin du camenbert prestation 

//traite la liste de commande pour le commanbert commercial:
	$vendeurList = $UserClass->getAll();
	$arrayGlobal = [];
	$arrayheader = [['Vendeur'],['Chiffre']];
	array_push($arrayGlobal ,$arrayheader);
		 foreach ($vendeurList as $vendeurN) 
		 {
			$array[$vendeurN->id_utilisateur][0] = [$vendeurN->nom];
			$totalParVendeur = [] ;
					foreach ($cmdList as $cmd) 
					{
						if ($vendeurN->id_utilisateur == $cmd->client__id_vendeur) 
						{
									$tempCmd = [];
									if ($maintenance_location == true) 
									{
										$results= $Stat->get_ligne_maintenance_stat($cmd->cmd__id);
									}
									else 
									{
										$results= $Stat->WLstatsGlobal($cmd->cmd__id , $cmd->cmd__etat);
									}
									$temp = [];

									foreach ($results as $ligne) 
									{
										
											$total = floatval($ligne->ht) * intval($ligne->qte);

											if (!empty($ligne->htg)) 
											{
												$htg = floatval($ligne->htg) * intval($ligne->qte);
												$total = $total + $htg;
											}

											array_push($temp, $total);
											$total = array_sum($temp);
											array_push($tempCmd , $total);
									}
									$tempCmd =  array_sum($tempCmd);
									array_push($totalParVendeur, $tempCmd);    
						}
					}
					$totalParVendeur = array_sum($totalParVendeur);
					if (!empty($totalParVendeur)) 
					{
						$tempsarrayVendeur = [];
						$tempsarrayVendeur[0] = $vendeurN->nom . ' : ' . $totalParVendeur . '€' ;
						$tempsarrayVendeur[1] = $totalParVendeur;
						array_push($arrayGlobal ,$tempsarrayVendeur );
					}    
		 }
	$chartsVendeur = json_encode($arrayGlobal);
	// fin du camembert : 
	//formattage des dates pour l'affichage à l'utilisateur : 
	$dateFormatdebut = new DateTime($date_debut);
	$dateFormatdebut = $dateFormatdebut->format('d/m/Y');
	$dateFormatFin = new DateTime($date_fin);
	$dateFormatFin = $dateFormatFin->format('d/m/Y');
	array_push($arrayJson , $resultHt );
	array_push($arrayJson , $dateFormatFin);
	$chartsResponses = json_encode($arrayJson);
	}
	else 
	{
		$dateFormatdebut = new DateTime($_POST['dateDebut']);
		$dateFormatdebut = $dateFormatdebut->format('d/m/Y');
		$dateFormatFin = new DateTime($_POST['dateFin']);
		$dateFormatFin = $dateFormatFin->format('d/m/Y');
		$alertDate = true;
	} 
}

// Donnée transmise au template : 
echo $twig->render('statistique.twig',
[
'user' => $user,
'vendeurList'    => $vendeurList, 
'clientList'     => $clientList , 
'articleList'    => $articleTypeList,
'alertDate'      => $alertDate ,
'resultHt'       => $resultHt,
'NombreCmd'      => $NombreCmd , 
'DateDeb'        => $date_debut,
'DateFin'        => $date_fin, 
'formatDebut'    => $dateFormatdebut,
'formatFin'      => $dateFormatFin , 
'clientSelect'   => $client,
'vendeurSelect'  => $vendeur,
'chartsResponse' => $chartsResponses ,
'chartsVendeur'  => $chartsVendeur , 
'arrayPresta'    => $arrayPresta , 
'abnSearch'      => $abnSearch,
'cmdSearch'      => $cmdSearch ,
'chiffre_commandes_fact' => $chiffre_cmd_fact ,
'decription_recherche'   => $description_recherche ,
'maintenance_location'   => $maintenance_location
]);
 
 
	
