<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Stock extends Table 
{
  public Database $Db;

  //constructeur
  public function __construct($db) 
  {
    $this->Db = $db;
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
        ORDER BY a.aap__pn DESC LIMIT 50 ');

    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function get_specs_pn_show($pn)
  {

    $request = $this->Db->Pdo->query('SELECT DISTINCT   
        a.aap__cle as cle , c.aac__cle_txt as cle_txt , c.aac__cle_txt_result as text_cle
        FROM art_attribut_pn as a
        LEFT JOIN art_attribut_cle as c ON a.aap__cle = c.aac__cle 
        WHERE a.aap__pn = "' . $pn . '"  
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

    return $data;
  }

  public function get_specs_modele_show($pn)
  {

    $request = $this->Db->Pdo->query('SELECT DISTINCT   
        a.aam__cle as cle , c.aac__cle_txt as cle_txt , c.aac__cle_txt_result as text_cle
        FROM art_attribut_modele as a
        LEFT JOIN art_attribut_cle as c ON a.aam__cle = c.aac__cle 
        WHERE a.aam__id_fmm = "' . $pn . '"  
        ORDER BY a.aam__cle DESC LIMIT 150  ');
    $data = $request->fetchAll(PDO::FETCH_OBJ);

    foreach ($data as $key) {
      $request = $this->Db->Pdo->query('SELECT   
        a.aam__valeur as valeur , v.aav__valeur_txt as valeur_txt 
        FROM art_attribut_modele as a
        LEFT JOIN art_attribut_valeur as v ON a.aam__valeur = v.aav__valeur and ( a.aam__cle = v.aav__cle ) 
        WHERE a.aam__id_fmm = "' . $pn . '" AND a.aam__cle = "' . $key->cle . '" 
        ORDER BY a.aam__valeur DESC LIMIT 150  ');
      $key->data = $request->fetchAll(PDO::FETCH_OBJ);
    }

    return $data;
  }


  public function find_models_spec(array $forms_data , array $post_data)
  {
   
	  	$array_where_clause = '';
		$count  = 1 ; 
		
		foreach ($forms_data as $data) 
		{
			foreach ($post_data as $key => $value) 
			{

				if (!empty($value) &&  $key != 'famille') 
				{
					if (is_array($value)) 
					{
						if ($count == 1) 
						{
							$array_where_clause .= '  (';
							$count += 1;
							$iteration = 0 ;
							foreach ($value as $response)
							{
								if ($iteration == 0 ) {
									$array_where_clause .=  ' ( aam__cle = "'.$key.'" AND  aam__valeur =  "'.$response.'" ) ';
									$iteration += 1;
								}
								else{
									$array_where_clause .=  ' OR  ( aam__cle = "'.$key.'" AND aam__valeur =  "'.$response.'" ) ';
									$iteration += 1;
								}
							}
							$array_where_clause .= ' )';
						}
						else 
						{
							$array_where_clause .= ' AND (';
							$count += 1;
							$iteration = 0 ;
							foreach ($value as $response)
							{
								if ($iteration == 0 ){
									$array_where_clause .=  '( aam__cle = "'.$key.'" AND  aam__valeur =  "'.$response.'" )';
									$iteration += 1;
								}
								else {
									$array_where_clause .=  ' OR ( aam__cle = "'.$key.'" AND aam__valeur =  "'.$response.'" ) ';
									$iteration += 1;
								}
							
							}
							$array_where_clause .= ' ) ';
						}
					}
				}
			}

		}

		$request = $this->Db->Pdo->query('SELECT   
		a.* 
		FROM art_attribut_modele as a WHERE 
		' .$array_where_clause. '
		 LIMIT 150 ');
		$data = $request->fetchAll(PDO::FETCH_OBJ);
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


  

  public function get_pn_and_model_list_catalogue($string)
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
      	a.* , u.nom , u.prenom  , k.kw__lib as famille ,  t.afmm__id , t.afmm__marque, m.am__marque 
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
        OR v.aav__valeur_txt LIKE '%" . $mots_filtre[0] . "%'
		)";

      for ($i = 1; $i < $nb_mots_filtre; $i++) {
        $request .=  $operateur . " ( apn__pn LIKE '%".preg_replace("#[^!A-Za-z0-9_%]+#", "", $mots_filtre[$i])."%' 
        OR apn__desc_short LIKE '%".$mots_filtre[$i]."%' 
        OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
        OR u.nom LIKE '%".$mots_filtre[$i]."%'
        OR k.kw__lib LIKE '%".$mots_filtre[$i]. "%'
        OR m.am__marque LIKE '%" . $mots_filtre[$i] . "%'
        OR v.aav__valeur_txt LIKE '%" . $mots_filtre[$i] . "%'
		)";
      }
      $request .= " ORDER BY  apn__date_modif DESC  LIMIT 60";
    } 
	else 
	{
      	$request .= " ORDER BY  apn__date_modif DESC  LIMIT 60";
    }

   
    $send = $this->Db->Pdo->query($request);
    $data = $send->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }



}