<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Keyword = new \App\Tables\Keyword($Database);
$Cmd = new \App\Tables\Cmd($Database);
$Pistage = new App\Tables\Pistage($Database);
$Article = new App\Tables\Article($Database);
$General = new App\Tables\General($Database);
$Stocks = new App\Tables\Stock($Database);

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) {
	header('location: login');
}

if (empty($_SESSION['user']->user__matos_acces) or $_SESSION['user']->user__matos_acces < 20 ) {
	header('location: noAccess');
	die();
}


switch ($_SERVER['REQUEST_URI']) 
{
	case "/SoftRecode/create-pn-first":
		//première partie creation et recherche de pn  : 
		$pn_id = false ;
		$famille_list = $Article->get_famille_for_spec_pn();
		$alert = false;

		//si une cretaion de pn à eu lieu : 
		if (!empty($_POST['recherche_pn'])) 
		{

			$_POST['recherche_pn'] = trim($_POST['recherche_pn']);
			if (!empty(preg_match("/[^A-Z0-9-]/", $_POST['recherche_pn']))) {
				$alert =  [
						'alert' => 'Le Nom  ' . $_POST['recherche_pn'] . ' ne peut pas contenir d\'espace , de minuscules ,  de caractères spéciaux( accents inclus ) sauf le tiret et doit faire 5 caractères minimums ',
						'pn' =>  $_POST['recherche_pn'] , 
						'famille' => $_POST['famille_pn'] 
					];
			}
			else {
					$verify_if_exist = $Article->get_pn_byID(trim($_POST['recherche_pn']) ,'"');
			
					if (!empty($verify_if_exist)) 
					{
						$pn_id =  $verify_if_exist;
						
					}
					else 
					{
							trim($_POST['recherche_pn']);
							$pn__id =  $Article->insert_pn($_POST['recherche_pn'] , $_POST['recherche_pn'] ,$_SESSION['user']->id_utilisateur );
			
						if (!empty($_POST['famille_pn'])) 
						{
							$pn_court = preg_replace("#[^!A-Za-z0-9%]+#", "", $_POST['recherche_pn']);
							$General->updateAll('art_pn' , $_POST['famille_pn'], 'apn__famille' , 'apn__pn', $pn_court );
							$objDateTime = new DateTime('NOW');
							$General->updateAll('art_pn' , $objDateTime->format('Y-m-d\TH:i:s.') , 'apn__date_modif' , 'apn__pn', $pn_court );
						}
						
						$_SESSION['pn_id'] = $_POST['recherche_pn']; 	
						header('location: create-pn-second');
						break;
					}
			}


		  
		}

		// Donnée transmise au template : 
		echo $twig->render(
			'pn/create_pn_first.twig',
			[
				'user' => $_SESSION['user'],
				'pn_id' => $pn_id , 
				'famille_list' => $famille_list , 
				'alert' => $alert
			]
		);
		break;




	case "/SoftRecode/create-pn-second":

		if (!empty($_SESSION['pn_id'])) 
		{	
			$pn_id = $_SESSION['pn_id'];
			
			$pn_court = preg_replace("#[^!A-Za-z0-9%]+#", "", $pn_id);
			
			$pn = $Article->get_pn_byID($pn_id);
			$marqueur_famille = 0 ;

			if ($pn->apn__famille == 'PID' or $pn->apn__famille == 'ACC' ) 
			{
				$marqueur_famille = 1 ;
				$model_list = $Article->getModels();
			}	
			else $model_list = $Article->find_models_byFamille($pn->apn__famille);
			
			
			
			$model_relation = $Article->find_by_liaison($pn_court);
			$model_relation = json_encode($model_relation);

			//data nécéssaire pour la déclaration des attributs : 
			$forms_data = $Stocks->get_famille_forms($pn->apn__famille);
			$spec_array = $Stocks->get_specs($pn_id);

			

			echo $twig->render(
				'pn/create_pn_second.twig',
				[
					'user' => $_SESSION['user'],
					'pn_id' => $pn_id , 
					'model_list' => $model_list ,
					'model_relation' => $model_relation, 
					'pn' => $pn , 
					'forms_data' => $forms_data , 
					'spec_array' => $spec_array, 
					'marqueur_famille' => $marqueur_famille
				]
			);
			break;	
		}

	
	case "/SoftRecode/create-pn-specs":

		if (!empty($_POST['model_array'])) 
		{
			$tableau_modele = json_decode($_POST['model_array']);
			foreach ($tableau_modele as $key => $value) 
				{
					if ($value == '100' or $value == '101' ) {
						unset($tableau_modele[$key]);
					}
				}
			if ($_POST['famille'] == 'PID') 
			{
				array_push($tableau_modele , '100' );
			}
			if ($_POST['famille'] == 'ACC') 
			{
				array_push($tableau_modele , '101' );
			}
			$update_models = $Article->insert_liaison_pn_fmm($tableau_modele , preg_replace("#[^!A-Za-z0-9%]+#", "", $_POST['id_pn']));
		}

		

		if (!empty($_POST['id_pn'])) 
		{	
				$pn_id = $_POST['id_pn'];
				$pn_court = preg_replace("#[^!A-Za-z0-9_%]+#", "", $pn_id);
				
				$pn = $Article->get_pn_byID($pn_id);
				$model_list = $Article->getModels();
				$model_relation = $Article->find_one_by_liaison($pn_court);
				$model_relation_object = $model_relation;
				$model_relation = json_encode($model_relation);

				//data nécéssaire pour la déclaration des attributs : 
				$forms_data = $Stocks->get_famille_forms($pn->apn__famille);

				//si pas de formulaire: 
				if (empty($forms_data)) 
				{
					header('location: create-pn-third');
					$_SESSION['redirect_third'] = $_POST['id_pn'];
					break;
				}
				
				$spec_array = $Stocks->get_specs($pn_id);

				if (empty($spec_array)) 
				{
					
					$spec_array = $Stocks->get_spec_model_if_null($model_relation_object->id__fmm);
				}

				$marqueur = false ;
				$marqueur_disabled = false ;
				$marqueur_heritage = false ;
				echo $twig->render(
					'pn/create_pn_specs.twig',
					[
						'user' => $_SESSION['user'],
						'pn_id' => $pn_id , 
						'model_list' => $model_list ,
						'model_relation' => $model_relation, 
						'object' => $pn , 
						'forms_data' => $forms_data , 
						'spec_array' => $spec_array , 
						'marqueur' => $marqueur , 
						'marqueur_disabled' => $marqueur_disabled , 
						'marqueur_heritage' => $marqueur_heritage
					]
				);
			break;	
		}

		

	case "/SoftRecode/create-pn-third":

		// if (empty($_POST['id_pn']))  header('location: create-pn-second');

		if (!empty($_POST['retour_pn'])) 
		{
			$_SESSION['pn_id'] = $_POST['retour_pn'];
			header('location: create-pn-second');
			break;
		}

	
		if (!empty($_SESSION['redirect_third'])) 
		{
			$_POST['id_pn'] = $_SESSION['redirect_third'];
			$_SESSION['redirect_third'] = "";
		}
		

		if (!empty($_POST['id_pn'])) 
		{
			$pn = $Article->get_pn_byID($_POST['id_pn']);
		
			
			$forms_data = $Stocks->get_famille_forms($pn->apn__famille);
			
			$count = 0 ;
			foreach ($forms_data as $data) 
			{	
				$count += 1 ;			
				if (!empty($_POST[$data->aac__cle])) 
				{
					
					if ($count == 1 ) 
					{	
						$delete_all_specs = $Stocks->delete_specs($pn->apn__pn);
					}
					if (is_array($_POST[$data->aac__cle])) {
						foreach ($_POST[$data->aac__cle] as $value) {
							if (!empty($value)) {
								$Stocks->insert_attr_pn($_POST['id_pn'], $data->aac__cle, $value);
							}
						}
					} else {
						$Stocks->insert_attr_pn($_POST['id_pn'], $data->aac__cle, $_POST[$data->aac__cle]);
					}
				}
			}

			$model_relation = $Article->find_one_by_liaison($_POST['id_pn']);

			if (!empty($model_relation->id__fmm)) 
			{
				$Stocks->check_heritage($model_relation->id__fmm , $_POST['id_pn']);
			}

			if (empty($pn->apn__desc_short)) {
				// $short_desc = $Stocks->select_empty_heritage($_POST['id_pn'], true, false);

				// $General->updateAll('art_pn', $short_desc, 'apn__desc_short', 'apn__pn', $_POST['id_pn']);
			}
			
			
		}
	
		

		//si une validation de pn à été posté 
		if (!empty($_POST['pn_id'])) 
		{
		
			$date = date("Y-m-d H:i:s");
			$General->updateAll('art_pn' , $_POST['desc-courte'], 'apn__desc_short' , 'apn__pn', $_POST['pn_id'] );
			$General->updateAll('art_pn', $_POST['desc-longue'], 'apn__desc_long', 'apn__pn', $_POST['pn_id']);
			$General->updateAll('art_pn', $_SESSION['user']->id_utilisateur, 'apn__id_user_modif', 'apn__pn', $_POST['pn_id']);
			$General->updateAll('art_pn', $date, 'apn__date_modif', 'apn__pn', $_POST['pn_id']);
			
				if (!empty($_FILES['modele_image']['tmp_name']))
				{
					$blob_image = file_get_contents($_FILES['modele_image']['tmp_name']);
					$General->updateAll('art_pn', $blob_image, 'apn__image', 'apn__pn', $_POST['pn_id']);
				}
				
			$General->updateAll('art_pn', 1 , 'apn__actif', 'apn__pn', $_POST['pn_id']);
			header('location: ArtCataloguePN');
			break;
		}
	
		if (!empty($_POST['id_pn']))
		{
			$pn = $Article->get_pn_byID($_POST['id_pn']);
		}
		else
		{
			$pn = $Article->get_pn_byID($_POST['pn_id']);
		}

		// $pn->apn__image = base64_encode($pn->apn__image);
		$pn->specs = $Stocks->get_specs_pn_show($pn->apn__pn);
		echo $twig->render(
			'pn/create_pn_third.twig',
			[
				'user' => $_SESSION['user'],
				'pn' => $pn
				
			]
		);
		break;	
	
}