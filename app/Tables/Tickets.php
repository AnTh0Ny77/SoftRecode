<?php


namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Tables\General;
use DateTime;
use PDO;
use stdClass;

class Tickets extends Table {
  
  public Database $Db;

  public function __construct($db){
    $this->Db = $db;
  }

  public function get_scenario(){
        $request = $this->Db->Pdo->query('SELECT  *  FROM ticket_scenario WHERE 1 = 1  ORDER BY tks__ordre  LIMIT 50000');
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
  }


  public function get_last(){
	$results  = [];
	$request = $this->Db->Pdo->query('SELECT  t.*  FROM ticket as t
	WHERE 1 = 1  ORDER BY tk__id DESC LIMIT 30');
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	foreach ($data as $ticket) {
		$ticket = $this->findOne($ticket->tk__id);
		array_push($results , $ticket);
	}
	return $results;
}

  public function get_subject_table($column_name){
	  $request = $this->Db->Pdo->query('SELECT COLUMN_NAME  , TABLE_NAME 
	  FROM INFORMATION_SCHEMA.COLUMNS 
	  WHERE COLUMN_NAME =  "'.$column_name.'" ');
	  $data = $request->fetch(PDO::FETCH_ASSOC);
	  return $data;
  }


  public function findOne($id){
	    $tmoti = "tmoti";
		$request = $this->Db->Pdo->query('SELECT  t.* , k.kw__lib 
		FROM ticket as t
		LEFT JOIN keyword as k ON ( k.kw__value = t.tk__motif AND  k.kw__type= "tmoti") 
		WHERE t.tk__id = "'.$id.'" ');
		$ticket = $request->fetch(PDO::FETCH_OBJ);
		$request = $this->Db->Pdo->query('SELECT  l.*  , u.nom , u.prenom , z.nom as nom_dest , z.prenom as prenom_dest   
		FROM ticket_ligne as l
		LEFT JOIN utilisateur AS u ON ( u.id_utilisateur = l.tkl__user_id ) 
		LEFT JOIN utilisateur AS z ON ( z.id_utilisateur = l.tkl__user_id_dest ) 
		WHERE l.tkl__tk_id = "'.$id.'" ');
		$lignes = $request->fetchAll(PDO::FETCH_OBJ);
		$ticket->lignes = $lignes;
		$ticket->last_line = $this->get_last_line($id);
		foreach ($ticket->lignes as $ligne){
			$date_time = new DateTime($ligne->tkl__dt);
			//formate la date pour l'utilisateur:
			$ligne->tkl__dt = $date_time->format('d/m/Y H:i');
			$request = $this->Db->Pdo->query('SELECT  c.* , t.tksc__label
			FROM ticket_ligne_champ as c
			LEFT JOIN ticket_senar_champ as t ON ( c.tklc__nom_champ = t.tksc__nom_champ )
			WHERE c.tklc__id = "'.$ligne->tkl__id.'" ');
			$fields = $request->fetchAll(PDO::FETCH_OBJ);
			$ligne->fields = $fields;
			$request = $this->Db->Pdo->query('SELECT  s.*  FROM ticket_senar_champ as s
			WHERE s.tksc__motif_ligne = "' . $ligne->tkl__motif_ligne . '" ');
			$scenario = $request->fetchAll(PDO::FETCH_OBJ);
			foreach ($scenario as $step) {
				if (intval($step->tksc__sujet) == 1 ){
					$ticket->sujet = $step;
				}
			}

		}
		return $ticket;
  }

  public function get_last_line($id){
		$request = $this->Db->Pdo->query('SELECT  MAX(tkl__dt) as dateLigne  FROM ticket_ligne 
		WHERE tkl__tk_id = "' . $id . '" ');
		$ligne = $request->fetch(PDO::FETCH_OBJ);
		$request = $this->Db->Pdo->query('SELECT * , u.nom , u.prenom , z.nom as nom_dest , z.prenom as prenom_dest 
		FROM ticket_ligne 
		LEFT JOIN utilisateur AS u ON ( u.id_utilisateur = tkl__user_id ) 
		LEFT JOIN utilisateur AS z ON ( z.id_utilisateur = tkl__user_id_dest ) 
		WHERE tkl__dt = "' . $ligne->dateLigne . '" ');
		$ligne = $request->fetch(PDO::FETCH_OBJ);
		if (!empty($ligne)) {
			$date_time = new DateTime($ligne->tkl__dt);
			$ligne->tkl__dt = $date_time->format('d/m/Y H:i');
		}
		
		return $ligne;
  }

  public function getCurrentUser( int $ticket_id){
		$request = $this->Db->Pdo->query('SELECT  MAX(tkl__dt) as dateLigne  FROM ticket_ligne 
		WHERE tkl__tk_id = "'.$ticket_id.'" ');
		$ligne = $request->fetch(PDO::FETCH_OBJ);
		$request = $this->Db->Pdo->query('SELECT tkl__user_id_dest , u.nom , u.prenom  FROM ticket_ligne 
		LEFT JOIN utilisateur as u ON ( tkl__user_id_dest = u.id_utilisateur) 
		WHERE tkl__dt = "'.$ligne->dateLigne.'" ');
		$ligne = $request->fetch(PDO::FETCH_OBJ);
		return $ligne;
  }

  public function getNextAction($ticket_id){
		$request = $this->Db->Pdo->query('SELECT  MAX(tkl__dt) as dateLigne  FROM ticket_ligne 
		WHERE tkl__tk_id = "' . $ticket_id . '" ');
		$ligne = $request->fetch(PDO::FETCH_OBJ);
		$request = $this->Db->Pdo->query('SELECT  tkl__motif_ligne   FROM ticket_ligne 
		WHERE tkl__dt = "' . $ligne->dateLigne . '" ');
		$ligne = $request->fetch(PDO::FETCH_OBJ);
		$request = $this->Db->Pdo->query('SELECT  * FROM ticket_scenario
		WHERE tks__motif_ligne_preced = "' . $ligne->tkl__motif_ligne . '" ');
		$scenario = $request->fetchAll(PDO::FETCH_OBJ);
		return $scenario;
  }


  public function createEntities(object $entitie, string $identifier ,  $id){
		$pattern = "@";
		if (stripos($identifier, $pattern)){
			$request = explode('@', $identifier);
			$subject_table = $this->get_subject_table($request[1]);
			if (!empty($subject_table) and $entitie->identifier  == $request[0] ){
				$request = $this->Db->Pdo->query('SELECT  * 
				FROM '. $subject_table['TABLE_NAME'].' 
				WHERE '. $entitie->identifier.' = "'. $id.'" ');
				$data = $request->fetch(PDO::FETCH_ASSOC);
				if (!empty($data)){
					$subject = [];
					foreach ($entitie as $key => $properties) {
						$text = '';
						foreach ($data as $clefs => $valeur) {
							if (is_string($properties)) {
								
								if ($clefs == $properties) {
									$subject["alternative"] = $entitie->alternative; 
									if ($key == 'picture') {
										$valeur = base64_encode($valeur);
									}
									$subject[$key] = $valeur;
								}
							}elseif(is_array($properties)){
								foreach($properties as $field){
									if ($field == $clefs ) {
										$text .= $valeur . ' ';
										$subject[$key] = $text;
										
									}
								}
							}		
						}
					}
					
					return $subject;
				}else return null;
			} else return null;
		} else return null;
  }

  public function create_secondary_entities(object $entitie, string $identifier){
		$pattern = "@";
		if (stripos($identifier, $pattern)) {
			$request = explode('@', $identifier);
			$subject_table = $this->get_subject_table($request[1]);
			if (!empty($subject_table) and $entitie->identifier  == $request[0]){
				$request = $this->Db->Pdo->query('SELECT  * 
				FROM ' . $subject_table['TABLE_NAME'] . ' 
				WHERE ' . $entitie->identifier . ' = "' . end($request) . '" ');
				$data = $request->fetch(PDO::FETCH_ASSOC);
				if (!empty($data)) {
					$subject = [];
					foreach ($entitie as $key => $properties) {
						$text = '';
						foreach ($data as $clefs => $valeur) {
							if (is_string($properties)) {

								if ($clefs == $properties) {
									$subject["alternative"] = $entitie->alternative;
									$subject["name"] = $entitie->name;
									if ($key == 'picture') {
										$valeur = base64_encode($valeur);
									}
									$subject[$key] = $valeur;
								}
							} elseif (is_array($properties)) {
								foreach ($properties as $field) {
									if ($field == $clefs) {
										$text .= $valeur . ' ';
										$subject[$key] = $text;
									}
								}
							}
						}
					}
					return $subject;
				} else return null;
			}else return null;
		}else return null;
  }



  public function insert_ticket(array $post){
	$request = $this->Db->Pdo->prepare("
	INSERT INTO ticket  (tk__motif,		 	tk__motif_id ,	 	tk__titre , tk__indic ) 
	VALUES              (:tk__motif,      :tk__motif_id,      :tk__titre , :tk__indic)"); 
	$request->bindValue(":tk__motif", $post['type']);
	$request->bindValue(":tk__motif_id", null);
	$request->bindValue(":tk__titre",  $post['Titre']);
	$request->bindValue(":tk__indic",  'STD');
	$request->execute();
	$id = $this->Db->Pdo->lastInsertId();
	return $id;
  }

  public function insert_line(array $post){
	$request = $this->Db->Pdo->prepare("
	INSERT INTO ticket_ligne  (tkl__tk_id,	tkl__user_id ,	 	tkl__dt,  	tkl__motif_ligne,  tkl__user_id_dest , tkl__memo) 
	VALUES      			  (:tkl__tk_id,  :tkl__user_id,      :tkl__dt, :tkl__motif_ligne,   :tkl__user_id_dest , :tkl__memo)"); 
	$request->bindValue(":tkl__tk_id", $post['id_ligne']);
	$request->bindValue(":tkl__user_id", $post['creator']);
	$request->bindValue(":tkl__dt",  $post['dt']);
	$request->bindValue(":tkl__motif_ligne",  $post['motif']);
	$request->bindValue(":tkl__memo",  $post['libelle']);
	$request->bindValue(":tkl__user_id_dest",  $post['A_Qui']);
	$request->execute();
	$id = $this->Db->Pdo->lastInsertId();
	return $id;
  }

	public function insert_multipart( string $directory,  int $id_line , array $files){
	
		foreach ($files as $key  => $file){
			//check mime type and size 
			//rename without special char and space 
			$mime_type  = mime_content_type($file['tmp_name']);
			$file = file_get_contents($file['tmp_name']);
			$path = $directory . '/' . $id_line ;
			if (!is_dir($path)) {
				mkdir($path, 0777, TRUE);
			}
			
			file_put_contents($path, $file);
		}
		die();
	}

  public function insert_field(array $post , $id_ligne , $id_tickets){
		foreach ($post as $key => $value) {
				switch ($key) {
					case 'id_ligne':
					case 'creator':
					case 'dt':
					case 'motif':
					case 'libelle':
					case 'A_Qui':
					case 'type':
					case 'idSubject':
					case 'Titre':
					break;
					default:
						//requete pour chercher le type de champs : 
						
						$champs = $this->findChamp($post['motif'],$key);
						if (is_array($value)){
							$text = '';
							foreach ($value as  $response) {
								$text .= $response . ' '; 
							}
						}
						if ( isset($text) and strlen($text) > 2 ){
							$value =  $text;
						}
						$text = '';
						
						if (!empty($champs) and intval($champs->tksc__sujet) != 1 ) {
							$pattern = "@";
					
							if(stripos( $champs->tksc__option , $pattern)){
								$value = $champs->tksc__option . '@' . $value ;
								$request = $this->Db->Pdo->prepare("
								INSERT INTO ticket_ligne_champ  (tklc__id, tklc__nom_champ,  tklc__ordre,  tklc__memo ) 
								VALUES      			  (:tklc__id,  :tklc__nom_champ,  :tklc__ordre,  :tklc__memo)"); 
								$request->bindValue(":tklc__id", $id_ligne);
								$request->bindValue(":tklc__nom_champ", $key);
								$request->bindValue(":tklc__ordre",  $champs->tksc__ordre);
								$request->bindValue(":tklc__memo",  $value);
								$request->execute();
							}else{
								$request = $this->Db->Pdo->prepare("
								INSERT INTO ticket_ligne_champ  (tklc__id, tklc__nom_champ,  tklc__ordre,  tklc__memo ) 
								VALUES      			  (:tklc__id,  :tklc__nom_champ,  :tklc__ordre,  :tklc__memo)"); 
								$request->bindValue(":tklc__id", $id_ligne);
								$request->bindValue(":tklc__nom_champ", $key);
								$request->bindValue(":tklc__ordre",  $champs->tksc__ordre);
								$request->bindValue(":tklc__memo",  $value);
								$request->execute();
							} 
						}
						elseif(!empty($champs) and intval($champs->tksc__sujet) == 1 ){
							$update = new General($this->Db);
							$update->updateAll('ticket', $value , 'tk__motif_id' , 'tk__id' , $id_tickets);
						}
						
						break;
				}
		}
  }

  public function get_subject_list($array_column , $table_name){

	$request_string =  '';
	foreach ($array_column as $key => $column){
		if ($key === array_key_last($array_column)){
			$request_string .= $column . ' ';
		} else 	$request_string .= $column . ', ';
	
	}
	$request = $this->Db->Pdo->query('SELECT '.$request_string.'
	FROM  '.$table_name.' LIMIT 5000 ');
	$data = $request->fetchAll(PDO::FETCH_ASSOC);
	$array_response = [];
	foreach ($data as $results){
		$subject = new stdClass;
		$lib = '';
		foreach($results as $key => $value){
			if ($key === array_key_first($results)){
				$subject->id = $value ; 
			}
			else{
				$lib .= ' ' . $value . ' ';
			}
			
		}
		$subject->lib = $lib;
		array_push($array_response , $subject);
	}
	return $array_response;
}

  public function get_for_select($array_column, $table_name){
		$request_string =  '';
		foreach ($array_column as $key => $column) {
			if ($key === array_key_last($array_column)) {
				$request_string .= $column . ' ';
			} else 	$request_string .= $column . ', ';
		}
		$request = $this->Db->Pdo->query('SELECT ' . $request_string . '
		FROM  ' . $table_name . ' LIMIT 1 ');
		$data = $request->fetchAll(PDO::FETCH_ASSOC);
  }

  public function find_first_step($motif){
    $request = $this->Db->Pdo->query('SELECT  * , k.kw__info
		FROM ticket_scenario 
		LEFT JOIN keyword as k ON ( k.kw__type = "tmoti" AND k.kw__value = tks__motif ) 
		WHERE  ( tks__ordre = 1 AND tks__motif_ligne_preced IS NULL ) 
		AND ( tks__motif =  "' . $motif . '" ) 
		ORDER BY tks__ordre  LIMIT 50000');

	    $data = $request->fetch(PDO::FETCH_OBJ);
    	$request = $this->Db->Pdo->query('SELECT  *  FROM ticket_senar_champ 
		WHERE  tksc__motif_ligne =  "' . $data->tks__motif_ligne . '" 
		ORDER BY tksc__ordre LIMIT 50000');
		$champs = $request->fetchAll(PDO::FETCH_OBJ);
		$multiparts = null;
		foreach($champs as $key => $value){
		if ($value->tksc__type_de_champ == 'FIL') {
			$multiparts =  true;
		}
		if (!empty($value->tksc__option)){
			if (preg_match('/@/' , $value->tksc__option) == 1 and  $value->tksc__type_de_champ != 'CLI'){
				$request = explode('@',$value->tksc__option);
                $subject_list = $this->get_subject_table($request[0]);
				if (!empty($subject_list)){
					$subject_list = $this->get_subject_list($request , $subject_list['TABLE_NAME']);
					$value->tksc__option = $subject_list;
				}
			}
			else{
				$value->tksc__option = explode(';' ,$value->tksc__option);
			}
		}
	}
	$data->multiparts = $multiparts;
	$data->forms =  $champs;
	return $data;
  }

  public function find_next_step($step){
		$request = $this->Db->Pdo->query('SELECT  * , k.kw__info
		FROM ticket_scenario 
		LEFT JOIN keyword as k ON ( k.kw__type = "tmoti" AND k.kw__value = tks__motif ) 
		WHERE  ( tks__motif_ligne =  "' . $step . '" ) 
		ORDER BY tks__ordre  LIMIT 50000');

		$data = $request->fetch(PDO::FETCH_OBJ);
		$request = $this->Db->Pdo->query('SELECT  *  FROM ticket_senar_champ 
		WHERE  tksc__motif_ligne =  "' . $data->tks__motif_ligne . '" 
		ORDER BY tksc__ordre LIMIT 50000');
		$champs = $request->fetchAll(PDO::FETCH_OBJ);
		$multiparts = null;
		foreach ($champs as $key => $value) {
			if ($value->tksc__type_de_champ == 'FIL') {
				$multiparts =  true;
			}
			if (!empty($value->tksc__option)) {
				if (preg_match('/@/', $value->tksc__option) == 1 and  $value->tksc__type_de_champ != 'CLI') {
					$request = explode('@', $value->tksc__option);
					$subject_list = $this->get_subject_table($request[0]);
					if (!empty($subject_list)) {
						$subject_list = $this->get_subject_list($request, $subject_list['TABLE_NAME']);
						$value->tksc__option = $subject_list;
					}
				} else {
					$value->tksc__option = explode(';', $value->tksc__option);
				}
			}
		}
		$data->multiparts = $multiparts;
		$data->forms =  $champs;
		return $data;
  }

  public function findScenario($motif){
    $request = $this->Db->Pdo->query('SELECT  *  FROM ticket_scenario WHERE tks__motif =  "'.$motif.'" ORDER BY tks__ordre  LIMIT 50000');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

	public function findChamp($motif ,$nom){
		$request = $this->Db->Pdo->query('SELECT  tksc__option , tksc__ordre , tksc__sujet  FROM ticket_senar_champ WHERE tksc__motif_ligne =  "'.$motif.'" AND tksc__nom_champ = "'.$nom.'"');
		$data = $request->fetch(PDO::FETCH_OBJ);
		return $data;
	}


}