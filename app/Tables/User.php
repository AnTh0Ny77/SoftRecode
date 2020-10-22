<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

/*ooo     ooo  .oooooo..o oooooooooooo ooooooooo.   
`888'     `8' d8P'    `Y8 `888'     `8 `888   `Y88. 
 888       8  Y88bo.       888          888   .d88' 
 888       8   `"Y8888o.   888oooo8     888ooo88P'  
 888       8       `"Y88b  888    "     888`88b.    
 `88.    .8'  oo     .d8P  888       o  888  `88b.  
   `YbodP'    8""88888P'  o888ooooood8 o888o  o888*/
   


   

class User extends Table 
{
  public string $Table;
  public Database $Db;
  private object $Request;

  public function __construct($db) 
  {
    $this->Db = $db;
    $this->Table = 'utilisateur';
    $this->Request =$this->Db->Pdo->prepare("SELECT * FROM ".$this->Table." WHERE login=? ");
  }

  public function getAll()
  {
    $request =$this->Db->Pdo->query("SELECT * FROM utilisateur WHERE type_user > 0 ORDER BY nom" );
    $data = $request->fetchAll(PDO::FETCH_CLASS);
    return $data;
  }

  public function getByID($id)
  {
    $request = $this->Db->Pdo->prepare("SELECT *  FROM utilisateur WHERE id_utilisateur = ?");
    $request->execute(array($id));
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }


  public function getCommerciaux()
  {
    $request = $this->Db->Pdo->prepare("SELECT id_utilisateur , log_nec  , prenom , nom
    FROM utilisateur 
    WHERE fonction = 'commercial' 
    OR fonction = 'Commercial'
    OR fonction = 'Responsable Commercial'
    OR fonction = 'Assistante commerciale'
    ");
    $request->execute();
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function login($login)
  {
    $this->Request->execute(array($login));
    $this->Request->setFetchMode(PDO::FETCH_OBJ);
    $data = $this->Request->fetch();
    return $data;
  }

 /*""b8 88""Yb 888888    db    888888 
dP   `" 88__dP 88__     dPYb     88   
Yb      88"Yb  88""    dP__Yb    88   
 YboodP 88  Yb 888888 dP""""Yb   8*/
  public function create($id, $login, $date_arrive, $prenom, $nom, $log_nec, $email, $postefix, $gsm, $t_crm, $type_user, $fonction, $devis_acces, $cmd_acces ,$saisie_acces, $facture_acces, $admin_acces, $ticket_acces, $password)
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table.
    "(
    id_utilisateur, login, datearrive, prenom, nom, log_nec, email, postefix, gsmperso, t_crm, type_user, fonction, 
    user__devis_acces, user__cmd_acces, user__admin_acces, user__facture_acces, user__saisie_acces, user__ticket_acces, password_user )
    VALUES (
    :id,         :login, :date_arrive, :prenom, :nom, :log_nec, :email, :postefix, :gsm, :t_crm, :type_user, :fonction, 
    :devis_acces,    :cmd_acces,     :admin_acces,      :facture_acces,     :saisie_acces,     :ticket_acces,    :password )");
    $request->bindValue(":id", $id);
    $request->bindValue(":login", $login);
    $request->bindValue(":date_arrive", $date_arrive);
    $request->bindValue(":prenom", $prenom);
    $request->bindValue(":nom", $nom);
    $request->bindValue(":log_nec", $log_nec);
    $request->bindValue(":email", $email);
    $request->bindValue(":postefix", $postefix);
    $request->bindValue(":gsm", $gsm);
    $request->bindValue(":t_crm", $t_crm);
    $request->bindValue(":type_user", $type_user);
    $request->bindValue(":fonction", $fonction);
    $request->bindValue(":devis_acces", $devis_acces);
    $request->bindValue(":cmd_acces", $cmd_acces);
    $request->bindValue(":admin_acces", $admin_acces);
    $request->bindValue(":facture_acces", $facture_acces);
    $request->bindValue(":saisie_acces", $saisie_acces);
    $request->bindValue(":ticket_acces", $ticket_acces);
    $request->bindValue(":password", $password);
    $request->execute();
    $idUser = $this->Db->Pdo->lastInsertId();
    return $idUser;
  }

/*    d8  dP"Yb  8888b.  88 888888 Yb  dP 
88b  d88 dP   Yb  8I  Yb 88 88__    YbdP  
88YbdP88 Yb   dP  8I  dY 88 88""     8P   
88 YY 88  YbodP  8888Y"  88 88      d*/
  public function modify($id, $login, $date_arrive, $prenom, $nom, $log_nec, $email, $postefix, $gsm, $t_crm, $type_user, $fonction, $devis_acces, $cmd_acces ,$saisie_acces, $facture_acces, $admin_acces, $ticket_acces, $password)
  {
    // Exemple de MAJ SQL : UPDATE utilisateur SET nom='8888', login='888', log_nec='888', email='888' WHERE (id_utilisateur='998546') LIMIT 1
    // Update de tout sauf Password (qui n'est mis a jour que si il est present.)
    
    // mise a jour de tout exepté ID (non modifiable) et Password (Mise a jour plus bas que si il est modifié)
    $request = $this->Db->Pdo->prepare("update ".$this->Table.
    " SET 
    login=:login, datearrive=:date_arrive, prenom=:prenom, nom=:nom, log_nec=:log_nec, email=:email,
    postefix=:postefix, gsmperso=:gsm, t_crm=:t_crm, type_user=:type_user, fonction=:fonction, 
    user__devis_acces=:devis_acces, user__cmd_acces=:cmd_acces, user__admin_acces=:admin_acces, 
    user__facture_acces=:facture_acces, user__saisie_acces=:saisie_acces, user__ticket_acces=:ticket_acces 
    WHERE (id_utilisateur=:id) LIMIT 1");
    $request->bindValue(":id", $id);
    $request->bindValue(":login", $login);
    $request->bindValue(":date_arrive", $date_arrive);
    $request->bindValue(":prenom", $prenom);
    $request->bindValue(":nom", $nom);
    $request->bindValue(":log_nec", $log_nec);
    $request->bindValue(":email", $email);
    $request->bindValue(":postefix", $postefix);
    $request->bindValue(":gsm", $gsm);
    $request->bindValue(":t_crm", $t_crm);
    $request->bindValue(":type_user", $type_user);
    $request->bindValue(":fonction", $fonction);
    $request->bindValue(":devis_acces", $devis_acces);
    $request->bindValue(":cmd_acces", $cmd_acces);
    $request->bindValue(":admin_acces", $admin_acces);
    $request->bindValue(":facture_acces", $facture_acces);
    $request->bindValue(":saisie_acces", $saisie_acces);
    $request->bindValue(":ticket_acces", $ticket_acces);
    $request->execute();
    
    // mise a jour du password que si il est present (pour eviter de le re saisir a chaque modif)
    if ($password) 
    {
      $request = $this->Db->Pdo->prepare("update ".$this->Table.
      " SET 
      password_user=:password
      WHERE (id_utilisateur=:id) LIMIT 1");
      $request->bindValue(":id", $id);
      $request->bindValue(":password", $password);
      $request->execute();
    }
    return $id;
  }



}

?>
