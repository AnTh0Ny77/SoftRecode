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

		if (!empty($_SESSION['pn_id'])) 
		{	
			$pn_id = $_SESSION['pn_id'];
			$pn = $Article->get_pn_byID($pn_id);
			$model_list = $Article->getModels();

			echo $twig->render(
				'pn/create_pn_second.twig',
				[
					'user' => $_SESSION['user'],
					'pn_id' => $pn_id , 
					'model_list' => $model_list ,
					'pn' => $pn
				]
			);
			break;	
		}
		
		
		

	case "/SoftRecode/create-pn-third":
		

		if (empty($_POST['id_pn']))  header('location: create-pn-second');
	

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