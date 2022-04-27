<?php
namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Tables\General;
use DateTime;
use PDO;

class UserGroup extends Table {
  
  public Database $Db;
  public function __construct($db){
    $this->Db = $db;
  }


  public function get_user_by_groups($groups){
    if ($groups != 1001 or $groups != 1002) {
            $request = $this->Db->Pdo->query("SELECT u.* 
            FROM utilisateur as u
            LEFT JOIN utilisateur_grp as g ON ( g.id_groupe = " . $groups .")
			WHERE (  g.id_groupe = " . $groups . " AND u.id_utilisateur =  g.id_utilisateur  )
            ORDER BY prenom");
            $data = $request->fetchAll(PDO::FETCH_CLASS);
            return $data;
    }else{
            $request = $this->Db->Pdo->query("SELECT * 
            FROM utilisateur 
            ORDER BY prenom");
            $data = $request->fetchAll(PDO::FETCH_CLASS);
            return $data;
    }  
  }

  public function ticket_notifier($id_user){

		$request = $this->Db->Pdo->query('SELECT * 
		FROM ticket 
		WHERE  ( SELECT MAX(tkl__dt) FROM ticket_ligne as t  WHERE t.tkl__user_id_dest = '. $id_user.'  ) ');
		$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
		
		return $user_ticket;
  }

}