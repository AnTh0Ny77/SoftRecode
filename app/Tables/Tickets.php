<?php


namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Methods\Pdfunctions;
use PDO;


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

  public function find_first_step($motif){
      $request = $this->Db->Pdo->query('SELECT  *  FROM ticket_scenario 
	  LEFT JOIN keyword as k ON 
      WHERE  ( tks__ordre = 1 AND tks__motif_ligne_preced IS NULL ) 
      AND ( tks__motif =  "' . $motif . '" ) 
      ORDER BY tks__ordre  LIMIT 50000');
      $data = $request->fetch(PDO::FETCH_OBJ);

    $request = $this->Db->Pdo->query('SELECT  *  FROM ticket_senar_champ 
		WHERE  tksc__motif_ligne =  "' . $data->tks__motif_ligne . '" 
		ORDER BY tksc__ordre LIMIT 50000');
		$champs = $request->fetchAll(PDO::FETCH_OBJ);
		$data->forms =  $champs;
		return $data;
  }

  public function findScenario($motif){
    $request = $this->Db->Pdo->query('SELECT  *  FROM ticket_scenario WHERE tks__motif =  "'.$motif.'" ORDER BY tks__ordre  LIMIT 50000');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}


}