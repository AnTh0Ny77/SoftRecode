<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;
use stdClass;

/*.                    .    o8o            oooo            
	 .888.                 .o8    `"'            `888            
	.8"888.     oooo d8b .o888oo oooo   .ooooo.   888   .ooooo.  
   .8' `888.    `888""8P   888   `888  d88' `"Y8  888  d88' `88b 
  .88ooo8888.    888       888    888  888        888  888ooo888 
 .8'     `888.   888       888 .  888  888   .o8  888  888    .o 
o88o     o8888o d888b      "888" o888o `Y8bod8P' o888o `Y8bod8*/
 

class Article extends Table 
{
  public string $Table;
  public Database $Db;
  private object $Request;

  public function __construct($db) 
  {
	$this->Db = $db;
  }

  public function getAll() 
  { /* ancienne table article2 */
	$request =$this->Db->Pdo->query('SELECT DISTINCT art_type  FROM articles2 ORDER BY  art_type ASC');
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	return $data;
  }

  public function getART($art_filtre=FALSE)
  { /* nouvelle tables article (composé de art_fmm, art_pn, art_marque) */
	$SQL_WHERE = $SQL_GROUPBY = $SQL_ORDER = '';
	// order par defaut si pas de filtre voir les X dernières créations
	$SQL_ORDER = 'ORDER BY art_pn.apn__date_modif DESC ';
	if ($art_filtre)
	{
	  $first_digit = substr($art_filtre,0,1);
	  $art_filtre_special = trim(substr($art_filtre,1));
	  $SQL_ORDER = 'ORDER BY keyword.kw__ordre ASC, art_fmm.afmm__modele ASC, art_pn.apn__pn ASC ';
	  switch ($first_digit) 
	  {
		case ",": // Famille
		  $SQL_WHERE .= 'WHERE ( keyword.kw__lib     like(\''.$art_filtre_special.'\') ) ';
		  break;
		case ";": // Marque
		  $SQL_WHERE .= 'WHERE ( art_marque.am__marque like(\''.$art_filtre_special.'\') ) ';
		  break;
		case ":": // Modele
		  $SQL_WHERE .= 'WHERE ( art_fmm.afmm__modele  like(\''.$art_filtre_special.'\') ) ';
		  break;
		case "!": // PN (recherche sur le PN court)
		  $art_filtre_court = preg_replace("#[^!A-Za-z0-9%]+#", "", $art_filtre_special); // pour avoir le PN court (que du alpha et nombre)
		  $SQL_WHERE .= 'WHERE ( art_pn.apn__pn        like(\''.$art_filtre_court.'\') ) ';
		  break;
		default:
		  // decoupage du filtre par mots
		  $x_filtre       = trim($art_filtre);
		  $x_filtre       = str_replace("-", " ", $x_filtre); // le - est un char special et donc non vue dans str-word-count
		  $nb_mots_filtre = str_word_count($x_filtre, 0, '0123456789'); // Nombre de mots 
		  $mots_filtre    = str_word_count($x_filtre, 1, '0123456789'); // renvoie un tableau - le 3eme paramettre est pour prendre en compte les chiffre comme mot et non comme separateur
		  $opperateur     = 'AND ';
		  if ($nb_mots_filtre > 0) $mode_filtre = TRUE; else $mode_filtre = FALSE; 
		  // Construction du WHERE
		  if ($mode_filtre) 
		  { // boucle avec les # mots et le bon opperateur Booleen
			$SQL_WHERE .= 'WHERE ';
			$SQL_WHERE .= '( ';
			for ($i = 0; $i < $nb_mots_filtre; $i++) 
			{
			  $SQL_WHERE .= '( ';
			  $SQL_WHERE .= 'art_fmm.afmm__modele     like(\'%'.$mots_filtre[$i].'%\') OR ';
			  $SQL_WHERE .= 'art_marque.am__marque    like(\'%'.$mots_filtre[$i].'%\') OR ';
			  $SQL_WHERE .= 'art_pn.apn__pn           like(\'%'.$mots_filtre[$i].'%\') OR ';
			  $SQL_WHERE .= 'keyword.kw__lib          like(\'%'.$mots_filtre[$i].'%\') OR ';
			  $SQL_WHERE .= 'art_pn.apn__desc_short   like(\'%'.$mots_filtre[$i].'%\') ';
			  $SQL_WHERE .= ') '.$opperateur;
			}
			$SQL_WHERE = substr($SQL_WHERE,0,-1*strlen($opperateur)); // supprimer le dernier opperateur.
			$SQL_WHERE .= ') ';
		  }
	  }
	}
	$SQL = 'SELECT 
	keyword.kw__lib as Famille, art_marque.am__marque as Marque, art_fmm.afmm__modele as Modele, 
	art_pn.apn__pn_long as PN, art_pn.apn__desc_short as Info, 
	art_pn.apn__id_user_modif as ID_User_Modif, art_pn.apn__date_modif as Date_Modif,
	art_fmm.afmm__image as FMM_Image, art_fmm.afmm__doc as FMM_Doc,
	art_pn.apn__image as PN_Image, 	art_pn.apn__doc as PN_Doc
	FROM art_pn
	INNER JOIN art_marque ON art_fmm.afmm__marque = art_marque.am__id
	INNER JOIN keyword ON art_fmm.afmm__famille = keyword.kw__value and keyword.kw__type = \'famil\' '.
	$SQL_WHERE.$SQL_ORDER; // LIMIT 0,50
	//print $SQL; // pour debug
	$request =$this->Db->Pdo->query($SQL);
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	$TA = array();
	$i = 0;
	 foreach($data as $row => $ligne) 
	{
	  // je cherche la bonne image en priorité le PN si non le MODELE
	  $Image = '';
	  if ($ligne->FMM_Image) $Image = 'Modele_Image/'.$ligne->FMM_Image;
	  if ($ligne->PN_Image)  $Image = 'PN_Image/'.$ligne->PN_Image;
	  if ($Image) $Image = '<img src="public/_Documents_/'.$Image.'" class=" ml-5 my-2" height="55">';
	  // je cherche la doc en priorité le PN si non le MODELE
	  $Doc = '';
	  if ($ligne->FMM_Doc) $Doc = 'Modele_Doc/'.$ligne->FMM_Doc;
	  if ($ligne->PN_Doc ) $Doc = 'PN_Doc/'.$ligne->PN_Doc;
	  if ($Doc) $Doc = 'public/_Documents_/'.$Doc;
	  $TA[] = array (
		'Famille'     => $ligne->Famille,
		'Marque'      => $ligne->Marque,
		'Modele'      => $ligne->Modele,
		'PN'          => $ligne->PN,
		'Info'        => $ligne->Info,
		'ID_User_Modif' => $ligne->ID_User_Modif,
		'Date_Modif'  => $ligne->Date_Modif,
		'Image'       => $Image,
		'Doc'         => $Doc
	  );
	  $i++;
	}
	//exit;
	return $TA;
  }

  public function select_all_pn()
  {

		$SQL = 'SELECT DISTINCT  a.* , u.prenom , u.nom , k.kw__lib as famille
		FROM art_pn as a  
		LEFT JOIN utilisateur as u on  u.id_utilisateur = a.apn__id_user_modif
		LEFT JOIN keyword as k ON ( k.kw__type = "famil" AND k.kw__value =  a.apn__famille ) 
		ORDER BY apn__date_modif DESC LIMIT 50';
		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetchAll(PDO::FETCH_OBJ);
		
		foreach ($data as $pn) 
		{
			$SQL = 'SELECT  id__fmm 
			FROM liaison_fmm_pn 
			WHERE id__pn = "'.$pn->apn__pn.'"
			LIMIT 5';
			$request = $this->Db->Pdo->query($SQL);
			$liaison = $request->fetchAll(PDO::FETCH_OBJ);

			if(count($liaison) > 1 )
				$pn->modele = null ; $pn->count_relation =  intval(count($liaison));
			if(count($liaison) == 1 )
				$pn->modele = $liaison[0]->id__fmm ; $pn->count_relation =  intval(count($liaison));
			if(count($liaison) == 0 )
				$pn->modele = null ; $pn->count_relation =  intval(count($liaison));

			$SQL = 'SELECT a.afmm__modele , m.am__marque as marque
			FROM art_fmm as a  
			LEFT JOIN art_marque as m on ( m.am__id = a.afmm__marque ) 
			WHERE a.afmm__id = "' . $pn->modele . '"
			';
			$request = $this->Db->Pdo->query($SQL);
			$model_data = $request->fetch(PDO::FETCH_OBJ);
			if (!empty($model_data))
			{
				$pn->modele = $model_data->afmm__modele; 
				$pn->marque = $model_data->marque;
			}
				
		}
			
		return $data;
  }

	public function select_all_model()
	{

		$SQL = 'SELECT DISTINCT  a.* , u.prenom , u.nom , k.kw__lib as famille
		FROM art_fmm as a  
		LEFT JOIN utilisateur as u on  u.id_utilisateur = a.afmm__id_user_creat
		LEFT JOIN keyword as k ON ( k.kw__type = "famil" AND k.kw__value =  a.afmm__famille ) 
		LEFT JOIN art_marque as m on ( m.am__id = a.afmm__marque ) 
		ORDER BY afmm__dt_modif DESC LIMIT 25';
		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetchAll(PDO::FETCH_OBJ);

		foreach ($data as $model) {
			$SQL = 'SELECT  id__pn 
			FROM liaison_fmm_pn 
			WHERE id__fmm = "' . $model->afmm__id . '"
			LIMIT 5';
			$request = $this->Db->Pdo->query($SQL);
			$liaison = $request->fetchAll(PDO::FETCH_OBJ);

			if (count($liaison) > 1)
			$model->pn = null;
			$model->count_relation =  intval(count($liaison));
			if (count($liaison) == 1)
			$model->pn = $liaison[0]->id__pn;
			$model->count_relation =  intval(count($liaison));
			if (count($liaison) == 0)
			$model->pn = null;
			$model->count_relation =  intval(count($liaison));

		}

		return $data;
	}

  public function get_catalogue_fmm($filtre=FALSE)
  { /* nouvelle tables article (composé de art_fmm, art_marque) */
	$SQL_WHERE = $SQL_GROUPBY = $SQL_ORDER = '';
	// order par defaut si pas de filtre voir les X dernières créations
	$SQL_ORDER = 'ORDER BY art_fmm.afmm__id DESC ';
	if ($filtre)
	{
		$first_digit = substr($filtre,0,1);
		$filtre_special = trim(substr($filtre,1));
		$SQL_ORDER = 'ORDER BY keyword.kw__ordre ASC, art_fmm.afmm__modele ASC ';
		switch ($first_digit) 
		{
		case ",": // Famille
		  $SQL_WHERE .= 'WHERE ( keyword.kw__lib     like(\''.$filtre_special.'\') ) ';
		  break;
		case ";": // Marque
		  $SQL_WHERE .= 'WHERE ( art_marque.am__marque like(\''.$filtre_special.'\') ) ';
		  break;
		case ":": // Modele
		  $SQL_WHERE .= 'WHERE ( art_fmm.afmm__modele  like(\''.$filtre_special.'\') ) ';
		  break;
		default:
		  // decoupage du filtre par mots
		  $x_filtre       = trim($filtre);
		  $x_filtre       = str_replace("-", " ", $x_filtre); // le - est un char special et donc non vue dans str-word-count
		  $nb_mots_filtre = str_word_count($x_filtre, 0, '0123456789'); // Nombre de mots 
		  $mots_filtre    = str_word_count($x_filtre, 1, '0123456789'); // renvoie un tableau - le 3eme paramettre est pour prendre en compte les chiffre comme mot et non comme separateur
		  $opperateur     = 'AND ';
		  if ($nb_mots_filtre > 0) $mode_filtre = TRUE; else $mode_filtre = FALSE; 
		  // Construction du WHERE
		  if ($mode_filtre) 
		  { // boucle avec les # mots et le bon opperateur Booleen
			$SQL_WHERE .= 'WHERE ';
			$SQL_WHERE .= '( ';
			for ($i = 0; $i < $nb_mots_filtre; $i++) 
			{
			  $SQL_WHERE .= '( ';
			  $SQL_WHERE .= 'art_fmm.afmm__modele     like(\'%'.$mots_filtre[$i].'%\') OR ';
			  $SQL_WHERE .= 'art_marque.am__marque    like(\'%'.$mots_filtre[$i].'%\') OR ';
			  $SQL_WHERE .= 'keyword.kw__lib          like(\'%'.$mots_filtre[$i].'%\') ';
			  $SQL_WHERE .= ') '.$opperateur;
				}
			$SQL_WHERE = substr($SQL_WHERE,0,-1*strlen($opperateur)); // supprimer le dernier opperateur.
			$SQL_WHERE .= ') ';
		  }
	  }
	}
	$SQL = 'SELECT 
	art_fmm.afmm__id as FMM_ID, keyword.kw__lib as Famille, art_marque.am__marque as Marque, art_fmm.afmm__modele as Modele, 
	art_fmm.afmm__image as FMM_Image, art_fmm.afmm__doc as FMM_Doc, art_fmm.afmm__design_com as Descom, 
	art_fmm.afmm__prix_conseil AS Prix_conseil, art_fmm.afmm__actif AS Actif,
	CONCAT(SUBSTR(user_creat.prenom FROM 1 FOR 1),\'. \',user_creat.nom) AS Creat_nom, SUBSTR(art_fmm.afmm__dt_creat FROM 1 FOR 16) AS Creat_dt,
	CONCAT(SUBSTR(user_modif.prenom FROM 1 FOR 1),\'. \',user_modif.nom) AS Modif_nom, SUBSTR(art_fmm.afmm__dt_modif FROM 1 FOR 16) AS Modif_dt 
	FROM art_fmm
	LEFT JOIN art_marque ON art_fmm.afmm__marque = art_marque.am__id 
	LEFT JOIN keyword ON art_fmm.afmm__famille = keyword.kw__value and keyword.kw__type = \'famil\' 
	LEFT JOIN utilisateur AS user_creat ON user_creat.id_utilisateur = art_fmm.afmm__id_user_creat 
	LEFT JOIN utilisateur AS user_modif ON user_modif.id_utilisateur = art_fmm.afmm__id_user_modif '.
	$SQL_WHERE.$SQL_ORDER.' LIMIT 90'; // 'LIMIT 0,50 ';
	debug($SQL); // pour debug
	$request =$this->Db->Pdo->query($SQL);
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	$TM = array();
	$i = 0;
	 foreach($data as $row => $ligne) 
	{
	  // je cherche l image
	  $Image = '';
	  if ($ligne->FMM_Image) $Image = base64_encode($ligne->FMM_Image); // pour image en base (codé sur un BLOB)
	  // je cherche la doc 
	  $Doc = '';
	  if ($ligne->FMM_Doc) $Doc = 'Modele_Doc/'.$ligne->FMM_Doc;
	  if ($Doc) $Doc = 'public/_Documents_/'.$Doc;
	  // creation du tableau a renvoyer 
	  $TM[] = array (
		'ID'          => $ligne->FMM_ID,
		'Famille'     => $ligne->Famille,
		'Marque'      => $ligne->Marque,
		'Modele'      => $ligne->Modele,
		'Descom'      => $ligne->Descom,
		'Image'       => $Image,
		'Doc'         => $Doc,
		'Creat_nom'   => $ligne->Creat_nom,
		'Creat_dt'    => $ligne->Creat_dt,
		'Modif_nom'   => $ligne->Modif_nom,
		'Modif_dt'    => $ligne->Modif_dt,
		'Prix_Conseil'=> $ligne->Prix_conseil,
		'Actif'       => $ligne->Actif
	  );
	  $i++;
	}
	//exit;
	return $TM;
  }

  public function getPARTS($art_filtre, $art_modele=FALSE)
  { /* Liste dans Art_Parts les PN en lien avec LE filtre (Modele ou PN) */
	// cet fonction n'est appelé que si il y a un filtre Modele ou PN
	$first_digit = substr($art_filtre,0,1);
	$art_filtre_special = trim(substr($art_filtre,1));
	$art_filtre_court = preg_replace("#[^!A-Za-z0-9%]+#", "", $art_filtre_special); // pour avoir le PN court (que du alpha et nombre)
	$SQL_WHERE = 'WHERE ( FALSE ) ';
	if($first_digit == "!") // PN (recherche sur le PN court) et sur modele
	{
	  $SQL_WHERE  = 'WHERE ( art_parts.apa__pa2_modele like(\''.$art_modele.'\') OR ';
	  $SQL_WHERE .= 'art_parts.apa__pa2_pn like(\''.$art_filtre_court.'\') ) ';
	}
	if($first_digit == ":") // MODELE (recherche sur le Modele)
	{
	  $SQL_WHERE = 'WHERE ( art_parts.apa__pa2_modele like(\''.$art_filtre_special.'\') ) '; // recherche au mini sur le Modele
	}

	$SQL = 'SELECT 
	keyword.kw__lib as Famille, art_fmm.afmm__famille as Famille_Key, art_marque.am__marque as Marque, art_fmm.afmm__modele as Modele,
	art_pn.apn__pn_long as PN, art_pn.apn__desc_short as Info, art_pn.apn__id_user_modif as ID_User_Modif,
	art_pn.apn__date_modif as Date_Modif, art_fmm.afmm__image as FMM_Image, art_fmm.afmm__doc as FMM_Doc,
	art_pn.apn__image as PN_Image, art_pn.apn__doc as PN_Doc, art_parts.apa__pa2_info as Parts_Info
	FROM art_parts
	INNER JOIN art_pn ON art_parts.apa__pn = art_pn.apn__pn
	INNER JOIN art_marque ON art_fmm.afmm__marque = art_marque.am__id
	INNER JOIN keyword ON art_fmm.afmm__famille = keyword.kw__value and keyword.kw__type = \'famil\' '.
	$SQL_WHERE.
	'ORDER BY keyword.kw__ordre ASC, art_pn.apn__pn ASC '; // limit 50

	// print $SQL; // pour debug
	$request =$this->Db->Pdo->query($SQL);
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	$TA_ACC = $TA_CON = $TA_PID = array(); // 3 tableaux pour accessoire, consommable et pieces
	$i = 0;
	// il peut y avoir des doublons dans le resultat de la requette.
	// pas possible de trouver une requette qui ne donne pas de doublon ??? Distinc tou group by ne fonctone pas  ???
	// la bonne nouvelle c'est que c'est trié et donc je supprime le doublon si vois passer un double ...
	$old_pn = '';
	foreach($data as $row => $ligne) 
	{
	  if ($old_pn <> $ligne->PN) // pour eviter les doublons...
	  { // je cherche la bonne image en priorité le PN si non le MODELE
		$Image = '';
		if ($ligne->FMM_Image) $Image = 'Modele_Image/'.$ligne->FMM_Image;
		if ($ligne->PN_Image ) $Image = 'PN_Image/'.$ligne->PN_Image;
		if ($Image) $Image = '<img src="public/_Documents_/'.$Image.'" class="mx-0 my-0" height="55">';
		$Doc = '';
		if ($ligne->FMM_Doc) $Doc = 'Modele_Doc/'.$ligne->FMM_Doc;
		if ($ligne->PN_Doc ) $Doc = 'PN_Doc/'.$ligne->PN_Doc;
		if ($Doc) $Doc = 'public/_Documents_/'.$Doc;
		$temp = array (
		  'Famille'     => $ligne->Famille,
		  'Marque'      => $ligne->Marque,
		  'Modele'      => $ligne->Modele,
		  'PN'          => $ligne->PN,
		  'Info'        => $ligne->Info,
		  'ID_User_Modif' => $ligne->ID_User_Modif,
		  'Date_Modif'  => $ligne->Date_Modif,
		  'Image'       => $Image,
		  'Doc'         => $Doc,
		  'Parts_Info'  => $ligne->Parts_Info
		);
		switch ($ligne->Famille_Key) 
		{ 
		  case "ACC": // Accessoire option
			$TA_ACC[] = $temp;
			break;
		  case "CON": // Accessoire option
			$TA_CON[] = $temp;
			break;
		  case "PID": // Accessoire option
			$TA_PID[] = $temp;
			break;
		}
		$i++;
		$old_pn = $ligne->PN;
	  }
	}
	//exit;
	return array('ACC'=>$TA_ACC, 'CON'=>$TA_CON, 'PID'=>$TA_PID);
  }

  public function get_pn_from_liaison(string $fmm_id): array
  {
		$SQL = 'SELECT l.* , p.apn__pn_long , p.apn__desc_short
		FROM liaison_fmm_pn as l
		LEFT JOIN art_pn as p ON (l.id__pn = p.apn__pn)
		WHERE id__fmm = '. $fmm_id .' ORDER BY id__pn';

		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetchAll(PDO::FETCH_OBJ);
		return $data;
  }

  public function get_pn_where_not_liaison(): array
  {
		$SQL = 'SELECT l.id__pn 
		FROM liaison_fmm_pn as l
		WHERE 1 OR 1 ORDER BY id__pn';
		$request = $this->Db->Pdo->query($SQL);
		$array = $request->fetchAll(PDO::FETCH_COLUMN);
		
		$SQL = 'SELECT a.apn__pn as id__pn , a.apn__pn_long , a.apn__desc_short
		FROM apn__pn as a
		LEFT JOIN art_pn as p ON (l.id__pn = p.apn__pn)
		WHERE id__fmm NOT IN '.$array[0].'  ORDER BY id__pn';

		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetchAll(PDO::FETCH_OBJ);
		return $data;
  }

  public function get_line_pn_and_return_liaison_list($id_ligne) : array 
  {
	  	$response = [] ; 
		$SQL = 'SELECT c.cmdl__id__fmm , c.cmdl__pn  
		FROM cmd_ligne as c 
		WHERE cmdl__id = ' . $id_ligne . '';
		$request = $this->Db->Pdo->query($SQL);
		$ligne = $request->fetch(PDO::FETCH_OBJ);


		$SQL = 'SELECT l.* , p.apn__pn_long , p.apn__image
		FROM liaison_fmm_pn as l 
		LEFT JOIN art_pn as p ON p.apn__pn = l.id__pn
		WHERE id__fmm = ' . $ligne->cmdl__id__fmm . ' ';
		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetchAll(PDO::FETCH_OBJ);

		foreach ($data as $value) 
		{
			if (!empty($value->apn__image)) 
			{
				$value->apn__image = base64_encode($value->apn__image);
			}
		}

		array_push($response , $ligne ); 
		array_push($response , $data );
		return $response;
  }

  

  public function get_pn($pn_name)
  {
		$SQL = 'SELECT a.* 
		FROM art_pn as a  
		WHERE apn__pn = "'. $pn_name .'"';
		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetch(PDO::FETCH_OBJ);

		if (!empty($data->apn__image))
			$data->apn__image = base64_encode($data->apn__image);

		return $data;
  }

  public function get_pn_byID($pn_name)
  {
	  	//compare le champs input dénué de caractère spéciaux et en majuscules : 
	  	$pn_court = preg_replace("#[^!A-Za-z0-9%]+#", "", $pn_name);
		$pn_court = strtoupper($pn_court);

		$SQL = 'SELECT a.* , u.prenom , u.nom , k.kw__lib as famille  , l.id__fmm as modele
		FROM art_pn as a  
		LEFT JOIN utilisateur as u on  u.id_utilisateur = apn__id_user_modif
		LEFT JOIN keyword as k ON ( k.kw__type = "famil" AND k.kw__value =  a.apn__famille ) 
		LEFT JOIN liaison_fmm_pn as l ON ( a.apn__pn  = l.id__pn ) 
		WHERE apn__pn = "'. $pn_court .'"';
		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetch(PDO::FETCH_OBJ);


	
		if (!empty($data))
		{
			$request = $this->Db->Pdo->query(
				'SELECT  m.am__marque as marque
				FROM art_fmm as a
				INNER JOIN art_marque as m ON a.afmm__marque = m.am__id
				WHERE a.afmm__id =  "' .$data->modele.'"
				LIMIT 10');
				$marque = $request->fetch(PDO::FETCH_OBJ);
				
				
			$data->marque = $marque ; 


			$SQL = 'SELECT a.afmm__modele
			FROM art_fmm as a  
			WHERE a.afmm__id = "'. $data->modele .'"';
			$request = $this->Db->Pdo->query($SQL);
			$model_data = $request->fetch(PDO::FETCH_OBJ);

			if (!empty($model_data)) 
			{
				$data->modele = $model_data->afmm__modele ; 
			}
		
		}


		if (!empty($data->apn__image))
				$data->apn__image = base64_encode($data->apn__image);
				
		return $data;
  }

  

  public function find_by_liaison(string $pn_id )
  {
		//compare le champs input dénué de caractère spéciaux et en majuscules : 
		$pn_court = preg_replace("#[^!A-Za-z0-9%]+#", "", $pn_id);
		$pn_court = strtoupper($pn_court);

		$SQL = 'SELECT *
		FROM liaison_fmm_pn 
		WHERE id__pn = "' . $pn_court . '"';
		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetchAll(PDO::FETCH_OBJ);
		return $data;
  }


  public function find_one_by_liaison(string $pn_id )
  {
		//compare le champs input dénué de caractère spéciaux et en majuscules : 
		$pn_court = preg_replace("#[^!A-Za-z0-9%]+#", "", $pn_id);
		$pn_court = strtoupper($pn_court);

		$SQL = 'SELECT *
		FROM liaison_fmm_pn 
		WHERE id__pn = "' . $pn_court . '"';
		$request = $this->Db->Pdo->query($SQL);
		$data = $request->fetch(PDO::FETCH_OBJ);
		return $data;
  }


  public function insert_liaison_pn_fmm(array $tableau_modele , string $pn__id) : bool
  {
	 
		$request = 'DELETE FROM liaison_fmm_pn WHERE  id__pn = "' . $pn__id . '" ';
		$update = $this->Db->Pdo->prepare($request);
		$update->execute();
		
		foreach ($tableau_modele as $modele_id) 
		{
			$request = $this->Db->Pdo->prepare("
			INSERT INTO liaison_fmm_pn  (id__fmm,		id__pn) 
			VALUES              (:id__fmm,      :id__pn)");
			$request->bindValue(":id__fmm", $modele_id);
			$request->bindValue(":id__pn",  strtoupper($pn__id));
			$request->execute();
		}
		return true;
  }

  public function insert_pn($pn, $pn_long , $id_user)
  {
		$pn = preg_replace("#[^!A-Za-z0-9%]+#", "", $pn);

		$pn = trim($pn, '"');
		$pn_long = trim($pn_long, '"');

		
		$request = $this->Db->Pdo->prepare("
		INSERT INTO art_pn  (apn__pn,		apn__pn_long,	 	apn__id_user_modif, 	apn__date_modif) 
		VALUES              (:apn__pn,      :apn__pn_long,      :apn__id_user_modif,	:apn__date_modif)"); 
		$request->bindValue(":apn__pn", strtoupper($pn));
		$request->bindValue(":apn__pn_long",  strtoupper($pn_long));
		$request->bindValue(":apn__id_user_modif",  $id_user);
		$request->bindValue(":apn__date_modif",   date("Y-m-d H:i:s"));
		$request->execute();
		$idFmm = $this->Db->Pdo->lastInsertId();
		return $idFmm;
  }

  public function getFAMILLE()
  { /* Liste des famille dans keyword.famil */
	$SQL = 'SELECT kw__value, kw__lib, kw__lib_uk, kw__info
	FROM keyword WHERE kw__type = \'famil\' ORDER BY kw__ordre, kw__lib';
	$request =$this->Db->Pdo->query($SQL);
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	return $data;
  }

  public function get_famille_for_spec()
  {
	$SQL = 'SELECT kw__value, kw__lib, kw__lib_uk, kw__info
	FROM keyword WHERE kw__type = \'famil\' AND (  kw__info != "XX" OR kw__info != "MDL" ) ORDER BY kw__ordre, kw__lib';
	$request =$this->Db->Pdo->query($SQL);
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	return $data;
  }

  public function get_famille_for_spec_pn()
  {
	$SQL = 'SELECT kw__value, kw__lib, kw__lib_uk, kw__info
	FROM keyword WHERE kw__type = \'famil\' AND kw__info != "XX" ORDER BY kw__ordre, kw__lib';
	$request =$this->Db->Pdo->query($SQL);
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	return $data;
  }


  public function getMARQUE()
  { /* Liste des Marques dans  table ART_MARQUE */
	$SQL = 'SELECT am__id, am__marque FROM art_marque WHERE am__actif = 1 ORDER BY am__ordre, am__marque';
	$request =$this->Db->Pdo->query($SQL);
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	return $data;
  }

 /*""b8 88""Yb 888888    db    888888        db    88""Yb 888888            888888 8b    d8 8b    d8 
dP   `" 88__dP 88__     dPYb     88         dPYb   88__dP   88              88__   88b  d88 88b  d88 
Yb      88"Yb  88""    dP__Yb    88        dP__Yb  88"Yb    88              88""   88YbdP88 88YbdP88 
 YboodP 88  Yb 888888 dP""""Yb   88       dP""""Yb 88  Yb   88   oooooooooo 88     88 YY 88 88 YY 8*/ 
public function fmm_create($famille, $marque, $modele, $image, $doc, $descom)
{
	
	/* exemple : INSERT INTO art_fmm (afmm__famille, afmm__marque, afmm__modele) VALUES ('btm', '14', 'dddd') */
	if(!$descom) $descom = NULL; // remplace vide par NULL pour la table SQL
	$request = $this->Db->Pdo->prepare("
	INSERT INTO art_fmm (afmm__famille, afmm__marque, afmm__modele, afmm__image, afmm__doc, afmm__design_com, afmm__dt_creat, afmm__id_user_creat, afmm__dt_modif, afmm__id_user_modif) 
	VALUES              (:famille,      :marque,      :modele,      :image,      :doc,      :descom,          :dtcreat,       :idcreat,            :dtmodif,       :idmodif)"); 
	$request->bindValue(":famille", $famille);
	$request->bindValue(":marque",  $marque);
	$request->bindValue(":modele",  $modele);
	$request->bindValue(":image",   $image);
	$request->bindValue(":doc",     $doc);
	$request->bindValue(":descom",  $descom);
	$request->bindValue(":dtcreat", date("Y-m-d H:i:s"));
	$request->bindValue(":idcreat", $_SESSION['user']->id_utilisateur);
	$request->bindValue(":dtmodif", date("Y-m-d H:i:s"));
	$request->bindValue(":idmodif", $_SESSION['user']->id_utilisateur);
	$request->execute();
	$idFmm = $this->Db->Pdo->lastInsertId();
	return $idFmm;
}

/*8888 8b    d8 8b    d8            88   88 88""Yb 8888b.     db    888888 888888        db    88""Yb 888888            888888 8b    d8 8b    d8 
88__   88b  d88 88b  d88            88   88 88__dP  8I  Yb   dPYb     88   88__         dPYb   88__dP   88              88__   88b  d88 88b  d88 
88""   88YbdP88 88YbdP88            Y8   8P 88"""   8I  dY  dP__Yb    88   88""        dP__Yb  88"Yb    88              88""   88YbdP88 88YbdP88 
88     88 YY 88 88 YY 88 oooooooooo `YbodP' 88     8888Y"  dP""""Yb   88   888888     dP""""Yb 88  Yb   88   oooooooooo 88     88 YY 88 88 YY 8*/ 
public function fmm_update($id_fmm, $famille, $marque, $modele, $image, $doc, $descom)
{
	// Update de tout sauf Image et doc (qui ne sont mises a jour que si il sont present.) (pour eviter d'ecraser l'existant)
	// champs txt
	if(!$descom) $descom = NULL; // remplace vide par NULL pour la table SQL
	$request = $this->Db->Pdo->prepare("UPDATE art_fmm SET 
	afmm__famille=:famille, afmm__marque=:marque, afmm__modele=:modele, afmm__design_com=:descom 
	WHERE (afmm__id=:id_fmm) LIMIT 1");
	$request->bindValue(":id_fmm",  $id_fmm);
	$request->bindValue(":famille", $famille);
	$request->bindValue(":marque",  $marque);
	$request->bindValue(":modele",  $modele);
	$request->bindValue(":descom",  $descom);
	$request->execute();
	// champs img (pour ne mettre l'image que si il y a une nouvelle image)
	if ($image)
	{
		$request = $this->Db->Pdo->prepare("UPDATE art_fmm SET 
		afmm__image=:image 
		WHERE (afmm__id=:id_fmm) LIMIT 1");
		$request->bindValue(":id_fmm",  $id_fmm);
		$request->bindValue(":image",   $image);
		$request->execute();
	}
	// champs doc
	if ($doc)
	{
		$request = $this->Db->Pdo->prepare("UPDATE art_fmm SET 
		afmm__doc=:doc 
		WHERE (afmm__id=:id_fmm) LIMIT 1");
		$request->bindValue(":id_fmm",  $id_fmm);
		$request->bindValue(":doc",   $doc);
		$request->execute();
	}
	return $id_fmm;
}

public function getModels()
{
	$request = $this->Db->Pdo->query(
	'SELECT afmm__id , afmm__modele, k.kw__lib as famille , m.am__marque as Marque
	FROM art_fmm
	INNER JOIN art_marque as m ON afmm__marque = m.am__id
	INNER JOIN keyword as k on afmm__famille = k.kw__value 
	WHERE afmm__actif > 0 
	order by k.kw__ordre ASC, afmm__modele ASC');
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	return $data ; 
}

public function find_models_byFamille($famille_char_3)
{
	$request = $this->Db->Pdo->query(
		'SELECT afmm__id , afmm__modele, k.kw__lib as famille , m.am__marque as Marque
		FROM art_fmm
		INNER JOIN art_marque as m ON afmm__marque = m.am__id
		INNER JOIN keyword as k on afmm__famille = k.kw__value 
		WHERE afmm__actif > 0 AND afmm__famille = "'.$famille_char_3.'"
		order by k.kw__ordre ASC, afmm__modele ASC');
		$data = $request->fetchAll(PDO::FETCH_OBJ);
		return $data ; 
}


public function find_models( string $find_by , string  $value) : array
{
	$request = $this->Db->Pdo->query(
	'SELECT afmm__id , afmm__modele, k.kw__lib as famille , m.am__marque as Marque
	FROM art_fmm
	INNER JOIN art_marque as m ON afmm__marque = m.am__id
	INNER JOIN keyword as k on afmm__famille = k.kw__value 
	WHERE afmm__actif > 0 AND ( '. $find_by .' = "'. $value .'"  ) 
	order by k.kw__ordre ASC, afmm__modele ASC');
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	return $data ; 
	
}

//recupère la désignation commerciale pour les suggestions aux commerciaux lors des devis : 
public function get_article_devis(int  $id_fmm) 
{
	$request = $this->Db->Pdo->query(
	'SELECT a.afmm__id , a.afmm__design_com , a.afmm__image , k.kw__lib , a.afmm__modele , m.am__marque 
	FROM art_fmm as a
	LEFT JOIN art_marque as m ON a.afmm__marque = m.am__id
	LEFT JOIN keyword as k on  ( a.afmm__famille = k.kw__value AND  k.kw__type = "famil" ) 
	WHERE afmm__id = '. $id_fmm.'');
	$data = $request->fetch(PDO::FETCH_OBJ);
	return $data;
  }

//recupère * sur Art_Fmm en foncrtion d'un afmm__id
public function get_fmm_by_id(int $id_fmm)
{	
	if ($id_fmm)
	{ // je fait la lacture si il y a un ID FMM
		$sql     = "SELECT * , k.kw__lib as famille , m.am__marque as marque  FROM art_fmm 
		LEFT JOIN keyword as k ON  ( afmm__famille = k.kw__value AND k.kw__type = 'famil') 
		LEFT JOIN art_marque as m ON ( afmm__marque = m.am__id ) 
		 WHERE afmm__id = ".$id_fmm ;
		$request = $this->Db->Pdo->query($sql);
		$data    = $request->fetch(PDO::FETCH_OBJ);
		if (isset($data->afmm__image))
			$data->afmm__image = base64_encode($data->afmm__image);
		if (isset($data->afmm__doc))
			$data->afmm__doc = 'public/_Documents_/Modele_Doc/'.$data->afmm__doc;
	}
	else
	{ // pas ID_FMM pas de lecture
		$data = FALSE;
	}
	return $data;
}



}

?>
