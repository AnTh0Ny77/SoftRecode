<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client    = new \App\Tables\Client($Database);
$Contact   = new \App\Tables\Contact($Database);
$Keyword   = new \App\Tables\Keyword($Database);
$Cmd       = new \App\Tables\Cmd($Database);
$Stats     = new App\Tables\Stats($Database);
$Stocks    = new \App\Tables\Stock($Database);
$Pistage   = new App\Tables\Pistage($Database);
$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
$_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
use App\Methods\Pdfunctions;
use App\Methods\Devis_functions;
//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) 
	header('location: login');
// mise a blanc des variables.
$search       = '';
$search_len   = 0;
//si une redirection arrive d'une autre page par le biais de la variable de session :
if (!empty($_SESSION['search_switch'])) {
	$_POST['search'] =  $_SESSION['search_switch'];
	$_SESSION['search_switch'] = '';
}
// recup des info et verif 
$search = strtoupper(trim($_POST['search']));
$search_len = strlen($search);
//switch sur la variable de recherche : 
if (!empty($search)){
	// recherche de mots clés
	if ($search == 'STATM')                        header('location: stat_marge');
	if ($search == 'STAT')                         header('location: stat');
	if ($search == 'DEV'   OR $search == 'DEVIS')  header('location: mesDevis');
	if ($search == 'FICHE' OR $search == 'FT')     header('location: ficheTravail');
	if ($search == 'GAR')                          header('location: fiches_garantie');
	if ($search == 'POR'   OR $search == 'PORT')   header('location: transport');
	if ($search == 'FAC'   OR $search == 'FACT')   header('location: facture');
	if ($search == 'ABO')                          header('location: abonnement');
	if ($search == 'CAT')                          header('location: ArtCataloguePN');

	switch ($search_len){
		//si la chaine fait une longueur 6 et qu'elle ne contient que des numérics
		case ($search == 4 and ctype_digit($search)):
			//groupe de ticket : 
		break;
		case ($search == 5 and ctype_digit($search)):
			//ticket : 
		break;
		case ($search == 6 and ctype_digit($search)):
			//je fais une recherche par id 
			$client = $Client->search_client_devis($search);
			foreach ($client as $client_results) {
				$date_modif = new DateTime($client_results->client__dt_last_modif);
				$client_results->client__dt_last_modif = $date_modif->format('d/m/Y');
			}
			//Si le résultat est bien unique : 
			if (count($client) == 1){
				$client = $Client->getOne($client[0]->client__id);
				//compte les contacts : 
				$count_contact = $Contact->count_contact($client->client__id);
				//liste des 3 principaux contacts
				$contact_list = $Contact->get_contact_search($client->client__id ,3 );
				//si la liste des contacts est plus grande que les 3 contact proposés : 
				if (intval($count_contact[0]["COUNT(*)"]) > count($contact_list)){
				       $extendre_contacts = intval($count_contact[0]["COUNT(*)"]) - count($contact_list) ;
				}
				else $extendre_contacts = false ;
				//liste des quinze dernière commmandes : 
				$cmd_list = $Cmd->get_by_client_id($client->client__id , 10 );
				//format les dates de la commande : 
				foreach ($cmd_list as $cmd){
					$date =  new DateTime($cmd->cmd__date_devis);
					$cmd->cmd__date_devis =  $date->format('d/m/Y');
				}
				$alert = false;
				if (isset($_SESSION['transfert']) and !empty($_SESSION['transfert'])) {
					$alert = $_SESSION['transfert'];
					$_SESSION['transfert'] = "";
					header('location displaySocieteMyRecode?cli__id='. $client->client__id.'');
					die();
				}
				// Donnée transmise au template : 
				echo $twig->render(
					'consult_client.twig',[
						'user' => $_SESSION['user'],
						'client' => $client ,
						'contact_list' => $contact_list ,
						'etendre_contact' =>  $extendre_contacts ,
						'commandes_list' => $cmd_list , 
						'alert' => $alert
					]);
			}
			else{
				$client_list = $client ;  
				 // Donnée transmise au template : 
				 echo $twig->render(
					'consult_client_list.twig',
					[
						'user' => $_SESSION['user'],
						'client_list' => $client_list 
					]);
			}
			break;
		
		//si la chaine fait une longueur 7 et qu'elle ne contient que des numérics
		case (strlen($search) == 7 and ctype_digit($search) and $Cmd->GetById($search)):
			$etat_list = $Keyword->get_etat();
			$commande = $Cmd->GetById($search);
			$liste_actions = $Pistage->get_pist_by_id($search);
			$lignes = $Cmd->devisLigne($search);
			foreach ($lignes as $ligne){
				if (!empty($ligne->devl__modele)) 
				{
					$ligne->spec = $Stocks->select_empty_heritage($ligne->devl__modele , true , true);
				}
				else $ligne->spec = " ";
			}

			if ($commande->devis__etat == 'VLD' || $commande->devis__etat == 'VLA'){
				$totaux = Pdfunctions::totalFacturePDF($commande, $lignes );
				foreach ($totaux as $key => $results){
				       $results = number_format(floatVal($results), 2, ',', ' ');
					$totaux[$key] = $results;
				}
			}else{
				$totaux = Pdfunctions::totalFacturePRO($commande, $lignes );
				foreach ($totaux as $key => $results) {
					$results = number_format(floatVal($results), 2, ',', ' ');
					$totaux[$key] = $results ;
				}
			}
			
			//formatte les dates pour l'utilisateur : 
			$date =  new DateTime($commande->devis__date_crea);
			$commande->devis__date_crea =  $date->format('d/m/Y');
			if (!empty($commande->cmd__date_cmd)){
				$date =  new DateTime($commande->cmd__date_cmd);
				$commande->cmd__date_cmd =  $date->format('d/m/Y');
			}
			if (!empty($commande->cmd__date_fact)){
				$date =  new DateTime($commande->cmd__date_fact);
				$commande->cmd__date_fact =  $date->format('d/m/Y');
			}
			
			echo $twig->render(
				'consult_commande.twig',
				[
					'user' => $_SESSION['user'],
					'commande' => $commande ,
					'etat_list' => $etat_list,
					'lignes' => $lignes , 
					'totaux' => $totaux ,
					'liste_action' => $liste_actions
				]);
			break;
		
		//par default je recherche un client : 
		default:
			$client_list = $Client->search_client_devis($search);
			foreach ($client_list as $client) 
			{
				$date_modif = new DateTime($client->client__dt_last_modif);
				$client->client__dt_last_modif = $date_modif->format('d/m/Y');
			}
			if (count($client_list) == 1) 
			{
				$client = $Client->getOne($client_list[0]->client__id);
				//compte les contacts : 
				$count_contact = $Contact->count_contact($client->client__id);
				//liste des 3 principaux contacts
				$contact_list = $Contact->get_contact_search($client->client__id, 3);
				//si la liste des contacts est plus grande que les 3 contact proposés : 
				if (intval($count_contact[0]["COUNT(*)"]) > count($contact_list)) {
					$extendre_contacts = intval($count_contact[0]["COUNT(*)"]) - count($contact_list);
				} else $extendre_contacts = false;
				//liste des quinze dernière commmandes : 
				$cmd_list = $Cmd->get_by_client_id($client->client__id, 10);
				//format les dates de la commande : 
				foreach ($cmd_list as $cmd) {
					$date =  new DateTime($cmd->cmd__date_devis);
					$cmd->cmd__date_devis =  $date->format('d/m/Y');
				}
				// Donnée transmise au template : 
				echo $twig->render(
					'consult_client.twig',
					[
						'user' => $_SESSION['user'],
						'client' => $client,
						'contact_list' => $contact_list,
						'etendre_contact' =>  $extendre_contacts,
						'commandes_list' => $cmd_list
					]
				);
			}
			else 
			{
				// Donnée transmise au template : 
				echo $twig->render(
					'consult_client_list.twig',
					[
						'user' => $_SESSION['user'],
						'client_list' => $client_list
					]
				);
			}
			
			break;
	}

}
else
{
	header('location: dashboard');
}

