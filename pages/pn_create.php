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

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) {
	header('location: login');
}



switch ($_SERVER['REQUEST_URI']) 
{
	case "/SoftRecode/create-pn-first":
		//première partie creation et recherche de pn  : 
		$pn_id = false ;

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
				'pn_id' => $pn_id
			]
		);
		break;




	case "/SoftRecode/create-pn-second":

		if (!empty($_POST['retour_pn'])) 
		{
			$_SESSION['pn_id'] = $_POST['retour_pn'];
		}

		if (!empty($_SESSION['pn_id'])) 
		{	
			$pn_id = $_SESSION['pn_id'];
			$pn_court = preg_replace("#[^!A-Za-z0-9_%]+#", "", $pn_id);
			
			$pn = $Article->get_pn_byID($pn_id);
			$model_list = $Article->getModels();
			$model_relation = $Article->find_by_liaison($pn_court);
			$model_relation = json_encode($model_relation);

			echo $twig->render(
				'pn/create_pn_second.twig',
				[
					'user' => $_SESSION['user'],
					'pn_id' => $pn_id , 
					'model_list' => $model_list ,
					'model_relation' => $model_relation, 
					'pn' => $pn
				]
			);
			break;	
		}
		
		
		

	case "/SoftRecode/create-pn-third":
		

		if (empty($_POST['id_pn']))  header('location: create-pn-second');

		if (!empty($_POST['model_array'])) 
		{
			
			$tableau_modele = json_decode($_POST['model_array']);
			$update_models = $Article->insert_liaison_pn_fmm($tableau_modele , $_POST['id_pn'] ) ;
		}

		//si une validation de pn à été posqté 
		if (!empty($_POST['pn_id'])) 
		{
			$date = date("Y-m-d H:i:s");
			$General->updateAll('art_pn' , $_POST['desc-courte'], 'apn__desc_short' , 'apn__pn', $_POST['pn_id'] );
			$General->updateAll('art_pn', $_POST['desc-longue'], 'apn__desc_long', 'apn__pn', $_POST['pn_id']);
			$General->updateAll('art_pn', $_SESSION['user']->id_utilisateur, 'apn__id_user_modif', 'apn__pn', $_POST['pn_id']);
			$General->updateAll('art_pn', $date, 'apn__date_modif', 'apn__pn', $_POST['pn_id']);
			$General->updateAll('art_pn', 1 , 'apn_actif', 'apn__pn', $_POST['pn_id']);

			header('location: ArtCataloguePN');
			break;
		}
	

		$pn = $Article->get_pn_byID($_POST['id_pn']);

		echo $twig->render(
			'pn/create_pn_third.twig',
			[
				'user' => $_SESSION['user'],
				'pn' => $pn
				
			]
		);
		break;	
	
}