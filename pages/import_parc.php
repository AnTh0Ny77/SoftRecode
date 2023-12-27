<?php
session_start();
require "./vendor/autoload.php";             // chargement des pages PHP avec use
require "./App/twigloader.php";              // gestion des pages twig
require "./App/Methods/tools_functions.php"; // fonctions Boites à outils 
use App\Apiservice\ApiTest;                  // fonctions API


/*ooo                                                     .        ooooooooo.                                
`888'                                                   .o8        `888   `Y88.                              
 888  ooo. .oo.  .oo.   oo.ooooo.   .ooooo.  oooo d8b .o888oo       888   .d88'  .oooo.   oooo d8b  .ooooo.  
 888  `888P"Y88bP"Y88b   888' `88b d88' `88b `888""8P   888         888ooo88P'  `P  )88b  `888""8P d88' `"Y8 
 888   888   888   888   888   888 888   888  888       888         888          .oP"888   888     888       
 888   888   888   888   888   888 888   888  888       888 .       888         d8(  888   888     888   .o8 
o888o o888o o888o o888o  888bod8P' `Y8bod8P' d888b      "888"      o888o        `Y888""8o d888b    `Y8bod8P' 
						 888                                                                                 
						o888*/

if (empty($_SESSION['user']->id_utilisateur)) header('location: login');

$user_cnx = $_SESSION['user']->id_utilisateur;

//déclaration des instances nécéssaires :
$Database  = new App\Database('devis');
$Database->DbConnect();
$Totoro    = new App\Totoro('euro');
$Totoro->DbConnect();
$UserClass = new App\Tables\User($Database);

//declaration des variables diverses : 
// $msg_info = '<font color=red><b>Page en cour de développement</b></font>';
$msg_info = '';
$html     = '';
$visu_get = TRUE;
$visu_btn_ok = $visu_verif = FALSE;

// recuperations des GET ou POST
$info     = get_post('info', 1);
$btn_ok   = get_post('btn_ok', 2);
$file_csv = get_post('file_csv', 1);

// constantes

// dates par default 
$date_time     = date('Y-m-d H:i:s');
$time          = date('H:i:s');

/*8888 Yb  dP 88""Yb 88     88  dP""b8    db    888888 88  dP"Yb  88b 88 .dP"Y8 
88__    YbdP  88__dP 88     88 dP   `"   dPYb     88   88 dP   Yb 88Yb88 `Ybo." 
88""    dPYb  88"""  88  .o 88 Yb       dP__Yb    88   88 Yb   dP 88 Y88 o.`Y8b 
888888 dP  Yb 88     88ood8 88  YboodP dP""""Yb   88   88  YbodP  88  Y8 8bodP*/
/* 
	Vérification du fichier
	coherence des champs
	integration ligne par ligne dans base
*/

/*""Yb 888888  dP"Yb  88   88 888888 888888 888888 .dP"Y8 
88__dP 88__   dP   Yb 88   88 88__     88   88__   `Ybo." 
88"Yb  88""   Yb b dP Y8   8P 88""     88   88""   o.`Y8b 
88  Yb 888888  `"YoYo `YbodP' 888888   88   888888 8bodP*/

if ($btn_ok)
{
	$Api = new ApiTest();

	$visu_btn_ok = TRUE;
	$visu_get = FALSE;
	$nb_champs_lg_reference = 0;

	$file_tmp = $_FILES['file_csv']['tmp_name'];//fichier_csv = nom du input type file.
	$fic_nom  = $_FILES['file_csv']['name'];//fichier_csv = nom du input type file.
	$html .= 'Nom du fichier : '.$fic_nom.'<br>';
	$nb_lg = $nb_lg_ok = $nb_lg_tete = $nb_lg_vide = $nb_lg_notok = $nb_champs_lg = 0;
	$id_first = $id_last = 0;
	$html_ex_start = $html_ex_end = $html_lg_insert_err = '';
	$contenu  = fopen($file_tmp, "r");
	while (!feof($contenu) ) 
	{	
		$nb_lg ++;
		$lg_ok = TRUE;
		$line = trim(fgets($contenu));
		$line = mb_convert_encoding($line, 'UTF-8', 'auto');
		// print $nb_lg.'<br>'.$line.'<br>'.$line2.'<br>'.substr($line,0,12).'<br>';

		// recherche de ligne ENTETE
		if (substr($line,0,12) == 'mat__cli__id')
		{
			$nb_lg_tete ++;
			$lg_ok = FALSE;
			$line = str_replace(' ', '', $line);
			// nombre de champs (le chiffre doit etre de 13 les champs sont : mat__cli__id;mat__type;mat__marque;mat__model;mat__pn;mat__sn;mat__idnec;mat__date_in;mat__kw_tg;mat__date_offg;mat__contrat_id;mat__contrat_ligne;mat__actif)
			$nb_champs_lg_reference = substr_count($line, ';') + 1;
			$html_ex_start .= $line.'<br>';
			$tab_tete = str_getcsv($line,';');
		}
		

		// recherche de ligne VIDE
		if (strlen($line) < 15) // il ne peut y avoir que des ;  c'est mieux de comparé < 15 que == 0
		{
			$nb_lg_vide ++;
			$lg_ok = FALSE;
		}

		if ($lg_ok and $nb_lg_tete) // integration possible 
		{
			$nb_lg_ok ++;
			// conservation de qq lignes pour affichage
			if ($nb_lg < 4)
			{
				$html_ex_start .= $line.'<br>';
			}
			$html_ex_end = $line.'<br>'; // pour garder la derniere ligne
			// verification de la structure ligne et extraction des champs
			$nb_champs_lg = substr_count($line, ';') + 1;
			if ($nb_champs_lg == $nb_champs_lg_reference)
			{ // le nb de champs est ok => je decompose
				// je met les champs a vide si je ne les trouves pas.
				$mat__cli__id = $mat__type = $mat__marque = $mat__model = $mat__pn = $mat__sn = $mat__idnec = $mat__ident = '';
				$mat__memo = $mat__date_in = $mat__kw_tg = $mat__date_offg = $mat__contrat_id = $mat__contrat_ligne = $mat__actif = '';
				$tab_lg = str_getcsv($line,';');
				for ($i = 0; $i < $nb_champs_lg_reference; $i++) 
				{ // astuce avec $$ pour remplire les variables avec le nom de la ligne d'entete
					$temp = $tab_tete[$i]; $$temp = $tab_lg[$i];
				} // ce qui oblige a la presence d'une ligne entete mais permet le mixte de ses colonnes

				// verification de champs...
				$msg_info_integration = 'Le format de fichier est OK<br>';
				$msg_err_date_offg = $msg_err_date_in = '';
				if (strlen($mat__date_offg) <> 10) // la date n'est pas au bon format ! ou vide => 01/01/2000
				{
					$mat__date_offg = '01/01/2000';
					$msg_err_date_offg = '<font color=red>Au moins une date de fin de garantie n\'etait pas valide ou vide, elle a été rempalcé par 01/01/2000 </font><br>';
				}
				if (strlen($mat__date_in) <> 10) // la date n'est pas au bon format ! ou vide => 01/01/2000
				{
					$mat__date_in = '01/01/2000';
					$msg_err_date_in = '<font color=red>Au moins une date d\'aquisition n\'etait pas valide ou vide, elle a été rempalcé par 01/01/2000 </font><br>';
				}
				$msg_err = $msg_err_date_offg.$msg_err_date_in;
				if (strlen($msg_err) > 0)
					$msg_info_integration = $msg_err;
				if (strlen($mat__actif) == 0)
					$mat__actif = '1'; // c'est 1 par default
				// print dtfr2dtsql($mat__date_in).'<br>';
				// ajout de materiel
				$body = [
				"secret"             => 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528',
				"mat__cli__id"       => $mat__cli__id, 
				"mat__type"          => $mat__type, 
				"mat__marque"        => strtoupper($mat__marque), 
				"mat__model"         => strtoupper($mat__model), 
				"mat__pn"            => $mat__pn, 
				"mat__sn"            => $mat__sn, 
				"mat__idnec"         => $mat__idnec, 
				"mat__ident"         => $mat__ident, 
				"mat__memo"          => $mat__memo,
				"mat__date_in"       => dtfr2dtsql($mat__date_in),
				"mat__kw_tg"         => $mat__kw_tg,
				"mat__date_offg"     => dtfr2dtsql($mat__date_offg),
				"mat__contrat_id"    => $mat__contrat_id,
				"mat__contrat_ligne" => $mat__contrat_ligne,
				"mat__actif"         => $mat__actif 
				]; 
				// var_dump($body); print '<br>';
				// tentative d'integration dans la table.
				$info_api = $Api->postMachine('', $body);
				$code_retour = $info_api['code'];
				if ($code_retour < 300) // c'est ok pas d'erreur
				{
					if ($nb_lg_ok == 1) $id_first = $info_api['data']['mat__id'];
					$id_last = $info_api['data']['mat__id'];
				}
				else 
				{
					$nb_lg_notok ++;
					$nb_lg_ok --;
					$html_lg_insert_err .= $nb_lg.', ';
					// print $code_retour.'<br>';
				}
			}
			else 
			{
				$nb_lg_notok ++;
				$nb_lg_ok --;
			}
		}
		
	}
	fclose($contenu);

	
	// affichage résultat
	$html .= '<hr>';
	$html .= 'Nombre de ligne dans le fichier : '.$nb_lg.'<br>';
	$html .= 'Nombre de ligne de tete : '.$nb_lg_tete.'<br>';
	$html .= 'Nombre de ligne intégrées : '.$nb_lg_ok.'<br>';
	$html .= 'Nombre de ligne avec Erreur : '.$nb_lg_notok; if (strlen($html_lg_insert_err) > 0) $html.=' ( sur les lignes : '.$html_lg_insert_err.' )';$html.='<br>';
	$html .= 'Nombre de ligne vide : '.$nb_lg_vide.'<br>';
	$html .= '<hr>';
	$html .= 'ID de la table materiel de : '.$id_first.' à '.$id_last.'<br>';
	$html .= 'Nombre de champs par ligne : '.$nb_champs_lg_reference.'<br>';
	$html .= '<hr>';
	$html .= '<em><font color=red>Info d\'intégration </font></em><br>';
	$html .= $msg_info_integration.'<br>';
	$html .= '<hr>';
	$html .= '<em>Extrais du fichier intégré (3 premières et dernière lignes) <br>';
	$html .= $html_ex_start.'...<br>'.$html_ex_end.'<br>';
	$html .= '</em>';

}


   /*    888888 888888 88  dP""b8 88  88    db     dP""b8 888888 
  dPYb   88__   88__   88 dP   `" 88  88   dPYb   dP   `" 88__   
 dP__Yb  88""   88""   88 Yb      888888  dP__Yb  Yb  "88 88""   
dP""""Yb 88     88     88  YboodP 88  88 dP""""Yb  YboodP 88888*/

// Donnée transmise au template : 
echo $twig->render('import_parc.twig',
[
'msg_info'    => $msg_info,
'html'        => $html,
'visu_get'    => $visu_get,
'visu_btn_ok' => $visu_btn_ok,
'visu_verif'  => $visu_verif
]);

