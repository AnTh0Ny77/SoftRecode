<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) {
	header('location: login');
} else {

	if ($_SESSION['user']->user__devis_acces < 10) {
		header('location: noAccess');
	}

	$user = $_SESSION['user'];
	//connexion et requetes :
	
	$Database = new App\Database('devis');
	$Database->DbConnect();
	$Client = new App\Tables\Client($Database);
	$Keywords = new App\Tables\Keyword($Database);
	$User = new App\Tables\User($Database);
	$General = new App\Tables\General($Database);
	$Pisteur = new App\Tables\Pistage($Database);
	$Contact = new App\Tables\Contact($Database);
	$Stats = new App\Tables\Stats($Database);
	$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
	$_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
	$tva_list = $Keywords->getAllFromParam('tva');
	$user_list = $User->getAll();
	$alert = false;
	$alertSuccess = false;
	$alertModif = false;
	$modif = false;
	$facturation_auto = false ;
	$contact_list = false ; 

	if (!empty($_POST['hidden_client'])) {
		$modif = $Client->getOne($_POST['hidden_client']);
		$facturation_auto = $Contact->get_facturation_auto($_POST['hidden_client']);
		$contact_list = $Contact->getFromLiaison($_POST['hidden_client']);
	}

	//si une creation de client a eu lieu : 
	if (empty($_POST['modif__id']) && !empty($_POST['nom_societe']) && !empty($_POST['ville']) && !empty($_POST['code_postal'])) {
		
		$pays = mb_strtoupper($_POST['input_pays'], 'UTF8');
		if ($pays === "FRANCE") {
			$pays = '';
		} else {
			$pays = $pays;
		}
	  
		if (!empty($_POST['telephone'])) 
		{
			$telephone = preg_replace('`[^0-9]`', '', $_POST['telephone']); 
		}
		else 
		{
			$telephone = '';
		}
		if (!empty($_POST['fax'])) 
		{
			$fax = preg_replace('`[^0-9]`', '', $_POST['fax']); 
		}
		else 
		{
			$fax = '';
		}
	  
		$creation_societe = $Client->create_one(
			$_POST['nom_societe'],
			$_POST['adresse_1'],
			$_POST['adresse_2'],
			$_POST['code_postal'],
			$_POST['ville'],
			$telephone,
			$fax,
			$_POST['select_tva'],
			$_POST['intracom_input'],
			$_POST['commentaire_client'],
			$_POST['vendeur'],
			$pays
		);

		if (!empty($_POST['config']))
		{
			$General->updateAll('client', $_POST['config'], 'client__memo_config', 'client__id', $creation_societe);
		}

		$Totoro = new App\Totoro('euro');
		$Totoro->DbConnect();
		$ContactTotoro = new App\Tables\ContactTotoro($Totoro);

		$creation_totoro = $Client->getOne($creation_societe);
		$creation =  $ContactTotoro->insertSociete($creation_totoro);

		
		if (!empty($_POST['facturation_auto'])) 
		{
			$contact = $Contact->update_facturation_auto($_POST['facturation_auto'], $creation_societe);
		 
			$ContactTotoro->totoro_delete_contact_facturation_and_update($_POST['facturation_auto'], $creation_societe);
		  
		}
		
		$alertSuccess = $creation_societe;
		$date = date("Y-m-d H:i:s");
		$Pisteur->addPiste($_SESSION['user']->id_utilisateur, $date, $creation_societe, ' création de societe: ');
	  
		//redirection vers la page de consultation : 
		$_SESSION['search_switch'] = $creation_societe;
		header('location: search_switch');
	}

	// si une modif de client à été effectué : 
	if (!empty($_POST['modif__id']) && !empty($_POST['nom_societe']) && !empty($_POST['ville']) && !empty($_POST['code_postal'])) {

		if (!empty($_POST['telephone'])) 
		{
			$telephone = preg_replace('`[^0-9]`', '', $_POST['telephone']); 
		}
		else 
		{
			$telephone = '';
		}
		if (!empty($_POST['fax'])) 
		{
			$fax = preg_replace('`[^0-9]`', '', $_POST['fax']); 
		}
		else 
		{
			$fax = '';
		}
		//on met dabord à jour dans sossuke : 
		$General->updateAll('client',  mb_strtoupper($_POST['nom_societe'], 'UTF8') , 'client__societe', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $_POST['adresse_1'], 'client__adr1', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $_POST['adresse_2'], 'client__adr2', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $_POST['code_postal'], 'client__cp', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', mb_strtoupper($_POST['ville'], 'UTF8'), 'client__ville', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $telephone, 'client__tel', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $fax, 'client__fax', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $_POST['select_tva'], 'client__tva', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $_POST['intracom_input'], 'client__tva_intracom', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $_POST['commentaire_client'], 'client__comment', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $_POST['vendeur'], 'client__id_vendeur', 'client__id', $_POST['modif__id']);
		
		if (!empty($_POST['ckeck_bloque'])) 
		{
			$General->updateAll('client', 1 , 'client__bloque', 'client__id', $_POST['modif__id']);
		}
		else
		{
			$General->updateAll('client', 0 , 'client__bloque', 'client__id', $_POST['modif__id']);
		}

		$pays = mb_strtoupper($_POST['input_pays'], 'UTF8');
		if ($pays === "FRANCE") {
			$pays = '';
		} else {
			$pays = $pays;
		}
		$General->updateAll('client',  $pays, 'client__pays', 'client__id', $_POST['modif__id']);
		$General->updateAll('client', $_POST['config'], 'client__memo_config', 'client__id', $_POST['modif__id']);
		// //ensuite totoro : 
		$Totoro = new App\Totoro('euro');
		$Totoro->DbConnect();
		$ContactTotoro = new App\Tables\ContactTotoro($Totoro);
		$ContactTotoro->updateAll('client', mb_strtoupper($_POST['nom_societe'], 'UTF8'), 'nsoc',       'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', $_POST['adresse_1'],                          'adr1',       'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', $_POST['adresse_2'],                          'adr2',       'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', $_POST['code_postal'],                        'cp',         'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', mb_strtoupper($_POST['ville'], 'UTF8'),       'ville',      'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', $telephone,                                   'tel',        'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', $fax,                                         'fax',        'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', $_POST['select_tva'],                         'code_tva',   'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', $_POST['intracom_input'],                     'tva',        'id_client', $_POST['modif__id']);
		$ContactTotoro->updateAll('client', $_POST['vendeur'],                            'id_vendeur', 'id_client', $_POST['modif__id']);

		$Contact->update_facturation_auto($_POST['facturation_auto'], $_POST['modif__id']);
		$ContactTotoro->totoro_delete_contact_facturation_and_update($_POST['facturation_auto'], $_POST['modif__id']);

		$facturation_auto = $Contact->get_facturation_auto($_POST['modif__id']);

		$date = date("Y-m-d H:i:s");
		$Pisteur->addPiste($_SESSION['user']->id_utilisateur, $date, $_POST['modif__id'], ' modification de societe: ');
		$alertModif = true;
		//redirection vers la page de consultation : 
		$_SESSION['search_switch'] = $_POST['modif__id'];
		header('location: search_switch');
		
	}

	
	// Donnée transmise au template : 
	echo $twig->render('societe_crea.twig', [
		'user' => $user,
		'alert' => $alert,
		'alertSucces' => $alertSuccess,
		'alert_modif' => $alertModif,
		'tva_list' => $tva_list,
		'user_list' => $user_list,
		'modif' => $modif,
		'facturation_auto' => $facturation_auto,
		'contact_list' => $contact_list
	]);
}
