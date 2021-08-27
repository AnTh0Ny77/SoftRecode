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



switch ($_SERVER['REQUEST_URI']) 
{
	case "/SoftRecode/create-pn-first":
		//première partie creation et recherche de pn  : 
		$pn_id = false ;
		$famille_list = $Article->getFAMILLE();

		//si une cretaion de pn à eu lieu : 
		if (!empty($_POST['recherche_pn'])) 
		{
		   $verify_if_exist = $Article->get_pn_byID($_POST['recherche_pn']);
		   
		   if (!empty($verify_if_exist)) 
		   {
				$pn_id =  $verify_if_exist;
				
		   }
		   else 
		   {
			   	$pn__id =  $Article->insert_pn($_POST['recherche_pn'] , $_POST['recherche_pn'] ,$_SESSION['user']->id_utilisateur );

				if (!empty($_POST['famille_pn'])) 
				{
					$pn_court = preg_replace("#[^!A-Za-z0-9_%]+#", "", $_POST['recherche_pn']);
					$General->updateAll('art_pn' , $_POST['famille_pn'], 'apn__famille' , 'apn__pn', $pn_court );
				}
				$_SESSION['pn_id'] = $_POST['recherche_pn']; 	
				header('location: create-pn-second');
				break;
		   }
		}

		// Donnée transmise au template : 
		echo $twig->render(
			'pn/create_pn_first.twig',
			[
				'user' => $_SESSION['user'],
				'pn_id' => $pn_id , 
				'famille_list' => $famille_list
			]
		);
		break;




	case "/SoftRecode/create-pn-second":

		if (!empty($_SESSION['pn_id'])) 
		{	
			$pn_id = $_SESSION['pn_id'];
			
			$pn_court = preg_replace("#[^!A-Za-z0-9_%]+#", "", $pn_id);
			
			$pn = $Article->get_pn_byID($pn_id);
			$model_list = $Article->getModels();
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
					'spec_array' => $spec_array
				]
			);
			break;	
		}

	
	case "/SoftRecode/create-pn-specs":

		if (!empty($_POST['model_array'])) 
		{
			$tableau_modele = json_decode($_POST['model_array']);
			$update_models = $Article->insert_liaison_pn_fmm($tableau_modele , $_POST['id_pn'] ) ;
		}
		

		if (!empty($_POST['id_pn'])) 
		{	
				$pn_id = $_POST['id_pn'];
				$pn_court = preg_replace("#[^!A-Za-z0-9_%]+#", "", $pn_id);
				
				$pn = $Article->get_pn_byID($pn_id);
				$model_list = $Article->getModels();
				$model_relation = $Article->find_by_liaison($pn_court);
				$model_relation = json_encode($model_relation);

				//data nécéssaire pour la déclaration des attributs : 
				$forms_data = $Stocks->get_famille_forms($pn->apn__famille);
				
				$spec_array = $Stocks->get_specs($pn_id);
				$marqueur = false ;
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
						'marqueur' => $marqueur
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

		$pn->apn__image = base64_encode($pn->apn__image);

		echo $twig->render(
			'pn/create_pn_third.twig',
			[
				'user' => $_SESSION['user'],
				'pn' => $pn
				
			]
		);
		break;	
	
}