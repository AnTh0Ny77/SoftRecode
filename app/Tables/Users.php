<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class Users extends Table {

  public string $Table;
  public Database $Db;
  private object $Request;

  public function __construct($db) {
    $this->Db = $db;
    $this->Table = 'utilisateur';
    $this->Request =$this->Db->Pdo->prepare("SELECT id_utilisateur , password_user , prenom , log_nec , nom , icone ,email  FROM  ".$this->Table. " WHERE 
    login=? ");
}

  public function getAll(){
      $request =$this->Db->Pdo->query('SELECT * FROM utilisateur');
      $data = $request->fetchAll(PDO::FETCH_CLASS);
      return $data;
  }

  public function getByID($id){
    $request = $this->Db->Pdo->prepare("SELECT *  FROM " .$this->Table. " WHERE id_utilisateur = " . $id ."");
    $data = $request->fetch(PDO::FETCH_CLASS);
    return $data;
  }

  public function login($login){
    $this->Request->execute(array($login));
    $this->Request->setFetchMode(PDO::FETCH_OBJ);
    $data = $this->Request->fetch();
    return $data;
  }

public function create($id, $login, $date, $prenom, $nom, $log_nec, $email, $postefix, $gsm , $t_crm , $po_valid , $devis, $cmd ,$saisie, $facture , $admin , $password){
  $request = $this->Db->Pdo->prepare('INSERT INTO ' .$this->Table.
  "(id_utilisateur, login, datearrive, prenom, nom, log_nec, email, postefix, gsmperso , t_crm, po_valid, user__devis_acces, user__cmd_acces, user__admin_acces, user__facture_acces, user__saisie_acces , password_user )
   VALUES (:id, :login, :date, :prenom, :nom, :log_nec , :email, :postefix, :gsm, :t_crm , :po_valid, :devis, :cmd, :facture, :saisie, :admin , :password)");
   $request->bindValue(":id", $id);
   $request->bindValue(":login", $login);
   $request->bindValue(":date", $date);
   $request->bindValue(":prenom", $prenom);
   $request->bindValue(":nom", $nom);
   $request->bindValue(":log_nec", $log_nec);
   $request->bindValue(":email", $email);
   $request->bindValue(":postefix", $postefix);
   $request->bindValue(":gsm", $gsm);
   $request->bindValue(":t_crm", $t_crm);
   $request->bindValue(":po_valid", $po_valid);
   $request->bindValue(":devis", $devis);
   $request->bindValue(":cmd", $cmd);
   $request->bindValue(":facture", $facture);
   $request->bindValue(":saisie", $saisie);
   $request->bindValue(":admin", $admin);
   $request->bindValue(":password", $password);
   $request->execute();
   $idUser = $this->Db->Pdo->lastInsertId();
   return $idUser;
}
}