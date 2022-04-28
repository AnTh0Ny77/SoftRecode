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

  public function get_groups_by_user($user){
            $request = $this->Db->Pdo->query("SELECT u.* 
            FROM utilisateur as u
            LEFT JOIN utilisateur_grp as g ON ( g.id_utilisateur = " . $user .")
			WHERE ( " . $user ." =  g.id_utilisateur  )
            ORDER BY prenom");
            $data = $request->fetchAll(PDO::FETCH_CLASS);
            return $data;
  }

  public function ticket_notifier($id_user){
		$request = $this->Db->Pdo->query('SELECT
		ticket_ligne.tkl__id,
		ticket_ligne.tkl__tk_id,
		ticket_ligne.tkl__user_id,
		ticket_ligne.tkl__dt,
		ticket_ligne.tkl__motif_ligne,
		ticket_ligne.tkl__memo,
		ticket_ligne.tkl__user_id_dest,
		ticket_ligne.tkl__visible,
		Max(ticket_ligne.tkl__dt),
		ticket.tk__lu
		FROM
		ticket_ligne
		left JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
		where tkl__user_id in('.$id_user.') and tk__lu = 1
		group by tkl__tk_id ');
		$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
		return $user_ticket;
  }


  public function ticket_notifier_groups($id_user){
		$array_groups = $this->get_groups_by_user($id_user);
		if (!empty($array_groups)){
			$string = "";
			foreach ($array_groups as $key => $value){
				if ($key === array_key_last($array_groups)){
					$string .= $value->id_utilisateur . ' ';
				}else{
					$string .= $value->id_utilisateur . ', ';
				}
			}
		}else{
			$string = $id_user;
		}

		
		$request = $this->Db->Pdo->query('SELECT
		Max(ticket_ligne.tkl__dt),
		ticket_ligne.tkl__id,
		ticket_ligne.tkl__tk_id,
		ticket_ligne.tkl__user_id,
		ticket_ligne.tkl__dt,
		ticket_ligne.tkl__motif_ligne,
		ticket_ligne.tkl__memo,
		ticket_ligne.tkl__user_id_dest,
		ticket_ligne.tkl__visible,
		ticket.tk__lu
		FROM
		ticket_ligne
		LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
		WHERE tkl__user_id IN('.$string.') and tk__lu = 1 GROUP BY tkl__tk_id');
		$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
		return $user_ticket;
}


public function get_ticket_for_user($id_user){

	$array_groups = $this->get_groups_by_user($id_user);
	if (!empty($array_groups)){
		$string = "";
		foreach ($array_groups as $key => $value){
			if ($key === array_key_last($array_groups)){
				$string .= $value->id_utilisateur . ' ';
			}else{
				$string .= $value->id_utilisateur . ', ';
			}
		}
	}else{
		$string = $id_user;
	}

	$request = $this->Db->Pdo->query('SELECT
		Max(ticket_ligne.tkl__dt),
		ticket_ligne.tkl__tk_id
		FROM
		ticket_ligne
		LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
		WHERE tkl__user_id IN('.$string.') and  ( ticket.tk__lu = 0 )
		GROUP BY ticket_ligne.tkl__tk_id ');
		$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
		return $user_ticket;
}
public function get_all_ticket_for_user($id_user){
	
	$array_groups = $this->get_groups_by_user($id_user);
	if (!empty($array_groups)){
		$string = "";
		foreach ($array_groups as $key => $value){
			if ($key === array_key_last($array_groups)){
				$string .= $value->id_utilisateur . ' ';
			}else{
				$string .= $value->id_utilisateur . ', ';
			}
		}
	}else{
		$string = $id_user;
	}

	$request = $this->Db->Pdo->query('SELECT
		Max(ticket_ligne.tkl__dt),
		ticket_ligne.tkl__tk_id
		FROM
		ticket_ligne
		LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
		WHERE tkl__user_id IN('.$string.') and  ( ticket.tk__lu = 1 or ticket.tk__lu = 0  )
		GROUP BY ticket_ligne.tkl__tk_id ');
		$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
		return $user_ticket;
}

}