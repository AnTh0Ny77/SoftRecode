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

  public function findScenario($motif){
    $request = $this->Db->Pdo->query('SELECT  *  FROM ticket_scenario WHERE tks__motif =  "'.$motif.'" ORDER BY tks__ordre  LIMIT 50000');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}


}