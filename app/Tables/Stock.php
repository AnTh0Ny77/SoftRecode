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

    foreach ($spec_model as $spec) 
    {
	    foreach ($spec_pn as $spec_p) 
	    {
		   if ($spec_p->aap__cle == $spec->aam__cle and $spec->aam__valeur == $spec_p->aap__valeur) 
		   {
			$update_heritage =
			"UPDATE art_attribut_pn
			SET aap__heritage = 1  
			WHERE aap__pn = ? ";
			$update = $this->Db->Pdo->prepare($update_heritage);
			$update->execute([$pn_id]);
		   }
		   else 
		   {
			$update_heritage =
			"UPDATE art_attribut_pn
			SET aap__heritage = 0 
			WHERE aap__pn = ? ";
			$update = $this->Db->Pdo->prepare($update_heritage);
			$update->execute([$pn_id]);
		   }
	    }

    }
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
        a.* 
        FROM art_attribut_modele as a
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


  public function get_famille_forms($famil) : array 
  {
    $request = $this->Db->Pdo->query('SELECT   
    c.aac__famille , c.aac__cle , c.aac__ordre , c.aac__cle_txt, c.aac__option , c.aac__champ
    FROM art_attribut_cle as c
    WHERE aac__famille = "'. $famil .'"
    ORDER BY c.aac__ordre DESC LIMIT 1500 ');
    $data = $request->fetchAll(PDO::FETCH_OBJ);

    foreach ($data as $clef) 
    {
      $request = $this->Db->Pdo->query('SELECT   
      v.aav__cle , v.aav__valeur, v.aav__ordre , v.aav__valeur_txt
      FROM art_attribut_valeur as v
      WHERE v.aav__cle = "'. $clef->aac__cle .'"
      ORDER BY v.aav__ordre DESC LIMIT 1500 ');
      $responses = $request->fetchAll(PDO::FETCH_OBJ);
      $clef->key_responses = $responses;
    }
    return $data;
  }


  public function get_attribut($pn) : array 
  {
    $request = $this->Db->Pdo->query('SELECT   
    a.aap__pn , a.aap__cle , a.aap__valeur
    FROM art_attribut_pn as a
    LEFT JOIN art_attribut_valeur as v ON a.aap__cle =  v.aav__valeur
    WHERE a.aap__pn = '. $pn  .'
    ORDER BY c.aac__ordre DESC LIMIT 1500 ');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }



 


}