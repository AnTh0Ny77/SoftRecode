<?php

require "vendor/autoload.php";
use App\Controller\RechercheController;
use App\Controller\TicketsFormsController;
use App\Controller\TicketsDisplayController;
use App\Controller\ExtranetController;


 
	$request = $_SERVER['REQUEST_URI'];

// recuperations des param de GET
	$get_request = explode('?' ,$request, 2);
	if (isset($get_request[1])) 
		$get_data = '?' . $get_request[1];
	else 
		$get_data = "";

	$global_request = $get_request[0] . $get_data ; 
	switch($global_request)
	{
		
		//pages :: 
		case '/SoftRecode/'.'':
			require __DIR__ .'/pages/login.php'; break;

		//recherches :
		case '/SoftRecode/search_switch';
			require __DIR__ . '/pages/search_switch.php'; break;
		//recherches :
		case '/SoftRecode/search_switch_notifs'.$get_data;
			require __DIR__ . '/pages/search_switch_notifs.php'; break;
		
		//login/unlog/no access->
		case '/SoftRecode/login':
				require __DIR__ .'/pages/login.php'; break;

		case '/SoftRecode/unlog';
				require __DIR__ .'/pages/utilities/unlog.php'; break;

		case '/SoftRecode/noAccess';
				require __DIR__ .'/pages/noAccess.php'; break;

		case '/SoftRecode/test';
				require __DIR__ .'/pages/test.php'; break;

		//dashboard->
		case '/SoftRecode/dashboard';
			require __DIR__ .'/pages/dashboard.php';break;

		//users->
		case '/SoftRecode/utilisateurs';
				require __DIR__ .'/pages/user.php'; break;

		case '/SoftRecode/User';
				require __DIR__ .'/pages/user.php'; break;
		
		case '/SoftRecode/U_UserUpdate';
				require __DIR__ .'/pages/utilities/U_UserUpdate.php'; break;

		case '/SoftRecode/UserModif';
				require __DIR__ .'/pages/UserModif.php'; break;

		case '/SoftRecode/UserCreat';
				require __DIR__ .'/pages/UserCreat.php'; break;

		case '/SoftRecode/action_utilisateur';
			require __DIR__ . '/pages/action_utilisateur.php';break;

		//articles->
		case '/SoftRecode/catalogue';
			require __DIR__ .'/pages/ArtCataloguePN.php';break;

		case '/SoftRecode/ArtCataloguePN'.$get_data;
			require __DIR__ .'/pages/catalogue_pn.php';break;

		case '/SoftRecode/ArtCatalogueModele';
			require __DIR__ .'/pages/ArtCatalogueModele.php';break;

		case '/SoftRecode/ArtCreation'.$get_data;
			require __DIR__ .'/pages/ArtCreat.php';break;

		case '/SoftRecode/U_ArtUpdate';
			require __DIR__ .'/pages/utilities/U_ArtUpdate.php'; break;

		case '/SoftRecode/TicketVisu';
			require __DIR__ .'/pages/TicketVisu.php'; break;

		case '/SoftRecode/create-pn-first';
			require __DIR__ . '/pages/pn_create.php'; break;
		
		case '/SoftRecode/create-pn-second';
			require __DIR__ . '/pages/pn_create.php'; break;
		
		case '/SoftRecode/create-pn-third';
			require __DIR__ . '/pages/pn_create.php'; break;

		case '/SoftRecode/create-pn-specs';
			require __DIR__ . '/pages/pn_create.php'; break;

		case '/SoftRecode/create-models';
			require __DIR__ . '/pages/create_models.php';break;

		case '/SoftRecode/recherche-articles-familles';
			echo RechercheController::recherche_famille();
			break;

		case '/SoftRecode/tickets-forms';
			echo TicketsFormsController::forms();
			break;

		case '/SoftRecode/tickets-lignes';
			echo TicketsFormsController::formsLigne();
			break;
		
		case '/SoftRecode/tickets-select-type';
			echo TicketsFormsController::selectTicketsType();
			break;

		case '/SoftRecode/tickets-handle-forms';
			echo TicketsFormsController::FormsMarker();
			break;

		case '/SoftRecode/tickets-display-list';
			echo TicketsDisplayController::displayTicketList();
			break;

		case '/SoftRecode/tickets-display'.$get_data;
			echo TicketsDisplayController::displayTicket($_GET);
			break;

		case '/SoftRecode/tickets-post-data';
			echo TicketsFormsController::formsHandler();
			break;

		case '/SoftRecode/extra-login';
			echo ExtranetController::login();
			break;

		case '/SoftRecode/recherche-articles-specs';
			echo RechercheController::recherche_spec();
			break;
		
		case '/SoftRecode/recherche-articles-results';
			echo RechercheController::recherche_results();
			break;
			
		//devis-> 
		case '/SoftRecode/nouveauDevis';
			require __DIR__ .'/pages/nouveauDevis.php';break;

		case '/SoftRecode/mesDevis';
			require __DIR__ .'/pages/mesDevis.php'; break;

		case '/SoftRecode/pdf';
			require __DIR__ .'/pages/utilities/pdf.php'; break;

		case '/SoftRecode/voirDevis';
			require __DIR__ .'/pages/utilities/viewPdf.php'; break;

		case '/SoftRecode/printDevis2';
			require __DIR__ .'/pages/utilities/PdfDevis2.php'; break;
		 
		case '/SoftRecode/contactCrea';
			require __DIR__ .'/pages/contactCrea.php'; break;

		case '/SoftRecode/societe_crea';
			require __DIR__ .'/pages/societe_crea.php'; break;
		
		case '/SoftRecode/DevisV2';
			require __DIR__ .'/pages/NdevisPlusPro.php'; break;

		case '/SoftRecode/ligneDevisV2';
			require __DIR__ .'/pages/NligneDevisV2.php'; break;
		
		case '/SoftRecode/validation_module';
			require __DIR__ .'/pages/module_validation.php'; break;

		case '/SoftRecode/admin_client';
			require __DIR__ .'/pages/admin_client.php'; break;

		case '/SoftRecode/admin_contact';
			require __DIR__ .'/pages/admin_contact.php'; break;
			
		//transport / fiches de travail->
		case '/SoftRecode/transport';
			require __DIR__ .'/pages/transport2.php'; break;

		case '/SoftRecode/transport2';
			require __DIR__ .'/pages/transport2.php'; break;

		case '/SoftRecode/commande';
			require __DIR__ .'/pages/commandValid.php'; break;

		case '/SoftRecode/etiquettes';
			require __DIR__ . '/pages/etiquettes.php';break;
			

		case '/SoftRecode/validation_devis';
			require __DIR__ .'/pages/validation_devis_v2.php'; break;
		
		case '/SoftRecode/ficheTravail';
			require __DIR__ .'/pages/ficheT.php'; break;
	 
		case '/SoftRecode/adminFiche';
			require __DIR__ .'/pages/ficheAdministration.php'; break;
		
		case '/SoftRecode/fichesEnCours';
			require __DIR__ .'/pages/fichesEnCours.php'; break;
		
		case '/SoftRecode/garantieCreation';
			require __DIR__ .'/pages/garantieCreation.php'; break;

		case '/SoftRecode/fiches_garantie';
			require __DIR__ .'/pages/fiches_garantie_2.php'; break;

		case '/SoftRecode/garantiesFiches';
			require __DIR__ .'/pages/garantiesFiches.php'; break;

		//facture/compta->
		case '/SoftRecode/facture';
			require __DIR__ .'/pages/facture.php'; break;

		case '/SoftRecode/export';
			require __DIR__ .'/pages/export.php'; break;
		
		case '/SoftRecode/abonnement';
			require __DIR__ .'/pages/abonnement.php'; break;

		case '/SoftRecode/abonnementNouveau';
			require __DIR__ .'/pages/abonnementNouveau.php'; break;

		case '/SoftRecode/abonnementAdmin';
			require __DIR__ .'/pages/abonnementAdmin.php'; break;

		case '/SoftRecode/ajoutMachine';
			require __DIR__ .'/pages/ajoutMachine.php'; break;
		
		case '/SoftRecode/adminMachine';
			require __DIR__ .'/pages/adminMachine.php'; break;

		case '/SoftRecode/facture_auto';
		require __DIR__ .'/pages/facture_auto.php'; break;

		case '/SoftRecode/archiveFacture';
		require __DIR__ .'/pages/archiveFacture.php'; break;

		//Ajax ::
		case '/SoftRecode/AjaxSociete';
			require __DIR__ .'/pages/ajax/ajaxClient.php'; break;

		case '/SoftRecode/AjaxVisio';
			require __DIR__ .'/pages/ajax/AjaxVisioPDF.php'; break;

		case '/SoftRecode/AjaxVisio2';
			require __DIR__ .'/pages/ajax/ajax_visio_devis.php'; break;

		case '/SoftRecode/AjaxFT';
			require __DIR__ .'/pages/ajax/AjaxVisionFT.php'; break;
		
		case '/SoftRecode/AjaxTransport';
			require __DIR__ .'/pages/ajax/ajaxVisionTransport.php'; break;

		case '/SoftRecode/AjaxLigneFT';
			require __DIR__ .'/pages/ajax/AjaxLigneFT.php'; break;
		
		case '/SoftRecode/AjaxDevis';
			require __DIR__ .'/pages/ajax/ajaxDevis.php'; break;
		
		case '/SoftRecode/AjaxDevisFacture';
			require __DIR__ .'/pages/ajax/ajaxDevisFacture.php'; break;

		case '/SoftRecode/AjaxStatDevis';
			require __DIR__ .'/pages/ajax/ajaxChartsDevis.php'; break;

		case '/SoftRecode/AjaxSaisie';
			require __DIR__ .'/pages/ajax/ajaxSaisie.php'; break;
		
		case '/SoftRecode/AjaxClientContact';
			require __DIR__ .'/pages/ajax/ajaxClientContact.php'; break;

		//integration des pn :	
		//function utilisées dans les requetes du devis pour l'integration des pn : 
		//creation
		case '/SoftRecode/AjaxPn';
			require __DIR__ .'/pages/ajax/ajaxPn.php'; break;
		//modif 
		case '/SoftRecode/Ajax-pn-ligne';
			require __DIR__ . '/pages/ajax/ajax_line_pn_list.php';break;
		///////////////////////////////////////////////////////
		case '/SoftRecode/Ajax-pn-id';
			require __DIR__ . '/pages/ajax/ajax_pn_id.php';break;
		//////////////////////////////////////////////////////



		case '/SoftRecode/Ajax_search_client';
			require __DIR__ .'/pages/ajax/ajax_search_client.php'; break;

		case '/SoftRecode/Ajax_search_client_devis';
			require __DIR__ . '/pages/ajax/ajax_search_client_devis.php'; break;
		
		case '/SoftRecode/Ajax_update_etat';
			require __DIR__ . '/pages/ajax/ajax_update_etat.php'; break;

		case '/SoftRecode/ajaxLigneTransport';
			require __DIR__ .'/pages/ajax/ajaxLigneTransport.php'; break;

		case '/SoftRecode/createNew';
			require __DIR__ .'/pages/ajax/ajaxCreate.php'; break;

		case '/SoftRecode/createClient';
			require __DIR__ .'/pages/ajax/ajaxCreateClient.php'; break;

		case '/SoftRecode/tableContact';
			require __DIR__ .'/pages/ajax/ajaxTableContact.php'; break;

		case '/SoftRecode/choixContact';
			require __DIR__ .'/pages/ajax/ajaxcontactChoix.php'; break;

		case '/SoftRecode/createContact';
			require __DIR__ .'/pages/ajax/ajaxcreateContact.php'; break;

		case '/SoftRecode/choixLivraison';
			require __DIR__ .'/pages/ajax/ajaxChoixLivraison.php'; break;

		case '/SoftRecode/factureVisio';
			require __DIR__ .'/pages/ajax/ajaxVisioFacture.php'; break;

		case '/SoftRecode/AvoirVisio';
			require __DIR__ .'/pages/ajax/ajaxVisoAvoir.php'; break;

		case '/SoftRecode/ajax_crea_contact_devis';
			require __DIR__ .'/pages/ajax/ajax_crea_contact_devis.php'; break; 
		
		case '/SoftRecode/ajax_update_quantite_ligne';
			require __DIR__ .'/pages/ajax/ajax_update_quantite_ligne.php'; break; 

		case '/SoftRecode/ajax_idfmm';
			require __DIR__ .'/pages/ajax/ajax_idfmm.php'; break;

		//Traitement et Utilities : 
		case '/SoftRecode/pdfTravail';
			require __DIR__ .'/pages/utilities/pdfTravail.php'; break;

		case '/SoftRecode/pdfFacture';
			require __DIR__ .'/pages/utilities/pdfFacture.php'; break;

		case '/SoftRecode/pdfBL';
			require __DIR__ .'/pages/utilities/pdfBL.php'; break;

		case '/SoftRecode/printFt';
			require __DIR__ .'/pages/utilities/printFT.php'; break;

		case '/SoftRecode/printBl';
			require __DIR__ .'/pages/utilities/printBL.php'; break;

		case '/SoftRecode/printFTC';
			require __DIR__ .'/pages/utilities/printFTC.php'; break;

		case '/SoftRecode/printABN';
			require __DIR__ .'/pages/utilities/printABN.php'; break;
			
		case '/SoftRecode/PRINTADMIN';
			require __DIR__ .'/pages/utilities/PRINTADMIN.php'; break;

		case '/SoftRecode/PrintAvoir';
			require __DIR__ .'/pages/utilities/printAvoir.php'; break;

		case '/SoftRecode/PRINTADMINAVOIR';
			require __DIR__ .'/pages/utilities/printAvoirAdmin.php'; break;
		
		case '/SoftRecode/printContrat';
			require __DIR__ .'/pages/utilities/printContrat.php'; break;

		
		
		
		case '/SoftRecode/PRINTFORMAT';
			require __DIR__ .'/pages/utilities/printFormat.php'; break;

		case '/SoftRecode/stat'.$get_data;
			require __DIR__ .'/pages/statistiques.php'; break;

		case '/SoftRecode/restore';
			require __DIR__ . '/pages/restore_fact.php';break;
		
		case '/SoftRecode/font';
			require __DIR__ .'/vendor/tecnickcom/tcpdf/fonts/convertfont.php'; break;

		//404::
		default:
				header('HTTP/1.0 404 not found');
				require  __DIR__ .'/pages/error404.php';
				break;
	}

?>
