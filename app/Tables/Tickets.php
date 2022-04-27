<?php


namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Tables\General;
use App\Tables\Article;
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

  public function getFiles($idligne){
	
	  if (is_dir('C:\laragon\www\SoftRecode\upload/'.$idligne.'')) {
		$files =  array_diff(scandir('C:\laragon\www\SoftRecode\upload/'.$idligne.''), array('..', '.'));
		
		if (!empty($files)){
		
			return $files;
		}
	  }		
  }


  public function get_last(){
	$results  = [];
	$request = $this->Db->Pdo->query('SELECT  t.* , MAX(l.tkl__dt) as last_date  FROM ticket as t
	LEFT JOIN ticket_ligne as l ON ( L.tkl__tk_id = t.tk__id ) 
	WHERE tk__lu != 2 GROUP BY   t.tk__id 
	ORDER BY last_date  DESC  LIMIT 20');
	$data = $request->fetchAll(PDO::FETCH_OBJ);
	foreach ($data as $ticket) {
		$ticket = $this->findOne($ticket->tk__id);
		array_push($results , $ticket);
	}
	return $results;
}

	public function get_last_in( $tickets){

		if (!empty($tickets)) {
			$results  = [];
			$text = '( ';
			foreach ($tickets as $key => $entry) {
				if ($key === array_key_last($tickets)) {
					$text .= $entry->tkl__tk_id . ' ';
				} else $text .= $entry->tkl__tk_id . ', ';
			}
			$text .= ' )';
			$request = $this->Db->Pdo->query('SELECT  t.* , MAX(l.tkl__dt) as last_date  FROM ticket as t
			LEFT JOIN ticket_ligne as l ON ( L.tkl__tk_id = t.tk__id ) 
			WHERE tk__lu != 2 AND ( t.tk__id IN  ' . $text . ')  GROUP BY t.tk__id 
			ORDER BY last_date DESC  LIMIT 20');
			$data = $request->fetchAll(PDO::FETCH_OBJ);

			foreach ($data as $ticket) {
				$ticket = $this->findOne($ticket->tk__id);
				array_push($results, $ticket);
			}
			return $results;
		} else return null;
	}

	public function get_last_ticket( $tickets){

			if (!empty($tickets)) {
				$results  = [];
				$text = '( ';
				foreach ($tickets as $key => $entry) {
					if ($key === array_key_last($tickets)) {
						$text .= $entry->tk__id . ' ';
					} else $text .= $entry->tk__id . ', ';
				}
				$text .= ' )';
				$request = $this->Db->Pdo->query('SELECT  t.* , MAX(l.tkl__dt) as last_date  FROM ticket as t
				LEFT JOIN ticket_ligne as l ON ( L.tkl__tk_id = t.tk__id ) 
				WHERE tk__lu != 2 AND ( t.tk__id IN  ' . $text . ')  GROUP BY t.tk__id 
				ORDER BY last_date DESC  LIMIT 20');
				$data = $request->fetchAll(PDO::FETCH_OBJ);

				foreach ($data as $ticket) {
					$ticket = $this->findOne($ticket->tk__id);
					array_push($results, $ticket);
				}
				return $results;
			} else return null;
	}

  public function get_subject_table($column_name){
	  $request = $this->Db->Pdo->query('SELECT COLUMN_NAME  , TABLE_NAME 
	  FROM INFORMATION_SCHEMA.COLUMNS 
	  WHERE COLUMN_NAME =  "'.$column_name.'" ');
	  $data = $request->fetch(PDO::FETCH_ASSOC);
	  return $data;
  }

  public function return_group($id){

	$request = $this->Db->Pdo->query('SELECT tk__groupe  as groupe  FROM ticket WHERE tk__id = '.$id.'');
	$data = $request->fetch(PDO::FETCH_OBJ);
	if (!empty($data->groupe)){
		return intval($data->groupe);
	}else{
		$request = $this->Db->Pdo->query('SELECT MAX( tk__groupe ) as groupe  FROM ticket');
		$data = $request->fetch(PDO::FETCH_OBJ);
		return intval($data->groupe) +1 ;
	}
  }


  public function findOne($id){
	    $tmoti = "tmoti";
		$request = $this->Db->Pdo->query('SELECT  t.* , k.kw__lib 
		FROM ticket as t
		LEFT JOIN keyword as k ON ( k.kw__value = t.tk__motif AND  k.kw__type= "tmoti") 
		WHERE t.tk__id = "'.$id.'" ');
		$ticket = $request->fetch(PDO::FETCH_OBJ);
		$request = $this->Db->Pdo->query('SELECT  l.*  , u.nom , u.prenom , z.nom as nom_dest , z.prenom as prenom_dest , z.id_utilisateur as id_dest
		FROM ticket_ligne as l
		LEFT JOIN utilisateur AS u ON ( u.id_utilisateur = l.tkl__user_id ) 
		LEFT JOIN utilisateur AS z ON ( z.id_utilisateur = l.tkl__user_id_dest ) 
		WHERE l.tkl__tk_id = "'.$id.'" ');
		$lignes = $request->fetchAll(PDO::FETCH_OBJ);
		$ticket->lignes = $lignes;
		$ticket->last_line = $this->get_last_line($id);
		$ticket->first_line = $this->get_first_line($id);
		foreach ($ticket->lignes as $ligne){
			$date_time = new DateTime($ligne->tkl__dt);
			//formate la date pour l'utilisateur:
			$ligne->tkl__dt = $date_time->format('d/m/Y H:i');
			$request = $this->Db->Pdo->query('SELECT  c.* , t.tksc__label , t.tksc__visible
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
		$request = $this->Db->Pdo->query('SELECT * , u.nom , u.prenom , z.id_utilisateur as id_dest , z.nom as nom_dest , z.prenom as prenom_dest 
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

	public function get_first_line($id)
	{
		$request = $this->Db->Pdo->query('SELECT  MIN(tkl__dt) as dateLigne  FROM ticket_ligne 
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


  public function insert_ticket(array $post ){

	if ($post['type'] === 'DP' && empty($post['Titre'])) {
		$post['Titre'] = $post['Quantite'] . ' - ' ;
		$Article = new Article($this->Db);
		$Pn = $Article->get_pn_byID($post['Pn']);
		$post['Titre'] .=  $Pn->apn__pn_long ;
	}
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
			// $file = file_get_contents($file['tmp_name']);
			$path = $directory . '/' . $id_line ;
			if (!is_dir($path)) {
				mkdir($path, 0777, TRUE);
			}
			move_uploaded_file($file["tmp_name"], $path.'/'.$file['name']);
		}
		
	}


	public function attribute_attachements( int $id_line)
	{
			$path =  'C:\laragon\www\SoftRecode\upload\temp';
			if (is_dir($path)) {
				rename('C:\laragon\www\SoftRecode\upload\temp', 'C:\laragon\www\SoftRecode\upload/'.$id_line.'');
				return true;
			}else return false;		
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

  public function cloture_ticket($id , $user_id , $date , $commentaire){
		$update = new General($this->Db);
		$update->updateAll('ticket',  2 , 'tk__lu' , 'tk__id' , $id);
		$request = $this->Db->Pdo->prepare("
		INSERT INTO ticket_ligne  (tkl__tk_id,	tkl__user_id ,	 	tkl__dt,  	tkl__motif_ligne,  tkl__user_id_dest , tkl__memo) 
		VALUES      			  (:tkl__tk_id,  :tkl__user_id,      :tkl__dt, :tkl__motif_ligne,   :tkl__user_id_dest , :tkl__memo)"); 
		$request->bindValue(":tkl__tk_id", $id);
		$request->bindValue(":tkl__user_id", $user_id);
		$request->bindValue(":tkl__dt",  $date);
		$request->bindValue(":tkl__motif_ligne",  'cloture');
		$request->bindValue(":tkl__memo",  'cloture du ticket');
		$request->bindValue(":tkl__user_id_dest",  0 );
		$request->execute();
		$ligne = $this->Db->Pdo->lastInsertId();
		$request = $this->Db->Pdo->prepare("
				INSERT INTO ticket_ligne_champ  (tklc__id, tklc__nom_champ,  tklc__ordre,  tklc__memo ) 
				VALUES      			  (:tklc__id,  :tklc__nom_champ,  :tklc__ordre,  :tklc__memo)"); 
		$request->bindValue(":tklc__id", $ligne);
		$request->bindValue(":tklc__nom_champ", 'cloture');
		$request->bindValue(":tklc__ordre",  999);
		$request->bindValue(":tklc__memo",  $commentaire);
		$request->execute();
		return $id;
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
		WHERE  ( tks__id =  "' . $step . '"  ) 
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
				if (preg_match('/@/', $value->tksc__option) == 1 and  $value->tksc__type_de_champ != 'CLI'){
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


public function search_ticket( string $input , array $config ){
	switch ($input) {
		// case strlen($input) == 7 and is_numeric($input):
			
		// 	return 'commande';
		// 	break;

		case strlen($input) == 6 and is_numeric($input):
			$list = $this->search_ticket_with_id('client', $input);
			$list = $this->get_last_ticket($list);
			return $list;
			break;

		case strlen($input) == 4 and is_numeric($input):
			$list = $this->search_ticket_with_id('id', $input);
			$list = $this->get_last_ticket($list);
			return $list;
			break;

		case strlen($input) == 3 and is_numeric($input):
			$list = $this->search_ticket_with_id('ticket', $input);
			$list = $this->get_last_ticket($list);
			return $list;
			break;
		default:
				// $list = $this->searchTicket($input, $config);
				// $list = $this->get_tickets_with_line($list);
				$list_in_ticket = $this->search_in_ticket($input);
				$list_in_ticket = $this->get_last_ticket($list_in_ticket);
				$list = $list_in_ticket ; 
				
				$list_in_ligne = $this->search_in_ticket_ligne($input);
				$list_in_ligne = $this->get_last_ticket($list_in_ligne);
				if (!empty($list_in_ligne) && !empty($list)) {
					$list = array_merge($list, $list_in_ligne);
				} elseif (empty($list) && !empty($list_in_ligne)){
					$list = $list_in_ligne;
				}
				$list_in_subject = $this->search_in_subject($input , $config);
				$list_in_subject = $this->get_last_ticket($list_in_subject);
				if (!empty($list)  && !empty($list_in_subject) ){
					$list = array_merge($list, $list_in_subject);
				}elseif(empty($list)  && !empty($list_in_subject)){
					$list =  $list_in_subject;
				}
				
				
				$list = $this->my_array_unique($list);
				return $list;
			break;
	}
}

public function my_array_unique($array){
    $duplicate_keys = array();
    $tmp = array();       

	if (!empty($array) and  count($array) > 1  ) {
		foreach ($array as $key => $val){
			// convert objects to arrays, in_array() does not support objects
			if (is_object($val))
				$val = (array)$val;
	
			if (!in_array($val, $tmp))
				$tmp[] = $val;
			else
				$duplicate_keys[] = $key;
		}
	
		foreach ($duplicate_keys as $key)
			unset($array[$key]);
	
		foreach ($array as $value) 
				$value = (object)$value;
	}

	
    return $array;
}



public function search_ticket_with_id(string $table , int $id){

		switch ($table) {
			case 'client':
				$request = $this->Db->Pdo->query("SELECT  tk__id FROM ticket 
				WHERE  CONCAT('0000 ',tk__motif_id) LIKE '". $id . "' ORDER BY tk__id LIMIT 1000");
				$data = $request->fetchAll(PDO::FETCH_OBJ);
				$array_field_results = [];
				$request = $this->Db->Pdo->query("SELECT  tklc__id , tklc__memo 
				FROM ticket_ligne_champ
				WHERE tklc__nom_champ = 'client' ORDER BY tklc__id  LIMIT 1000");
				$data_champs = $request->fetchAll(PDO::FETCH_OBJ);
				foreach ($data_champs as $key => $value){
					$id_client = explode('@', $value->tklc__memo);
					$id_client = end($id_client);
					if ($id_client == $id){
						$request = $this->Db->Pdo->query("SELECT  tkl__tk_id as tk__id 
						FROM ticket_ligne
						WHERE tkl__id = ". $value->tklc__id." ORDER BY tk__id LIMIT 1000");
						$data_champs = $request->fetch(PDO::FETCH_OBJ);
						array_push($array_field_results , $data_champs);
					}
				}
				if (!empty($array_field_results)) {
					$data = array_merge($data , $array_field_results);
				}
				return $data;


				break;
			case 'commande':
				# code...
				break;
			case 'ticket':
				$request = $this->Db->Pdo->query("SELECT  tk__id FROM ticket 
				WHERE   tk__groupe = '" . $id . "' ORDER BY tk__id LIMIT 1000");
				$data = $request->fetchAll(PDO::FETCH_OBJ);
				return $data;
				break;

			case 'id':
				$request = $this->Db->Pdo->query("SELECT  tk__id FROM ticket 
				WHERE   tk__id = '" . $id . "' ORDER BY tk__id LIMIT 1000");
				$data = $request->fetchAll(PDO::FETCH_OBJ);
					return $data;
					break;
			
		}
		
}


public function search_in_subject(string  $filtre, array  $entities){
	$array_results = [];
	$request = $this->Db->Pdo->query('SELECT  tk__motif , tk__id , c.tksc__option
	FROM ticket 
	LEFT JOIN ticket_scenario as s ON ( s.tks__motif  =  tk__motif ) 
	LEFT JOIN ticket_senar_champ as c ON ( s.tks__motif_ligne = c.tksc__motif_ligne ) 
	WHERE tk__motif_id IS NOT NULL 
	AND ( c.tksc__sujet = 1 ) 
	LIMIT 50000');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
	foreach ($data as $key => $value) {
		if (preg_match('/@/' , $value->tksc__option) == 1){
			$request = explode('@',$value->tksc__option);
                $subject_list = $this->get_subject_table($request[0]);
				if (!empty($subject_list)){
					foreach ($entities as $entitie) {
						if ($entitie->table == $subject_list['TABLE_NAME']){
							$match = $this->search_in_config($subject_list['TABLE_NAME'] , $entitie , $filtre);
							if ($match != null){
								array_push($array_results , $value);
							}
						}
					}
				}
		}
	}
	
	return $array_results;
}


public function search_in_config($table_name , $entitie , $filtre){
	$filtre = str_replace("-", ' ', $filtre);
	$filtre = str_replace("'", ' ', $filtre);
	$nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
	$mots_filtre = str_word_count($filtre, 1, '0123456789');
	$operateur = "AND ";
	$request = "SELECT  " . $entitie->identifier . " FROM " . $table_name . " WHERE 1 = 1 ";
	$request .=   $operateur . " ( " . $entitie->identifier . " = '" . $mots_filtre[0] . "' ";
	for ($i = 0; $i < count($entitie->label); $i++){
		$request .=  "OR " . $entitie->label[$i] . " LIKE '%" . $mots_filtre[0] . "%' ";
	}
	$request .= " ) ";
	for ($i = 1; $i < $nb_mots_filtre; $i++){
		if ($i == 1 ){
			$request .=   $operateur . " ( " . $entitie->identifier . " = '" . $mots_filtre[$i] . "' ";
		}
		for ($y = 0; $y < count($entitie->label); $y++) {
				$request .=  "OR " . $entitie->label[$y] . " LIKE '%" . $mots_filtre[$i] . "%' ";
		}
	}
	if ($nb_mots_filtre > 1){
		$request .= " ) ";
	}
	$request .= "ORDER BY  " . $entitie->identifier . " ASC  LIMIT 100";
	
	$send = $this->Db->Pdo->query($request);
	$data = $send->fetch(PDO::FETCH_OBJ);
	
	
	return $data;
}

public function format_string(string $input){
	$filtre = str_replace("-", ' ',$input);
	$filtre = str_replace("'", ' ', $filtre);
	$nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
	$mots_filtre = str_word_count($filtre, 1, '0123456789');
	if ($nb_mots_filtre == 1 ){$mode_filtre = false; }else $mode_filtre = true;
	return $mots_filtre;
}


public function search_in_entities( string $table, string  $filtre , object  $entitie){

		
		$filtre = str_replace("-", ' ', $filtre);
		$filtre = str_replace("'", ' ', $filtre);
		$nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
		$mots_filtre = str_word_count($filtre, 1, '0123456789');
		$operateur = "AND ";

		if ($entitie->table == $table) {
			$request = "SELECT  " . $entitie->identifier . "
			FROM " . $table . "
			WHERE 1 = 1 ";

			$request .=   $operateur . " ( " . $entitie->identifier . " = '" . $mots_filtre[0] . "' ";
			for ($i = 0; $i < count($entitie->label); $i++) {
				$request .=  "OR " . $entitie->label[$i] . " LIKE '%" . $mots_filtre[0] . "%' ";
			}
			$request .= " ) ";

			for ($i = 1; $i < $nb_mots_filtre; $i++) {
				if ($i == 1 ) {
					$request .=   $operateur . " ( " . $entitie->identifier . " = '" . $mots_filtre[$i] . "' ";
				}
				for ($y = 0; $y < count($entitie->label); $y++) {
						$request .=  "OR " . $entitie->label[$y] . " LIKE '%" . $mots_filtre[$i] . "%' ";
				}
				
				
			}
			if ($nb_mots_filtre > 1) {
				$request .= " ) ";
			}
			$request .= "ORDER BY  " . $entitie->identifier . " ASC  LIMIT 100  ";
		
			$send = $this->Db->Pdo->query($request);
			$data = $send->fetchAll(PDO::FETCH_OBJ);
			return $data;
		} else return null;
		
}

public function search_in_ticket(string  $filtre){
		$filtre = str_replace("-", ' ', $filtre);
		$filtre = str_replace("'", ' ', $filtre);
		$nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
		$mots_filtre = str_word_count($filtre, 1, '0123456789');
		$operateur = "AND ";

		$request = "SELECT tk__id  FROM ticket WHERE  ( tk__id = '" . $mots_filtre[0] . "' 
		OR tk__motif LIKE '%" . $mots_filtre[0] . "%' 
		OR tk__titre LIKE '%" . $mots_filtre[0] . "%' )";

		if (count($mots_filtre) >  1 ) {
			$request .= $operateur . " ( ";
			for ($i = 1; $i < $nb_mots_filtre; $i++){
				if ($i == 1) {
					$request .= " tk__motif LIKE '%" . $mots_filtre[$i] . "%'"; 
				}else $request .= "OR  tk__motif LIKE '%" . $mots_filtre[$i] . "%'
				OR tk__titre LIKE '%" . $mots_filtre[$i] . "%' "; 	
			}
			$request .= ' )';
		}
		
		
		$send = $this->Db->Pdo->query($request);
		$data = $send->fetchAll(PDO::FETCH_OBJ);
		return $data;
}

	public function search_in_ticket_ligne(string  $filtre)
	{
		$filtre = str_replace("-", ' ', $filtre);
		$filtre = str_replace("'", ' ', $filtre);
		$nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
		$mots_filtre = str_word_count($filtre, 1, '0123456789');
		$operateur = "AND ";

		$request = "SELECT tkl__tk_id as  tk__id  
		FROM ticket_ligne 
		LEFT JOIN utilisateur as u ON ( tkl__user_id = u.id_utilisateur ) 
		LEFT JOIN utilisateur as w ON ( tkl__user_id_dest = w.id_utilisateur ) 
		WHERE  ( tkl__motif_ligne  LIKE '%" . $mots_filtre[0] . "%' 
		OR u.prenom LIKE '%" . $mots_filtre[0] . "%' 
		OR u.nom LIKE '%" . $mots_filtre[0] . "%'
		OR w.nom LIKE '%" . $mots_filtre[0] . "%'
		OR w.prenom LIKE '%" . $mots_filtre[0] . "%')";

		if (count($mots_filtre) >  1) {
			$request .= $operateur . " ( ";
			for ($i = 1; $i < $nb_mots_filtre; $i++) {
				if ($i == 1) {
					$request .= " tkl__motif_ligne LIKE '%" . $mots_filtre[$i] . "%'";
				} else {
					$request .= "OR  u.prenom LIKE '%" . $mots_filtre[$i] . "%'
					OR u.nom LIKE '%" . $mots_filtre[$i] . "%' 
					OR w.nom LIKE '%" . $mots_filtre[$i] . "%'
					OR w.prenom LIKE '%" . $mots_filtre[$i] . "%'";
				} 
			}
			$request .= ' )';
		}
		
	
		$send = $this->Db->Pdo->query($request);
		$data = $send->fetchAll(PDO::FETCH_OBJ);
		return $data;
	}

public function get_tickets_with_line(array $line){

		if (!empty($line)) {
			$text = '( ';
			foreach ($line as $key => $entry) {
				if ($key === array_key_last($line)) {
					$text .= $entry . ' ';
				} else $text .= $entry . ', ';
			}
			$text .= ' )';
			$request = $this->Db->Pdo->query('SELECT tkl__tk_id  FROM ticket_ligne WHERE tkl__id  IN   ' . $text . ' GROUP BY tkl__tk_id LIMIT 500');
			$data = $request->fetchAll(PDO::FETCH_OBJ);
			return $data;
		}
	    
}

public function searchTicket(string $input, array $config){
	$results_array = [];
	
	$request = $this->Db->Pdo->query('SELECT  tklc__id  , tklc__memo  FROM ticket_ligne_champ WHERE 1 = 1  LIMIT 50000');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
	foreach ($data as $key => $value){
		//si cela est relié a une entité en base de donnée :
		if (preg_match('/@/' , $value->tklc__memo) == 1){
			$request = explode('@',$value->tklc__memo);
                $subject_list = $this->get_subject_table($request[0]);
				if (!empty($subject_list)){
					foreach ($config as $entitie) {
						$match = $this->search_in_entities($subject_list['TABLE_NAME'], $input, $entitie);
						if ($match != null) {
							array_push($results_array, $value->tklc__id);
						}
					}
					
				}
		}else{

		}
	}
	return $results_array ;
}

public function find_ticket($input){
	$filtre = str_replace("-", ' ',$input);
	$filtre = str_replace("'", ' ', $filtre);
	$nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
	$mots_filtre = str_word_count($filtre, 1, '0123456789');
	if ($nb_mots_filtre == 1 ) {
		$mode_filtre = false;
	}else $mode_filtre = true;
	$operateur = "AND ";

}


}