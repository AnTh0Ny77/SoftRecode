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

  /**
 * Différence entre bindParam et bindValue :
 *
 * bindParam permet de créer un lien direct entre une variable et un paramètre de requête
 * préparée, ce qui signifie que toute modification de la variable affectera la valeur du
 * paramètre dans la requête après la liaison. Utile lorsque la variable peut changer de
 * valeur entre la préparation et l'exécution de la requête.
 *
 * bindValue permet de lier directement une valeur à un paramètre de requête préparée, sans
 * établir de lien direct avec une variable. La valeur utilisée est celle au moment de la
 * liaison, et les modifications ultérieures de la variable n'affectent pas le paramètre
 * dans la requête. Utile lorsque vous souhaitez lier une valeur constante à un paramètre
 * de requête.
 */


  public function get_user_by_groups_lisible($groups){
    // Vérifier si les groupes ne sont ni 1001 ni 1002
    if ($groups != 1001 && $groups != 1002) {
        // Requête SQL pour récupérer les utilisateurs appartenant au groupe spécifié
        $query = "SELECT u.*
                  FROM utilisateur AS u
                  LEFT JOIN utilisateur_grp AS g ON (g.id_groupe = :groups)
                  WHERE (g.id_groupe = :groups AND u.id_utilisateur = g.id_utilisateur)
                  ORDER BY prenom";

        // Préparer la requête
        $statement = $this->Db->Pdo->prepare($query);

        // Binder la valeur de :groups
        $statement->bindParam(':groups', $groups, PDO::PARAM_INT);

        // Exécuter la requête
        $statement->execute();

        // Récupérer les données sous forme de classes
        $data = $statement->fetchAll(PDO::FETCH_CLASS);

        return $data;
    } else {
        // Requête SQL pour récupérer tous les utilisateurs
        $query = "SELECT *
                  FROM utilisateur
                  ORDER BY prenom";

        // Exécuter la requête
        $request = $this->Db->Pdo->query($query);

        // Récupérer les données sous forme de classes
        $data = $request->fetchAll(PDO::FETCH_CLASS);

        return $data;
    }
}


  public function get_groups_by_user($user){
            $request = $this->Db->Pdo->query("SELECT u.* 
			FROM utilisateur_grp as g
			LEFT JOIN utilisateur as u ON ( g.id_groupe = u.id_utilisateur OR g.id_utilisateur = u.id_utilisateur )
			WHERE ( ".$user." =  g.id_utilisateur  )
			ORDER BY prenom");
            $data = $request->fetchAll(PDO::FETCH_CLASS);
            return $data;
  }

  public function get_groups($user){
			$request = $this->Db->Pdo->query("SELECT g.id_groupe 
			FROM utilisateur_grp as g
			WHERE ( ".$user." =  g.id_utilisateur  )
			ORDER BY id_groupe");
			$data = $request->fetchAll(PDO::FETCH_CLASS);
			return $data;
  }

  public function get_groups_id_by_user($user){
	$request = $this->Db->Pdo->query("SELECT u.id_utilisateur 
	FROM utilisateur_grp as g
	LEFT JOIN utilisateur as u ON ( g.id_groupe = u.id_utilisateur )
	WHERE ( ".$user." =  g.id_utilisateur  )
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
	Max(ticket_ligne.tkl__dt) , ticket_ligne.tkl__tk_id
	FROM ticket_ligne
	LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
	WHERE tkl__user_id_dest  IN('.$string.') AND ( ticket.tk__lu = 0 ) AND  ( ticket.tk__motif = "DP" )
	AND  ticket_ligne.tkl__dt = ( SELECT Max(t.tkl__dt) FROM ticket_ligne as t WHERE t.tkl__tk_id = ticket.tk__id )
	GROUP BY ticket_ligne.tkl__tk_id');
	$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
	return $user_ticket;
}
public function get_all_ticket_for_user($id_user , $cloture){
	
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
	
	if ($cloture == 1 ) {
			$request = $this->Db->Pdo->query('SELECT 
			Max(ticket_ligne.tkl__dt) , ticket_ligne.tkl__tk_id
			FROM ticket_ligne
			LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
			WHERE tkl__user_id_dest  IN(' . $string . ') AND ( ticket.tk__lu = 0 or ticket.tk__lu = 1 or ticket.tk__lu = 2 )  AND  ( ticket.tk__motif = "DP" ) 
			AND  ticket_ligne.tkl__dt = ( SELECT Max(t.tkl__dt) FROM ticket_ligne as t WHERE t.tkl__tk_id = ticket.tk__id )
			GROUP BY ticket_ligne.tkl__tk_id');
			$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
			return $user_ticket;
	}else{
			$request = $this->Db->Pdo->query('SELECT 
			Max(ticket_ligne.tkl__dt) , ticket_ligne.tkl__tk_id
			FROM ticket_ligne
			LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
			WHERE tkl__user_id_dest  IN(' . $string . ') AND ( ticket.tk__lu = 0 or ticket.tk__lu = 1 )  AND  ( ticket.tk__motif = "DP" )
			AND  ticket_ligne.tkl__dt = ( SELECT Max(t.tkl__dt) FROM ticket_ligne as t WHERE t.tkl__tk_id = ticket.tk__id )
			GROUP BY ticket_ligne.tkl__tk_id');
			$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
			return $user_ticket;
	}
	
}

public function get_myRecode_for_user($id_user){

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
	}else{$string = $id_user;}
	
	$request = $this->Db->Pdo->query('SELECT 
	Max(ticket_ligne.tkl__dt) , ticket_ligne.tkl__tk_id
	FROM ticket_ligne
	LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
	WHERE tkl__user_id_dest  IN('.$string.') AND ( ticket.tk__lu = 3 AND  ticket.tk__motif = "TKM" )  
	AND  ticket_ligne.tkl__dt = ( SELECT Max(t.tkl__dt) FROM ticket_ligne as t WHERE t.tkl__tk_id = ticket.tk__id )
	GROUP BY ticket_ligne.tkl__tk_id');
	$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
	return $user_ticket;
}


public function get_myRecode_for_tech($id_user){

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
	Max(ticket_ligne.tkl__dt) , ticket_ligne.tkl__tk_id
	FROM ticket_ligne
	LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
	WHERE tkl__user_id_dest  IN('.$string.') AND ( ticket.tk__lu = 3 AND  ticket.tk__motif = "TKM" )  
	AND  ticket_ligne.tkl__dt = ( SELECT Max(t.tkl__dt) FROM ticket_ligne as t WHERE t.tkl__tk_id = ticket.tk__id )
	GROUP BY ticket_ligne.tkl__tk_id');
	$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
	return $user_ticket;
}


public function get_all_MyRecode_for_user($id_user , $cloture){
	
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
	
	if ($cloture == 1 ) {
			$request = $this->Db->Pdo->query('SELECT 
			Max(ticket_ligne.tkl__dt) , ticket_ligne.tkl__tk_id
			FROM ticket_ligne
			LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
			WHERE tkl__user_id_dest  IN(' . $string . ') AND ( ticket.tk__lu = 3 or ticket.tk__lu = 5 or ticket.tk__lu = 9 )  AND  ( ticket.tk__motif = "TKM" )
			AND  ticket_ligne.tkl__dt = ( SELECT Max(t.tkl__dt) FROM ticket_ligne as t WHERE t.tkl__tk_id = ticket.tk__id )
			GROUP BY ticket_ligne.tkl__tk_id');
			$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
			return $user_ticket;
	}else{
			$request = $this->Db->Pdo->query('SELECT 
			Max(ticket_ligne.tkl__dt) , ticket_ligne.tkl__tk_id
			FROM ticket_ligne
			LEFT JOIN ticket ON ticket_ligne.tkl__tk_id = ticket.tk__id
			WHERE tkl__user_id_dest  IN(' . $string . ') AND ( ticket.tk__lu = 3 or ticket.tk__lu = 5 ) AND  ( ticket.tk__motif = "TKM" )
			AND  ticket_ligne.tkl__dt = ( SELECT Max(t.tkl__dt) FROM ticket_ligne as t WHERE t.tkl__tk_id = ticket.tk__id )
			GROUP BY ticket_ligne.tkl__tk_id');
			$user_ticket = $request->fetchAll(PDO::FETCH_OBJ);
			return $user_ticket;
	}
	
}

}
