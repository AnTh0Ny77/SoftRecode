<?php


namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Methods\Pdfunctions;
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

  public function get_subject_table($column_name){
	  $request = $this->Db->Pdo->query('SELECT COLUMN_NAME  , TABLE_NAME 
	  FROM INFORMATION_SCHEMA.COLUMNS 
	  WHERE COLUMN_NAME =  "'.$column_name.'" ');
	  $data = $request->fetch(PDO::FETCH_ASSOC);
	  return $data;
  }


  public function insert_ticket(array $post){
	$request = $this->Db->Pdo->prepare("
	INSERT INTO ticket  (tk__motif,		 	tk__motif_id ,	 	tk__titre , tk__indic ) 
	VALUES              (:tk__motif,      :tk__motif_id,      :tk__titre , :tk__indic)"); 
	$request->bindValue(":tk__motif", $post['type']);
	$request->bindValue(":tk__motif_id", $post['idSubject']);
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

  public function insert_field(array $post , $id_ligne){
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
						var_dump($key , $value);
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
						
						if (!empty($champs)) {
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

  public function findScenario($motif){
    $request = $this->Db->Pdo->query('SELECT  *  FROM ticket_scenario WHERE tks__motif =  "'.$motif.'" ORDER BY tks__ordre  LIMIT 50000');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}

	public function findChamp($motif ,$nom){
		$request = $this->Db->Pdo->query('SELECT  tksc__option , tksc__ordre  FROM ticket_senar_champ WHERE tksc__motif_ligne =  "'.$motif.'" AND tksc__nom_champ = "'.$nom.'"');
		$data = $request->fetch(PDO::FETCH_OBJ);
		return $data;
	}


}