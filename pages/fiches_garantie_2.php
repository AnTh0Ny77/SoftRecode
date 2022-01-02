<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

//URL bloqué si pas de connexion:
if(empty($_SESSION['user']->id_utilisateur)) 
{
	header('location: login');
}

if($_SESSION['user']->user__cmd_acces < 10 )
{
	header('location: noAccess');
}

//Connexion et Entités : 
$Database = new App\Database('devis');
$Client = new App\Tables\Client($Database);
$Keywords = new App\Tables\Keyword($Database);
$Contact = new App\Tables\Contact($Database);
$Article = new App\Tables\Article($Database);
$General = new App\Tables\General($Database);
$Cmd = new App\Tables\Cmd($Database);
$Pisteur = new App\Tables\Pistage($Database);
$Database->DbConnect();
$Stats = new App\Tables\Stats($Database);
$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
$_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
//listes  : 
$clientList = $Client->get_client_devis();
$modeleList = $Keywords->getModele();
$articleTypeList = $Article->getModels();

//si une fiche de garantie a été créee : 
if(!empty($_POST['json_array']) && !empty($_POST['hidden_client']) && !empty($_POST['commande_origine']))
{
	//date du jour:
	$date = date("Y-m-d H:i:s");
	//creation de l'objet retour
	$objectInsert = new stdClass;
	$objectInsert->devis__id = $_POST['commande_origine'];
	$objectInsert->cmd__date_cmd =  $date;
	$objectInsert->devis__id_client_livraison = $_POST['hidden_client'];

	if (!empty($_POST['contact_select'])) 
	{
		$objectInsert->devis__contact_livraison = $_POST['contact_select'];
	}
	else 
	{
		$objectInsert->devis__contact_livraison = null;
	}
	$objectInsert->devis__note_client = null;
	$objectInsert->devis__note_interne = $_POST['commentaire_interne'];
	$objectInsert->devis__user__id = $_SESSION['user']->id_utilisateur;
	$Pisteur->addPiste($_SESSION['user']->id_utilisateur, $date , $_POST['hidden_client'] , ' à entammé la creation d une fiche de garantie de type : '.$_POST['prestation'].' ' );
	//type de retour maintenance ou garantie 
	$type = intval($_POST['prestation']);

	if ($type == 02 )
	{
		$text = 'Garantie';
	}
	elseif($type == 06)
	{
		$text = 'RMA Fournisseur';
	}
	else 
	{
		$text = 'Maintenance';
	}
	
	$retour = $Cmd->makeRetour( $objectInsert , $text,  $type , $_SESSION['user']->id_utilisateur);
	$ligne_json = json_decode($_POST['json_array']);

	//pour chaque item dans le tableau json recupéré : 
	foreach ($ligne_json as $ligne) 
	{
		$objectInsert = new stdClass;
		$objectInsert->idDevis = $retour;
		$objectInsert->prestation = $ligne->typ;
		$objectInsert->designation = $ligne->design;
		$objectInsert->etat = 'NC.';
		$objectInsert->garantie = '';
		$objectInsert->comClient = '';
		$objectInsert->quantite = $ligne->qte;
		$objectInsert->prix = '';
		$objectInsert->idfmm = $ligne->id;
		$objectInsert->extension = '';
		$objectInsert->prixGarantie = '';
		$objectInsert->pn = $ligne->pn;
		$insert = $Cmd->insertLine_fiche($objectInsert);
	}
	
	$retour = $Cmd->GetById($retour);
	$ligne = $Cmd->devisLigne($retour->devis__id);
	$update = $General->updateAll('cmd' , 'CMD' , 'cmd__etat' , 'cmd__id' , $retour->devis__id );
	$_SESSION['garanFiche'] = $retour->devis__id;
	header('location: printFt');
}

// Donnée transmise au template : 
echo $twig->render('fiches_garanties_2.twig',
[
   'user'=>$_SESSION['user'],
   'clientList' => $clientList,
   'modeleList' => $modeleList,
   'articleList' => $articleTypeList
   
]);;
