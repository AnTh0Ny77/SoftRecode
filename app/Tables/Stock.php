<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Totoro;
use PDO;


class Stock extends Table 
{
  public Database $Db;

  //constructeur
  public function __construct($db) 
  {
    $this->Db = $db;
  }


  //partie rajoutée pour la partie devis 
  public function check_famille_pn($pn)
  {
		$request = $this->Db->Pdo->query('SELECT apn__famille
		FROM art_pn as a
		WHERE  a.apn__pn = "' . $pn . '"');
		$data = $request->fetch(PDO::FETCH_OBJ);

		if ($data->apn__famille =='PID' or $data->apn__famille == 'ACC' ) {
			return $data->apn__famille;
		}	else return false;
  }

  

  public function insert_attr_pn($pn , $aap__cle , $aap__valeur ) : bool
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO art_attribut_pn (aap__pn, aap__cle , aap__valeur )
        VALUES (:aap__pn , :aap__cle , :aap__valeur)');
    $request->bindValue(":aap__pn", $pn);
    $request->bindValue(":aap__cle", $aap__cle);
    $request->bindValue(":aap__valeur", $aap__valeur);
    $request->execute();
    return true;
  }

  public function insert_attr_models($models, $aap__cle, $aap__valeur): bool
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO art_attribut_modele (aam__id_fmm, aam__cle , aam__valeur )
        VALUES (:aam__id_fmm , :aam__cle , :aam__valeur)');
    $request->bindValue(":aam__id_fmm", $models);
    $request->bindValue(":aam__cle", $aap__cle);
    $request->bindValue(":aam__valeur", $aap__valeur);
    $request->execute();
    return true;
  }

  public function delete_specs($pn)
  {
    $request = $this->Db->Pdo->prepare('DELETE FROM  art_attribut_pn WHERE aap__pn = "'. $pn .'"');
    $request->execute();
    return true;
  }

  public function delete_specs_models($models)
  {
    $request = $this->Db->Pdo->prepare('DELETE FROM  art_attribut_modele WHERE aam__id_fmm = "' . $models . '"');
    $request->execute();
    return true;
  }

  public function check_heritage($model_id , $pn_id)
  {
    $spec_model = $this->get_specs_models($model_id);
    $spec_pn = $this->get_specs($pn_id);

		//set 0 all fields : 
		$update_heritage =
		"UPDATE art_attribut_pn
		SET aap__heritage = 0  
		WHERE aap__pn = ? ";
		$update = $this->Db->Pdo->prepare($update_heritage);
		$update->execute([$pn_id]);

    foreach ($spec_model as $spec) 
    {
	    foreach ($spec_pn as $spec_p) 
	    {	   
			$update_heritage =
			"UPDATE art_attribut_pn
			SET aap__heritage = 1  
			WHERE aap__pn = ?  AND ( aap__cle = ? AND aap__valeur = ? ) ";
			$update = $this->Db->Pdo->prepare($update_heritage);
			$update->execute([$pn_id , $spec->aam__cle,  $spec->aam__valeur]);  
	    }

    }
    
  }

  //renvoi un tableau des propriétés non-héritées ou héritées ou un string formatté des propriétés non-héritées ou héritées en fonction des parametres :  
  public function select_empty_heritage(string $pn , bool $return , bool $heritage) 
  {
	    $html_return = '';
		
		if($heritage == false ){
			$and_clause ='AND  ( aap__heritage = 0 ) ';
		} 
		else $and_clause ='AND  ( aap__heritage = 0 OR aap__heritage = 1  )';

		$request = $this->Db->Pdo->query('SELECT DISTINCT   
		a.aap__cle as cle , c.aac__cle_txt as cle_txt , c.aac__cle_txt_result as text_cle
		FROM art_attribut_pn as a
		LEFT JOIN art_attribut_cle as c ON a.aap__cle = c.aac__cle 
		WHERE a.aap__pn = "' . $pn . '" '.$and_clause.' 
		ORDER BY a.aap__cle DESC LIMIT 150  ');
		$data = $request->fetchAll(PDO::FETCH_OBJ);

		foreach ($data as $key) 
		{
			$request = $this->Db->Pdo->query('SELECT   
			a.aap__valeur as valeur , v.aav__valeur_txt as valeur_txt 
			FROM art_attribut_pn as a
			LEFT JOIN art_attribut_valeur as v ON a.aap__valeur = v.aav__valeur and ( a.aap__cle = v.aav__cle ) 
			
			WHERE a.aap__pn = "' . $pn . '" AND a.aap__cle = "'.$key->cle .'" 
			ORDER BY a.aap__pn DESC LIMIT 150  ');
			$key->data = $request->fetchAll(PDO::FETCH_OBJ);
		}

	if ($return == true){
		foreach ($data as  $value) 
		{
			if ( $value->text_cle) 			
				$html_return .= $value->text_cle . ':';
			
			foreach ($value->data as $key => $endpoint ) 
			{
				$html_return .= $endpoint->valeur_txt . '';

				if ($key === array_key_last($value->data)) {
					$html_return .=  '●';
				}
				else{
					$html_return .=  '-';
				}
			}
		}
		return $html_return;
	}
	else return $data;
}


  public function get_specs($id_fmm)
  {
        $request = $this->Db->Pdo->query('SELECT   
        a.* 
        FROM art_attribut_pn as a
        WHERE a.aap__pn = "' . $id_fmm . '"
        ORDER BY a.aap__pn DESC LIMIT 50 ');
        $data = $request->fetchAll(PDO::FETCH_OBJ);

	return $data;
  }
  public function get_spec_model_if_null($model)
  {
      $request = $this->Db->Pdo->query('SELECT   
      a.* 
      FROM art_attribut_modele as a
      WHERE a.aam__id_fmm = "' . $model . '"
      ORDER BY a.aam__id_fmm  DESC LIMIT 50 ');
      $data = $request->fetchAll(PDO::FETCH_OBJ);

      foreach ($data as $row) 
      {
          $row->aap__cle = $row->aam__cle;
          $row->aap__valeur = $row->aam__valeur;
          $row->aap__heritage = 1 ;
      }

      return $data;
  }

  public function get_specs_models($pn)
  {
        $request = $this->Db->Pdo->query('SELECT   
        a.* , v.aav__valeur_txt 
        FROM art_attribut_modele as a
		    LEFT JOIN art_attribut_valeur as v ON  ( v.aav__valeur = a.aam__valeur AND  v.aav__cle = a.aam__cle )
        WHERE a.aam__id_fmm = "' . $pn . '"
        ORDER BY a.aam__id_fmm  DESC LIMIT 50 ');
        $data = $request->fetchAll(PDO::FETCH_OBJ);

        foreach ($data as $row) 
        {
            $row->aap__cle = $row->aam__cle;
            $row->aap__valeur = $row->aam__valeur;
        }
        return $data;
  }

  public function heritage($id_fmm)
  {
      //recupère une liste des pn à hériter: 
      $SQL = 'SELECT *
		FROM liaison_fmm_pn WHERE id__fmm = ' . $id_fmm . ' ORDER BY id__pn';
	$request = $this->Db->Pdo->query($SQL);
      $pn_list = $request->fetchAll(PDO::FETCH_OBJ);

      //recupère la liste de propriété de l'id_fmm
      $request = $this->Db->Pdo->query('SELECT   
		a.* 
		FROM art_attribut_modele as a
		WHERE a.aam__id_fmm = "' . $id_fmm . '"
		ORDER BY a.aam__id_fmm  DESC LIMIT 50 ');
      $modele_spec_list = $request->fetchAll(PDO::FETCH_OBJ);

	
	//remet à zero tous les champs obligatoires dans la table pn 
	foreach ($pn_list as $pn) 
	{
		$update = $this->Db->Pdo->prepare(
			'UPDATE art_attribut_pn
			SET aap__heritage = 0 
			WHERE  	aap__pn = ?'
		);
		$update->execute([$pn->id__pn]);

		//efface la liste des clefs valeurs similaires à  la nouvelle liste de propriétés : 
		foreach ($modele_spec_list as $spec) 
		{
			
			$request = "DELETE FROM art_attribut_pn WHERE  aap__pn = '" . $pn->id__pn . "' 
			AND ( aap__cle = '". $spec->aam__cle. "' AND  aap__valeur  = '". $spec->aam__valeur ."')";
			$update = $this->Db->Pdo->prepare($request);
			$update->execute();
		}

		//insert les nouvelles propriétés obligatoires et met a jour le champs : 
		foreach ($modele_spec_list as $spec) 
		{
			$request = $this->Db->Pdo->prepare('INSERT INTO art_attribut_pn (aap__pn, aap__cle , aap__valeur , aap__heritage)
			VALUES (:aap__pn , :aap__cle , :aap__valeur , :aap__heritage)');
			$request->bindValue(":aap__pn", $pn->id__pn);
			$request->bindValue(":aap__cle",  $spec->aam__cle);
			$request->bindValue(":aap__valeur", $spec->aam__valeur);
			$request->bindValue(":aap__heritage", 1 );
			$request->execute();
		
		}
		return true;
	}

  }

  public function get_specs_value($pn)
  {
    $request = $this->Db->Pdo->query('SELECT   
        a.* , v.aav__valeur_txt , c.aac__cle_txt
        FROM art_attribut_pn as a
        LEFT JOIN art_attribut_valeur as v ON a.aap__valeur = v.aav__valeur and ( a.aap__cle = v.aav__cle ) 
        LEFT JOIN art_attribut_cle as c ON a.aap__cle = c.aac__cle 
        WHERE a.aap__pn = "' . $pn . '"
        ORDER BY c.aac__ordre ASC, v.aav__ordre ASC LIMIT 50 ');

    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function get_specs_pn_show($pn)
  {
		$request = $this->Db->Pdo->query('SELECT  DISTINCT 
			a.aap__cle as cle , c.aac__cle_txt as cle_txt , c.aac__cle_txt_result as text_cle
			FROM art_attribut_pn as a
			LEFT JOIN art_attribut_cle as c ON a.aap__cle = c.aac__cle 
			WHERE a.aap__pn = "' . $pn . ' "
			GROUP BY a.aap__cle   
			ORDER BY c.aac__ordre ASC LIMIT 50  
			');
			$data = $request->fetchAll(PDO::FETCH_OBJ);
			
		foreach ($data as $key) 
		{
			$request = $this->Db->Pdo->query('SELECT   
			a.aap__valeur as valeur , v.aav__valeur_txt as valeur_txt 
			FROM art_attribut_pn as a
			LEFT JOIN art_attribut_valeur as v ON a.aap__valeur = v.aav__valeur and a.aap__cle = v.aav__cle  
			WHERE a.aap__pn = "' . $pn . '" AND a.aap__cle = "'.$key->cle .'" 
			ORDER BY v.aav__ordre  LIMIT 150  ');
			$key->data = $request->fetchAll(PDO::FETCH_OBJ);
		}
		
		return $data;
  }

  public function get_specs_modele_show($pn)
  {

    $request = $this->Db->Pdo->query('SELECT DISTINCT   
        a.aam__cle as cle , c.aac__cle_txt as cle_txt , c.aac__cle_txt_result as text_cle
        FROM art_attribut_modele as a
        LEFT JOIN art_attribut_cle as c ON a.aam__cle = c.aac__cle 
        WHERE a.aam__id_fmm = "' . $pn . '" 
		GROUP BY a.aam__cle
        ORDER BY c.aac__ordre ASC LIMIT 150  ');
    $data = $request->fetchAll(PDO::FETCH_OBJ);

    foreach ($data as $key) {
      $request = $this->Db->Pdo->query('SELECT   
        a.aam__valeur as valeur , v.aav__valeur_txt as valeur_txt 
        FROM art_attribut_modele as a
        LEFT JOIN art_attribut_valeur as v ON a.aam__valeur = v.aav__valeur and ( a.aam__cle = v.aav__cle ) 
        WHERE a.aam__id_fmm = "' . $pn . '" AND a.aam__cle = "' . $key->cle . '" 
        ORDER BY v.aav__ordre ASC LIMIT 150  ');
      $key->data = $request->fetchAll(PDO::FETCH_OBJ);
    }

    return $data;
  }

  public function return_forms(array $forms_data){
	
	$array = [];
	foreach ($forms_data as $key => $value) 
	{
		if (!empty($value) &&  $key != 'famille' &&  $key != 'recherche_guide') 
				{
					$on_clause = '( ';
					

					foreach ($value as $cle => $step) { 
						if ($cle === array_key_first($value)) 
						{
							$on_clause .= ' v.aav__valeur = "'.$step.'"';
						}
						else 
						{
							$on_clause .= '  OR v.aav__valeur = "'.$step.'"';
						}
						
					}
					$on_clause .= ' )';
					
					

					$request = $this->Db->Pdo->query('SELECT   
					c.aac__cle_txt , v.aav__valeur_txt 
					FROM art_attribut_cle as c
					LEFT JOIN art_attribut_valeur as v ON  ( v.aav__cle = c.aac__cle AND '.$on_clause.' )
					WHERE c.aac__cle =  "'.$key.'"  
					ORDER BY c.aac__ordre ASC LIMIT 150  '); 
					$data = $request->fetchAll(PDO::FETCH_OBJ);

					if (!empty($data)) 
					{
						$key_name = '';
						foreach($data as $clefs => $valeur){
							
							if ($clefs === array_key_first($data)) {
								$key_name = $valeur->aac__cle_txt ;
								$array[$key_name] = [];
								array_push($array[$key_name], $valeur->aav__valeur_txt );
							}
							else array_push($array[$key_name], $valeur->aav__valeur_txt );
			
						};
					}	
					
			
				}
	}

	
	return $array;
	
  }


  public function count_from_totoro(Totoro $totoro , $article){
	 	 $sql= $totoro->Pdo->query('SELECT id_etat, count(id_etat) AS ct_etat
		FROM locator
		WHERE out_datetime IS NULL
		AND article =  "'. $article.'"
		GROUP BY id_etat');
		$data = $sql->fetchAll(PDO::FETCH_OBJ);
		
		return $data;
  	}

  public function find_models_spec(array $forms_data , array $post_data)
  {
   
		$count  = 1 ; 
		$where = '';
		foreach ($post_data as $key => $value) {
			if (!empty($value) &&  $key != 'famille' &&  $key != 'recherche_guide') {
				if ($count == 1) {
					$count += 1;
					$specs = '';
					foreach ($value as $keys => $spec) {
						if ($keys === array_key_last($value)) {
							$specs .=  ' "' . $spec . '" ';
						} else $specs .=  ' "' .  $spec . '", ';
					}
					$where .= 'WHERE afmm__id in ( SELECT aam__id_fmm FROM art_attribut_modele WHERE aam__cle = "' . $key . '" AND aam__valeur IN (' . $specs . ') )';
				} else {
					$count += 1;
					$specs = '';
					foreach ($value as $keys => $spec) {
						if ($keys === array_key_last($value)) {
							$specs .=  ' "' . $spec . '" ';
						} else $specs .=  ' "' .  $spec . '", ';
					}
					$where .= 'AND afmm__id in ( SELECT aam__id_fmm FROM art_attribut_modele WHERE aam__cle = "' . $key . '" AND aam__valeur IN ( ' . $specs . ') )';
				}
			}
		}
	
		
		$request = $this->Db->Pdo->query('SELECT   
		a.* 
		FROM art_attribut_modele as a WHERE 
		' . $where. '
		 LIMIT 150 ');
		$data = $request->fetchAll(PDO::FETCH_OBJ);
		return $data;

  }


  public function find_pn_spec( array $post_data)
  {
   
		$count  = 1 ; 
		$where = '';
		foreach ($post_data as $key => $value) 
		{
			if (!empty($value) &&  $key != 'famille' &&  $key != 'recherche_guide') {
				if ($count == 1) {
					$count += 1;
					$specs = '';
					foreach ($value as $keys => $spec) {
						if ($keys === array_key_last($value)) {
							$specs .=  ' "'. $spec . '" ';
						}
						else $specs .=  ' "' .  $spec . '", ';
						
					}
					$where .= 'WHERE apn__pn in ( SELECT aap__pn FROM art_attribut_pn WHERE aap__cle = "' . $key . '" AND aap__valeur IN ('. $specs .') )';
				} else {
					$count += 1;
					$specs = '';
					foreach ($value as $keys => $spec) {
						if ($keys === array_key_last($value)) {
							$specs .=  ' "' . $spec . '" ';
						} else $specs .=  ' "' .  $spec . '", ';
					}
					$where .= 'AND apn__pn in ( SELECT aap__pn FROM art_attribut_pn WHERE aap__cle = "' . $key . '" AND aap__valeur IN ( ' . $specs . ') )';
				}
			}
		}

		
		$sql = $this->Db->Pdo->query('SELECT
			art_pn.*,
			u.prenom,
			u.nom,
			k.kw__lib AS famille
		FROM
			art_pn
		LEFT JOIN utilisateur AS u ON u.id_utilisateur = apn__id_user_modif
		LEFT JOIN keyword AS k ON k.kw__type = "famil" AND k.kw__value = apn__famille
					'. $where.'
		ORDER BY
			apn__date_modif
		LIMIT 50
		');
		
		
		$data = $sql->fetchAll(PDO::FETCH_OBJ);

		foreach ($data as $pn) 
		{
			$SQL = 'SELECT  id__fmm 
			FROM liaison_fmm_pn 
			WHERE id__pn = "'.$pn->apn__pn.'"
			LIMIT 5';
			$request = $this->Db->Pdo->query($SQL);
			$liaison = $request->fetchAll(PDO::FETCH_OBJ);

			if(count($liaison) > 1 )
				$pn->modele = $liaison ; $pn->count_relation =  intval(count($liaison));
			if(count($liaison) == 1 )
				$pn->modele = $liaison[0]->id__fmm ; $pn->count_relation =  intval(count($liaison));
			if(count($liaison) == 0 )
				$pn->modele = null ; $pn->count_relation =  intval(count($liaison));

			if ($pn->count_relation > 1 ) {

				$list_models = '';
					foreach ($pn->modele as $keys => $spec) {
						if ($keys === array_key_last($pn->modele)) {
							$list_models .=  ' "'. $spec->id__fmm . '" ';
						}
						else $list_models .=  ' "' .  $spec->id__fmm . '", ';
						
					}
				$SQL = 'SELECT a.afmm__modele ,  a.afmm__id , m.am__marque as marque
				FROM art_fmm as a  
				LEFT JOIN art_marque as m on ( m.am__id = a.afmm__marque ) 
				WHERE a.afmm__id IN  (' . $list_models . ')';
			}
			else {
				$SQL = 'SELECT a.afmm__modele , a.afmm__id ,  m.am__marque as marque
				FROM art_fmm as a  
				LEFT JOIN art_marque as m on ( m.am__id = a.afmm__marque ) 
				WHERE a.afmm__id = "' . $pn->modele . '"
				';
			}
			
			$request = $this->Db->Pdo->query($SQL);
			$model_data = $request->fetchAll(PDO::FETCH_OBJ);
			
			if (!empty($model_data) and count($model_data) == 1)
			{
			
				$pn->modele = $model_data[0]->afmm__modele; 
				$pn->marque = $model_data[0]->marque;
			}elseif(!empty($model_data) and count($model_data) > 1){
				
				$pn->relations = $model_data;
			}
				
		}
		return $data;
  }


  public function get_famille_forms($famil) : array 
  {
    $request = $this->Db->Pdo->query('SELECT   
    c.aac__famille , c.aac__cle , c.aac__ordre , c.aac__cle_txt, c.aac__option , c.aac__champ
    FROM art_attribut_cle as c
    WHERE aac__famille = "'. $famil .'"
    ORDER BY c.aac__ordre ASC LIMIT 1500 ');
    $data = $request->fetchAll(PDO::FETCH_OBJ);

    foreach ($data as $clef) 
    {
      $request = $this->Db->Pdo->query('SELECT   
      v.aav__cle , v.aav__valeur, v.aav__ordre , v.aav__valeur_txt
      FROM art_attribut_valeur as v
      WHERE v.aav__cle = "'. $clef->aac__cle .'"
      ORDER BY v.aav__ordre ASC LIMIT 1500 ');
      $responses = $request->fetchAll(PDO::FETCH_OBJ);
      $clef->key_responses = $responses;
    }
    return $data;
  }


  public function get_pn_list($string)
  {
	//traitement du champs recheche : 
    $filtre = str_replace("-", ' ', $string);
    $filtre = str_replace("'", ' ', $filtre);
	//nombre de mots pour itérations : 
    $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
    $mots_filtre = str_word_count($filtre, 1, '0123456789');
	$mode_filtre = false;

	if ($nb_mots_filtre > 0) 
		$mode_filtre = true;

    $operateur = ' AND ';
    $request = "SELECT DISTINCT 
      	a.* , u.nom , u.prenom  , k.kw__lib as famille  
		FROM art_pn as a
		LEFT JOIN utilisateur as u on  u.id_utilisateur = apn__id_user_modif
		LEFT JOIN keyword as k ON ( k.kw__type = 'famil' AND k.kw__value =  a.apn__famille ) 
		LEFT JOIN liaison_fmm_pn as l ON ( a.apn__pn  = l.id__pn )
		LEFT JOIN art_fmm as t ON ( l.id__fmm = t.afmm__id ) 
		LEFT JOIN art_marque as m ON ( t.afmm__marque = m.am__id ) 
		LEFT JOIN art_attribut_pn as s ON ( a.apn__pn = s.aap__pn )
		LEFT JOIN art_attribut_cle as c ON ( s.aap__cle = c.aac__cle ) 
		LEFT JOIN art_attribut_valeur as v ON( s.aap__valeur = v.aav__valeur AND s.aap__cle = v.aav__cle )";
      
    if ($mode_filtre) {
      $request .=  "WHERE ( apn__pn LIKE '%". preg_replace("#[^!A-Za-z0-9_%]+#", "", $mots_filtre[0])."%' 
        OR apn__desc_short LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE  '%".$mots_filtre[0]."%' 
        OR u.nom LIKE  '%".$mots_filtre[0]."%' 
        OR k.kw__lib LIKE '%".$mots_filtre[0]. "%' 
      	OR m.am__marque LIKE '%" . $mots_filtre[0] . "%' 
		OR t.afmm__modele LIKE '%" . $mots_filtre[0] . "%'
        OR v.aav__valeur_txt LIKE '%" . $mots_filtre[0] . "%'
		)";

      for ($i = 1; $i < $nb_mots_filtre; $i++) {
        $request .=  $operateur . " ( apn__pn LIKE '%".preg_replace("#[^!A-Za-z0-9_%]+#", "", $mots_filtre[$i])."%' 
        OR apn__desc_short LIKE '%".$mots_filtre[$i]."%' 
        OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
        OR u.nom LIKE '%".$mots_filtre[$i]."%'
        OR k.kw__lib LIKE '%".$mots_filtre[$i]. "%'
        OR m.am__marque LIKE '%" . $mots_filtre[$i] . "%'
		OR t.afmm__modele LIKE '%" . $mots_filtre[$i] . "%'
        OR v.aav__valeur_txt LIKE '%" . $mots_filtre[$i] . "%'
		)";
    }
    $request .= " ORDER BY  apn__pn   LIMIT 25";
    } 
	else
	if ($nb_mots_filtre > 0) {

	} else 
	 $request .= " ORDER BY  apn__date_modif DESC  LIMIT 25";

	
    $send = $this->Db->Pdo->query($request);
    $data = $send->fetchAll(PDO::FETCH_OBJ);

	//pour chaque pn trouvé
    foreach ($data as $pn) 
		{
			//recup les id fmm depuis la liaison
			$SQL = 'SELECT  id__fmm 
			FROM liaison_fmm_pn 
			WHERE id__pn = "'.$pn->apn__pn.'"
			LIMIT 5';
			$request = $this->Db->Pdo->query($SQL);
			$liaison = $request->fetchAll(PDO::FETCH_OBJ);

			
			//compte le nombre de liaison :
			if(count($liaison) > 1 )
				$pn->modele = $liaison ; $pn->count_relation =  intval(count($liaison));
			if(count($liaison) == 1 )
				$pn->modele = $liaison[0]->id__fmm ; $pn->count_relation =  intval(count($liaison));
			if(count($liaison) == 0 )
				$pn->modele = null ; $pn->count_relation =  intval(count($liaison));

			//si plusieurs modèle sont présents : 
			if ($pn->count_relation > 1 ) {
				//crée un string depuis le tableau 
				$list_models = '';
					foreach ($pn->modele as $keys => $spec) {
						if ($keys === array_key_last($pn->modele)) {
							$list_models .=  ' "'. $spec->id__fmm . '" ';
						}
						else $list_models .=  ' "' .  $spec->id__fmm . '", ';	
					}
				//recupère les données des modèles depuis art__fmm : IN si plusieurs = si 1 seul 
				$SQL = 'SELECT a.afmm__modele ,  a.afmm__id , m.am__marque as marque
				FROM art_fmm as a  
				LEFT JOIN art_marque as m on ( m.am__id = a.afmm__marque ) 
				WHERE a.afmm__id IN  (' . $list_models . ')';
			}
			else {
				$SQL = 'SELECT a.afmm__modele ,  a.afmm__id , m.am__marque as marque
				FROM art_fmm as a  
				LEFT JOIN art_marque as m on ( m.am__id = a.afmm__marque ) 
				WHERE a.afmm__id = "' . $pn->modele . '"
				';
			}
			
			$request = $this->Db->Pdo->query($SQL);
			$model_data = $request->fetchAll(PDO::FETCH_OBJ);
			
			if (!empty($model_data) and count($model_data) == 1)
			{
				
				$pn->modele = $model_data[0]->afmm__modele; 
				$pn->marque = $model_data[0]->marque;
			}elseif(!empty($model_data) and count($model_data) > 1){
				
				$pn->relations = $model_data;
			}
				
		}
    return $data;
  }


  public function find_pn_list($string)
  {
	//traitement du champs recheche : 
    $filtre = str_replace("-", ' ', $string);
    $filtre = str_replace("'", ' ', $filtre);
	//nombre de mots pour itérations : 
    $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
    $mots_filtre = str_word_count($filtre, 1, '0123456789');
	$mode_filtre = false;

	if ($nb_mots_filtre > 0) 
		$mode_filtre = true;

    $operateur = ' AND ';
    $request = "SELECT  a.apn__pn  FROM art_pn as a ";
      
    if ($mode_filtre){
      	$request .=  "WHERE ( a.apn__pn LIKE '%". preg_replace("#[^!A-Za-z0-9_%]+#", "", $mots_filtre[0])."%' )";

		for ($i = 1; $i < $nb_mots_filtre; $i++) {
			$request .=  $operateur . " ( a.apn__pn LIKE '%".preg_replace("#[^!A-Za-z0-9_%]+#", "", $mots_filtre[$i])."%' )";
		}
    	$request .= " ORDER BY  apn__pn   LIMIT 25";
		
		$send = $this->Db->Pdo->query($request);
		$data = $send->fetchAll(PDO::FETCH_OBJ);
		return $data;
    } 
	else return [];
  }


	public function get_model_list($string)
	{

		$filtre = str_replace("-", ' ', $string);
		$filtre = str_replace("'", ' ', $filtre);
		$nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
		$mots_filtre = str_word_count($filtre, 1, '0123456789');
		$mode_filtre = false;

		if ($nb_mots_filtre > 0)
		$mode_filtre = true;

		$operateur = ' AND ';
		$request = "SELECT DISTINCT 
      	a.* , u.nom , u.prenom  , k.kw__lib as famille ,   m.am__marque 
		FROM art_fmm as a
			LEFT JOIN utilisateur as u on  u.id_utilisateur = afmm__id_user_creat 
			LEFT JOIN keyword as k ON ( k.kw__type = 'famil' AND k.kw__value =  a.afmm__famille ) 
			LEFT JOIN liaison_fmm_pn as l ON ( a.afmm__id  = l.id__fmm )
			LEFT JOIN art_marque as m ON ( a.afmm__marque = m.am__id ) 
			LEFT JOIN art_attribut_modele as s ON ( a.afmm__id = s.aam__id_fmm )
			LEFT JOIN art_attribut_cle as c ON ( s.aam__cle = c.aac__cle ) 
			LEFT JOIN art_attribut_valeur as v ON( s.aam__valeur = v.aav__valeur AND s.aam__cle = v.aav__cle )";

		if ($mode_filtre) {
			$request .=  "WHERE ( afmm__modele LIKE '%" . preg_replace("#[^!A-Za-z0-9_%]+#", "", $mots_filtre[0]) . "%' 
			OR u.prenom LIKE  '%" . $mots_filtre[0] . "%' 
			OR u.nom LIKE  '%" . $mots_filtre[0] . "%' 
			OR k.kw__lib LIKE '%" . $mots_filtre[0] . "%' 
			OR m.am__marque LIKE '%" . $mots_filtre[0] . "%' 
			OR v.aav__valeur_txt LIKE '%" . $mots_filtre[0] . "%'
			)";

			for ($i = 1; $i < $nb_mots_filtre; $i++) {
				$request .=  $operateur . " ( afmm__modele LIKE '%" . preg_replace("#[^!A-Za-z0-9_%]+#", "", $mots_filtre[$i]) . "%' 
				OR u.prenom LIKE '%" . $mots_filtre[$i] . "%' 
				OR u.nom LIKE '%" . $mots_filtre[$i] . "%'
				OR k.kw__lib LIKE '%" . $mots_filtre[$i] . "%'
				OR m.am__marque LIKE '%" . $mots_filtre[$i] . "%'
				OR v.aav__valeur_txt LIKE '%" . $mots_filtre[$i] . "%'
		)";
			}
			$request .= " ORDER BY  afmm__modele ASC  LIMIT 25";
		} else {
			$request .= " ORDER BY  afmm__dt_modif  DESC  LIMIT 25";
		}

		$send = $this->Db->Pdo->query($request);
		$data = $send->fetchAll(PDO::FETCH_OBJ);


	
		foreach ($data as $model) {
			$SQL = 'SELECT  id__pn 
			FROM liaison_fmm_pn 
			WHERE id__fmm = "' . $model->afmm__id . '"
			LIMIT 5';
			$request = $this->Db->Pdo->query($SQL);
			$liaison = $request->fetchAll(PDO::FETCH_OBJ);

			
			if (count($liaison) > 1)
			$model->pn = $liaison;
				$model->count_relation =  intval(count($liaison));
			if (count($liaison) == 1)
			$model->pn = $liaison[0]->id__pn;
				$model->count_relation =  intval(count($liaison));
			if (count($liaison) == 0)
			$model->pn = null;
				$model->count_relation =  intval(count($liaison));


			if ($model->count_relation > 1 )
			 {

					$list_pn = '';

						foreach ($model->pn as $keys => $spec) {
							if ($keys === array_key_last($model->pn)) {
								$list_pn .=  ' "'. $spec->id__pn . '" ';
							}
							else $list_pn .=  ' "' .  $spec->id__pn . '", ';
							
						}
						
					$SQL = 'SELECT a.apn__pn_long , a.apn__pn , a.apn__famille  
					FROM art_pn as a  
					WHERE a.apn__pn IN  (' . $list_pn . ')';
				}
			else {
				$SQL = 'SELECT  a.apn__pn_long , a.apn__pn , a.apn__famille  
				FROM art_pn as a   
				WHERE a.apn__pn = "' . $model->pn . '"
				';
			}
				
				$request = $this->Db->Pdo->query($SQL);
				$model_data = $request->fetchAll(PDO::FETCH_OBJ);
				
				if (!empty($model_data) and count($model_data) == 1)
				{
					$model->pn = $model_data[0]->apn__pn; 

				}elseif(!empty($model_data) and count($model_data) > 1){
					
					$model->relations = $model_data;
				}
		}

		return $data;
	}


	public function find_model_spec(array $post_data)
	{

		$count  = 1 ; 
		$where = '';
		foreach ($post_data as $key => $value) 
		{
			if (!empty($value) &&  $key != 'famille' &&  $key != 'recherche_guide') {
				if ($count == 1) {
					$count += 1;
					$specs = '';
					foreach ($value as $keys => $spec) {
						if ($keys === array_key_last($value)) {
							$specs .=  ' "'. $spec . '" ';
						}
						else $specs .=  ' "' .  $spec . '", ';
						
					}
					$where .= 'WHERE afmm__id in ( SELECT aam__id_fmm FROM art_attribut_modele WHERE aam__cle = "' . $key . '" AND aam__valeur IN ('. $specs .') )';
				} else {
					$count += 1;
					$specs = '';
					foreach ($value as $keys => $spec) {
						if ($keys === array_key_last($value)) {
							$specs .=  ' "' . $spec . '" ';
						} else $specs .=  ' "' .  $spec . '", ';
					}
					$where .= 'AND afmm__id  in ( SELECT aam__id_fmm FROM art_attribut_modele WHERE aam__cle = "' . $key . '" AND aam__valeur IN ( ' . $specs . ') )';
				}
			}
		}


		$request = $this->Db->Pdo->query('SELECT  
		a.* , u.prenom , u.nom , k.kw__lib as famille , m.am__marque
		FROM art_fmm as a 
		LEFT JOIN utilisateur as u on  u.id_utilisateur = a.afmm__id_user_creat
     	LEFT JOIN keyword as k ON  k.kw__type = "famil" AND k.kw__value =  a.afmm__famille 
		LEFT JOIN art_marque as m on ( m.am__id = a.afmm__marque ) 
    	' . $where . '
		ORDER BY a.afmm__dt_modif LIMIT 50 ');

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

				if ($model->count_relation > 1 )
			 {

					$list_pn = '';

						foreach ($model->pn as $keys => $spec) {
							if ($keys === array_key_last($model->pn)) {
								$list_pn .=  ' "'. $spec->id__pn . '" ';
							}
							else $list_pn .=  ' "' .  $spec->id__pn . '", ';
							
						}
						
					$SQL = 'SELECT a.apn__pn_long , a.apn__pn , a.apn__famille  
					FROM art_pn as a  
					WHERE a.apn__pn IN  (' . $list_pn . ')';
				}
			else {
				$SQL = 'SELECT  a.apn__pn_long , a.apn__pn , a.apn__famille  
				FROM art_pn as a   
				WHERE a.apn__pn = "' . $model->pn . '"
				';
			}
				
				$request = $this->Db->Pdo->query($SQL);
				$model_data = $request->fetchAll(PDO::FETCH_OBJ);
				
			
				if (!empty($model_data) and count($model_data) == 1)
				{
					$model->pn = $model_data[0]->apn__pn; 

				}elseif(!empty($model_data) and count($model_data) > 1){
					
					$model->relations = $model_data;
				}

		
		
		}

		return $data;
	}
}