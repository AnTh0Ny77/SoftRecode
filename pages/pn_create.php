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
			   //je crée le pn et je vais vers la page suivante : 
		   }
		}

		//si une mofif de pn à eu lieu 

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
		//deuxième partie recherche de modèle ou absence ( necessite un id de pn dans le poste ) : 
		break;

	case "/SoftRecode/create-pn-third":
		//troisème partie -> formulaire + activation du pn ( necessite un id de pn dans le poste ) / redirection vers le catalogue : 
		break;
	
}