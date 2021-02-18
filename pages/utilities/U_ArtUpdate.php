<?php
require "./vendor/autoload.php"; 
require "./App/twigloader.php";
session_start();
require "./App/Methods/tools_functions.php"; // fonctions

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']) ) 
	{ header('location: login'); }
else
{
	//Connexion et requetes :
	$Database = new App\Database('devis');
	$Database->DbConnect();
	$Article = new App\Tables\Article($Database);
	$date_now     = date("Y-m-d");
	$datetime_now = date("Y-m-d H:i:s");
	// traitement du formulaire :
	$message = $msg_info = '';
	// recuperation des paramettres
	$creat = $modif = $GrpModele = $GrpMarque = $GrpPN = FALSE;
	if (isset($_POST['Creat']))     $creat = TRUE;
	if (isset($_POST['Modif']))     $modif = TRUE;
	$id_fmm    = get_post('id_fmm',1);
	$famille   = get_post('famille',1);
	$marque    = get_post('marque',1);
	$modele    = get_post('modele',1);
	$descom    = get_post('descom',1);
	// variables
	$accents = array('/[áàâãªäÁÀÂÃÄ]/u'=>'a','/[ÍÌÎÏíìîï]/u'=>'i','/[éèêëÉÈÊË]/u'=>'e','/[óòôõºöÓÒÔÕÖ]/u'=>'o','/[úùûüÚÙÛÜ]/u'=>'u','/[çÇ]/u' =>'c'); // pour supprimer accent (Attention ICI ca transforme en minuscule)
	$root_doc = './public/_Documents_/Modele_Doc/'; // repertoires pour doc modele


 /*""b8 88""Yb 888888    db    888888 
dP   `" 88__dP 88__     dPYb     88   
Yb      88"Yb  88""    dP__Yb    88   
 YboodP 88  Yb 888888 dP""""Yb   8*/   
	if($creat)
	{ // création de FMM (Model avec famille et marque)
		// Upload de l'image (exemple complet sur https://phpcodeur.net/articles/php/upload)
		// Image (à stocker dans la base)
		if ($_FILES['modele_image']['tmp_name'])
			$blob_image = file_get_contents($_FILES['modele_image']['tmp_name']);
		else
			$blob_image = '';
		// Doc
		$nom_doc  = basename($_FILES['modele_doc']['name']); // nom brut de destination
		$nom_doc  = preg_replace(array_keys($accents), array_values($accents), $nom_doc); // enlever les accents
		$nom_doc  = strtoupper($nom_doc); // mise en majuscule
		$nom_doc  = preg_replace('/([^.a-z0-9]+)/i', '-', $nom_doc); // suppression des caractères autres que lettre chiffres . et remplacement par - 
		// ecriture dans la base
		$last_id_fmm = $Article->fmm_create($famille, $marque, $modele, $blob_image, $nom_doc, $descom);
		// prefixage des nom de doc avec le id du model (format 00000-) (ID complété par zero)
		$last_id_fmm = substr('00000'.$last_id_fmm.'-',-6); // pour completer a zero sur 5 positions et - a la fin
		// Upload de Doc
		$upload_doc = $root_doc.$last_id_fmm.$nom_doc; // nom complet de destination dir et nom de fichier validé
		if (move_uploaded_file($_FILES['modele_doc']['tmp_name'], $upload_doc)) 
			$msg_info .= "Fichier Doc Ajouté<br>";
		else
			$msg_info .= "!Fichier Doc Absent ou trop volumineux.<br>";
		// FLM - il faut afficher le msg_info ou le transmettre .....
		header('location: ArtCatalogueModele');
	}

/*    d8  dP"Yb  8888b.  88 888888
88b  d88 dP   Yb  8I  Yb 88 88__
88YbdP88 Yb   dP  8I  dY 88 88""
88 YY 88  YbodP  8888Y"  88 8*/
	if($modif)
	{ // Modification de Article
		if ($id_fmm) // pour bien verifier qu'il y a un ID FMM
		{
			$maj_image  = $maj_doc = 0;
			$blob_image = $nom_doc = FALSE;
			// Image (à stocker dans la base)
			if ($_FILES['modele_image']['tmp_name'])
			{
				$maj_image = 1;
				$blob_image = file_get_contents($_FILES['modele_image']['tmp_name']);
			}
			
			if ($_FILES['modele_image']['tmp_name'])
			{
				$maj_doc = 1;
				$nom_doc  = basename($_FILES['modele_doc']['name']); // nom brut de destination
			}
			// info pour debug (apparait brievement)
			print 'id : '.$id_fmm.'<br>';
			print 'famille : '.$famille.'<br>';
			print 'marque : '.$marque.'<br>';
			print 'modele : '.$modele.'<br>';
			print 'descom : '.$descom.'<br>';
			print 'blob_image : '.$maj_image.' - '.substr($blob_image,0,50).'<br>';
			print 'nom_doc : '.$maj_doc.' - '.$nom_doc.'<br>';

			$result = $Article->fmm_update($id_fmm, $famille, $marque, $modele, $blob_image, $nom_doc, $descom);
		}
		else 
		{
			$msg_info = "Mise a jour NON Effectué, PAS de ID_FMM";
		}
		header('location: ArtCatalogueModele');
	}

}

?>
