<?php

namespace App\Tables;

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Tables\Table;
use App\Database;
use App\Methods\Pdfunctions;
use PDO;
use stdClass;
use DateTime;
use App\Tables\Client;
use App\Tables\Contact;
use App\Tables\User;
use App\Tables\Stock;

class Cmd extends Table
{

  public Database $Db;

  public function __construct($db)
  {
    $this->Db = $db;
  }

  //si un jour quelqu'un se demande pourquoi y'a des allias pourris : changement de DB en plein dev .. goood luck 
  public function GetById($id)
  {
    $request = $this->Db->Pdo->query("SELECT
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__note_client as  devis__note_client ,
    cmd__note_interne as devis__note_interne,
    LPAD(cmd__client__id_livr , 6,0 ) as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    cmd__nom_devis, cmd__modele_devis , cmd__date_fact , 
    cmd__date_cmd, cmd__date_envoi, cmd__code_cmd_client, cmd__tva, cmd__user__id_cmd, LPAD(cmd__id_facture ,7,0) as cmd__id_facture ,
    cmd__modele_facture, cmd__id_facture , cmd__date_fact, cmd__trans, cmd__mode_remise, cmd__report_xtend,
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    t2.contact__nom as nom__livraison , t2.contact__prenom as prenom__livraison , t2.contact__civ as civ__Livraison , 
    t2.contact__email as mail__livraison , t2.contact__gsm as gsm__livraison , t2.contact__telephone as fixe__livraison, 
    c.client__societe, c.client__adr1 , c.client__adr2 ,  c.client__ville, c.client__cp,  c.client__tel , c.client__pays,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    c2.client__adr2 as client__livraison__adr2 , c2.client__tel as telLivraion,  c2.client__pays as client__pays_livraison,
    u.log_nec , u.user__email_devis as email , u.nom as nomDevis , u.prenom as prenomDevis , u2.nom as nomCommande , u2.prenom as prenomCommande , 
    u3.nom as nomFacture , u3.prenom as prenomFacture , 
    k3.kw__info as tva_Taux , k3.kw__value as tva_value
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN contact as t2  ON  cmd__contact__id_livr = t2.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN keyword as k3 ON cmd__tva = k3.kw__value AND k3.kw__type = 'tva'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    LEFT JOIN utilisateur as u2 ON cmd__user__id_cmd = u2.id_utilisateur
    LEFT JOIN utilisateur as u3 ON cmd__user__id_fact = u3.id_utilisateur
    WHERE cmd__id = " . $id . "");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }


  public function GetByNumFact($id)
  {
    $request = $this->Db->Pdo->query("SELECT
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__note_client as  devis__note_client ,
    cmd__note_interne as devis__note_interne,
    LPAD(cmd__client__id_livr , 6,0 ) as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    cmd__nom_devis, cmd__modele_devis , cmd__date_fact , 
    cmd__date_cmd, cmd__date_envoi, cmd__code_cmd_client, cmd__tva, cmd__user__id_cmd, LPAD(cmd__id_facture ,7,0) as cmd__id_facture ,
    cmd__modele_facture, cmd__id_facture , cmd__date_fact, cmd__trans, cmd__mode_remise, cmd__report_xtend,
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    t2.contact__nom as nom__livraison , t2.contact__prenom as prenom__livraison , t2.contact__civ as civ__Livraison , 
    t2.contact__email as mail__livraison , t2.contact__gsm as gsm__livraison , t2.contact__telephone as fixe__livraison, 
    c.client__societe, c.client__adr1 , c.client__adr2 ,  c.client__ville, c.client__cp,  c.client__tel , c.client__pays,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    c2.client__adr2 as client__livraison__adr2 , c2.client__tel as telLivraion,  c2.client__pays as client__pays_livraison,
    u.log_nec , u.user__email_devis as email , u.nom as nomDevis , u.prenom as prenomDevis , u2.nom as nomCommande , u2.prenom as prenomCommande , 
    u3.nom as nomFacture , u3.prenom as prenomFacture , 
    k3.kw__info as tva_Taux , k3.kw__value as tva_value
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN contact as t2  ON  cmd__contact__id_livr = t2.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN keyword as k3 ON cmd__tva = k3.kw__value AND k3.kw__type = 'tva'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    LEFT JOIN utilisateur as u2 ON cmd__user__id_cmd = u2.id_utilisateur
    LEFT JOIN utilisateur as u3 ON cmd__user__id_fact = u3.id_utilisateur
    WHERE cmd__id_facture = " . $id . "");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }


  public function GetByCC($cc)
  {


    $filtre = str_replace("-", ' ',$cc);
    $filtre = str_replace("'", ' ', $filtre);
    $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
    $mots_filtre = str_word_count($filtre, 1, '0123456789');
    $request_clause  ="";
    if ($nb_mots_filtre > 0) {$mode_filtre = true;} else {$mode_filtre = false;}

    if ($mode_filtre) {
      $request_clause .=  "WHERE ( cmd__code_cmd_client LIKE '%" . $mots_filtre[0] . "%'  ) ";
      for ($i = 1; $i < $nb_mots_filtre; $i++) {
        $request_clause .=  "OR ( cmd__code_cmd_client LIKE '%" . $mots_filtre[$i] . "%'  ) ";
      }
    } else {
      $request_clause .= "WHERE ( 1 = 2 ) ";
    }

    $request_clause .= "ORDER BY cmd__date_cmd LIMIT 100";

    $request = $this->Db->Pdo->query("SELECT
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__note_client as  devis__note_client ,
    cmd__note_interne as devis__note_interne,
    LPAD(cmd__client__id_livr , 6,0 ) as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    cmd__nom_devis, cmd__modele_devis , cmd__date_fact , 
    cmd__date_cmd, cmd__date_envoi, cmd__code_cmd_client, cmd__tva, cmd__user__id_cmd, LPAD(cmd__id_facture ,7,0) as cmd__id_facture ,
    cmd__modele_facture, cmd__id_facture , cmd__date_fact, cmd__trans, cmd__mode_remise, cmd__report_xtend,
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    t2.contact__nom as nom__livraison , t2.contact__prenom as prenom__livraison , t2.contact__civ as civ__Livraison , 
    t2.contact__email as mail__livraison , t2.contact__gsm as gsm__livraison , t2.contact__telephone as fixe__livraison, 
    c.client__societe, c.client__adr1 , c.client__adr2 ,  c.client__ville, c.client__cp,  c.client__tel , c.client__pays,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    c2.client__adr2 as client__livraison__adr2 , c2.client__tel as telLivraion,  c2.client__pays as client__pays_livraison,
    u.log_nec , u.user__email_devis as email , u.nom as nomDevis , u.prenom as prenomDevis , u2.nom as nomCommande , u2.prenom as prenomCommande , 
    u3.nom as nomFacture , u3.prenom as prenomFacture , 
    k3.kw__info as tva_Taux , k3.kw__value as tva_value
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN contact as t2  ON  cmd__contact__id_livr = t2.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN keyword as k3 ON cmd__tva = k3.kw__value AND k3.kw__type = 'tva'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    LEFT JOIN utilisateur as u2 ON cmd__user__id_cmd = u2.id_utilisateur
    LEFT JOIN utilisateur as u3 ON cmd__user__id_fact = u3.id_utilisateur
    ". $request_clause ."");

    
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  //si un jour quelqu'un se demande pourquoi y'a des allias pourris : changement de DB en plein dev .. goood luck 
  public function get_user_status($id_user, $status_cmd)
  {
    $request = $this->Db->Pdo->query("SELECT
    cmd__id ,
    cmd__user__id_devis, 
    cmd__date_devis,  
    LPAD(cmd__client__id_fact ,6,0)  as client__id, 
    cmd__contact__id_fact ,
    cmd__etat,
    cmd__nom_devis, cmd__modele_devis ,
    cmd__date_cmd, cmd__date_envoi, cmd__code_cmd_client, cmd__tva, cmd__user__id_cmd, LPAD(cmd__id_facture ,7,0) as cmd__id_facture ,
    cmd__modele_facture, cmd__id_facture , cmd__date_fact, cmd__trans, cmd__mode_remise,
    k.kw__lib,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,  c.client__tel , 
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    u.log_nec , u.user__email_devis as email , u.nom as nomDevis , u.prenom as prenomDevis 
    FROM cmd
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN keyword as k3 ON cmd__tva = k3.kw__value AND k3.kw__type = 'tva'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__user__id_devis = '" . $id_user . "' AND cmd__etat = '" . $status_cmd . "'");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  //fonction qui retoune les commandes liés a un client ( ordre par date de devis + récentes) limite a passer en paramètre : 
  public function get_by_client_id($id_client, $limit)
  {
    $request = $this->Db->Pdo->query("SELECT
    cmd__id , 
    cmd__user__id_devis ,
    cmd__date_devis ,
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  ,
    cmd__etat ,
    cmd__note_client  ,
    cmd__note_interne ,
    cmd__client__id_livr ,
    cmd__contact__id_livr  , 
    cmd__nom_devis, cmd__modele_devis , 
    cmd__date_cmd, cmd__date_envoi, cmd__code_cmd_client, cmd__tva, cmd__user__id_cmd, LPAD(cmd__id_facture ,7,0) as cmd__id_facture ,
    cmd__modele_facture, cmd__id_facture , cmd__date_fact, cmd__trans, cmd__mode_remise, cmd__report_xtend,
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    t2.contact__nom as nom__livraison , t2.contact__prenom as prenom__livraison , t2.contact__civ as civ__Livraison , 
    t2.contact__email as mail__livraison , t2.contact__gsm as gsm__livraison , t2.contact__telephone as fixe__livraison, 
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp, 
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    c2.client__id as cli__id_livr , c.client__id as cli__id_fact ,
    c2.client__adr2 as client__livraison__adr2 , c2.client__tel as telLivraion, 
    u.log_nec , u.user__email_devis as email , u.nom as nomDevis , u.prenom as prenomDevis , 
    k3.kw__info as tva_Taux , k3.kw__value as tva_value
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN contact as t2  ON  cmd__contact__id_livr = t2.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN keyword as k3 ON cmd__tva = k3.kw__value AND k3.kw__type = 'tva'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE  ( cmd__client__id_fact = '" . $id_client . "' OR cmd__client__id_livr = '" . $id_client . "' )     ORDER BY cmd__date_devis DESC LIMIT " . $limit . " ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  //fonction qui retoune les commandes liés a un client ( ordre par date de devis + récentes) limite a passer en paramètre : 
  public function get_by_client_id_livr($id_client, $limit)
  {
    $request = $this->Db->Pdo->query("SELECT
    cmd__id , 
    cmd__user__id_devis ,
    cmd__date_devis ,
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  ,
    cmd__etat ,
    cmd__note_client  ,
    cmd__note_interne ,
    cmd__client__id_livr ,
    cmd__contact__id_livr  , 
    cmd__nom_devis, cmd__modele_devis , 
    cmd__date_cmd, cmd__date_envoi, cmd__code_cmd_client, cmd__tva, cmd__user__id_cmd, LPAD(cmd__id_facture ,7,0) as cmd__id_facture ,
    cmd__modele_facture, cmd__id_facture , cmd__date_fact, cmd__trans, cmd__mode_remise, cmd__report_xtend,
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    t2.contact__nom as nom__livraison , t2.contact__prenom as prenom__livraison , t2.contact__civ as civ__Livraison , 
    t2.contact__email as mail__livraison , t2.contact__gsm as gsm__livraison , t2.contact__telephone as fixe__livraison, 
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp, 
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__id as cli__id_livr , c.client__id as cli__id_fact ,
    c2.client__adr1 as client__livraison__adr1 , 
    c2.client__adr2 as client__livraison__adr2 , c2.client__tel as telLivraion, 
    u.log_nec , u.user__email_devis as email , u.nom as nomDevis , u.prenom as prenomDevis , 
    k3.kw__info as tva_Taux , k3.kw__value as tva_value
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN contact as t2  ON  cmd__contact__id_livr = t2.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN keyword as k3 ON cmd__tva = k3.kw__value AND k3.kw__type = 'tva'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__client__id_livr = '" . $id_client . "' AND cmd__client__id_fact <> '".$id_client."' AND  cmd__etat <> 'PBL' ORDER BY cmd__date_devis DESC LIMIT " . $limit . " ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function getUserDevis($id)
  {
    $request = $this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_envoi,
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,  c.client__bloque ,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 ,
    cmd__nom_devis, cmd__id_facture ,
    u.log_nec
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE  cmd__user__id_devis = '" . $id . "'AND  cmd__etat = 'ATN' ORDER BY  devis__date_crea DESC , c.client__societe ASC LIMIT 200");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function getAll()
  {
    $request = $this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    cmd__nom_devis,
    u.log_nec 
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function getAllDevis()
  {
    $request = $this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp, c.client__bloque ,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    cmd__nom_devis, cmd__id_facture ,
    u.log_nec 
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__etat = 'ATN'
    ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function updateStatus($etat, $id)
  {
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd
     SET cmd__etat=? 
     WHERE cmd__id =?'
    );
    $update->execute([$etat, $id]);
  }

  public function updateComInterne($com, $id)
  {
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd
     SET cmd__note_interne=? 
     WHERE cmd__id =?'
    );
    $update->execute([$com, $id]);
  }

  public function updateComInterneLigne($com, $id)
  {
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd_ligne
     SET cmdl__note_interne=? 
     WHERE cmdl__id =?'
    );
    $update->execute([$com, $id]);
  }

  public function updateDate($column, $date,  $id)
  {
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd 
     SET ' . $column . ' = ? 
     WHERE cmd__id =?    
    '
    );
    $update->execute([$date, $id]);
  }

  public function updateAuthor($column, $author, $id)
  {
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd 
     SET ' . $column . ' = ? 
     WHERE cmd__id = ? 
    '
    );
    $update->execute([$author, $id]);
  }


  public function updateGarantieToArchive($cmdId)
  {
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd 
       SET cmd__etat = "ARH"
       WHERE cmd__id = ? 
      '
    );
    $update->execute([$cmdId]);
  }

  public function test()
  {
    $request = $this->Db->Pdo->query("SELECT * FROM cmd WHERE cmd__etat = 'VLD' ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  //met a jour la date de livraison a j+1 de la date de facturation: 
  public function updateDatePlusOne($cmdId)
  {
    $check = $this->Db->Pdo->query(
      'SELECT cmd__date_cmd + INTERVAL 1 DAY  as dateLivraison FROM cmd WHERE cmd__id = ' . $cmdId . ' '
    );

    $results = $check->fetch(PDO::FETCH_OBJ);
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd 
       SET cmd__date_envoi =?
       WHERE cmd__id = ? 
      '
    );
    $update->execute([$results->dateLivraison, $cmdId]);
  }

  //met a jour les info relative au transport ainsi que la date et l'etat ( saisie )
  public function updateTransport($trans, $poids, $paquet,  $id, $imp, $date)
  {


    $check = $this->Db->Pdo->query(
      'SELECT cmd__client__id_fact , cmd__client__id_livr FROM cmd WHERE cmd__id = ' . $id . ' 
    '
    );

    $results = $check->fetch(PDO::FETCH_OBJ);

    if (empty($results->cmd__client__id_livr)) {
      $transfert =  $this->Db->Pdo->prepare(
        'UPDATE cmd 
       SET  cmd__client__id_livr = ' . $results->cmd__client__id_fact . ' 
       WHERE cmd__id = ' . $id . '
      '
      );
      $transfert->execute();
    }

    $check2 = $this->Db->Pdo->query(
      'SELECT cmd__contact__id_fact , cmd__client__id_fact FROM cmd WHERE cmd__id = ' . $id . ' 
      '
    );

    $results2 = $check2->fetch(PDO::FETCH_OBJ);

    if (!empty($results2->cmd__contact__id_fact)) {
      $transfert2 =  $this->Db->Pdo->prepare(
        'UPDATE cmd 
         SET  cmd__contact__id_livr = ' . $results2->cmd__contact__id_fact . ' 
         WHERE cmd__id = ' . $id . '
        '
      );
      $transfert2->execute();
    }

    $data =
      [
        $trans,
        $poids,
        $paquet,
        $imp,
        $date,
        $id,

      ];
    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd
       SET cmd__trans =? , cmd__trans_kg =? , cmd__trans_info =? , cmd__etat =? , cmd__date_envoi =?  

       WHERE cmd__id = ? '
    );

    $update->execute($data);
  }

  //met a jour un champs dans une ligne / prend une collones en second parametre
  public function updateLigne($qte, $column,  $id)
  {
    $data =
      [
        $qte,
        $id,
      ];

    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd_ligne 
      SET ' . $column . ' = ?
      WHERE cmdl__id = ? '
    );

    $update->execute($data);
  }

  //met a jour les quantité et le prix (facturation)
  public function updateLigneFTC($id, $qteCMD, $qteLVR, $qteFTC, $note_facture, $prix)
  {
    $data =
      [
        $qteCMD,
        $qteLVR,
        $qteFTC,
        $prix,
        $note_facture,
        $id
      ];

    $update = $this->Db->Pdo->prepare(
      'UPDATE cmd_ligne
       SET cmdl__qte_cmd =? ,cmdl__qte_livr=? , cmdl__qte_fact=? , cmdl__puht =? , cmdl__note_facture=?
       WHERE cmdl__id=? '
    );

    $update->execute($data);
  }


  //retourne le devis lié à la ligne:
  public function returnDevis($idCmdl)
  {
    $getD = $this->Db->Pdo->query("SELECT cmdl__cmd__id FROM cmd_ligne WHERE cmdl__id = " . $idCmdl . "");
    $id = $getD->fetch(PDO::FETCH_OBJ);


    $request = $this->Db->Pdo->query("SELECT
    cmd__id as devis__id FROM cmd
    WHERE cmd__id = " . $id->cmdl__cmd__id . "");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }


  //met a jours les extensions de garantie pour la cmd passée en parametre
  public function updateGarantie($mois, $prix, $comInterne, $qte,  $id, $ordre)
  {
    $data =
      [
        $mois,
        floatval($prix),
        $comInterne,
        $qte,
        $id,
        $ordre
      ];



    $sql =
      "UPDATE cmd_ligne
    SET  
    cmdl__garantie_option  =?,
    cmdl__garantie_puht = ?,
    cmdl__note_interne = ?,
    cmdl__qte_cmd = ?
    WHERE cmdl__cmd__id  = ?  
    AND  cmdl__ordre = ?";

    $update = $this->Db->Pdo->prepare($sql);
    $update->execute($data);
  }

  public function update_garantie()
  {
  }




  //met a jour l'id client et l'id contact (facture)
  public function updateClientContact($idClient, $idContact, $id)
  {
    $data =
      [
        $idClient,
        $idContact,
        $id
      ];
    $sql =
      "UPDATE cmd
     SET 
     cmd__client__id_fact =?,
     cmd__contact__id_fact =?
     WHERE cmd__id =? ";

    $update = $this->Db->Pdo->prepare($sql);
    $update->execute($data);
  }






  //met a jour la quanbtité facturé dans une ligne de facture: 
  public function updateQuantiteFTC($cmd)
  {
    $request = $this->Db->Pdo->query("SELECT
    cmdl__id , cmdl__cmd__id, cmdl__qte_livr , cmdl__qte_fact
    FROM cmd_ligne 
    WHERE cmdl__cmd__id = " . $cmd . "");
    $arrayLigne = $request->fetchAll(PDO::FETCH_OBJ);

    foreach ($arrayLigne as $ligne) {
      if ($ligne->cmdl__qte_fact == null) {
        $data =
          [
            $ligne->cmdl__qte_livr,
            $ligne->cmdl__id
          ];

        $updateFTC =
          "UPDATE cmd_ligne
            SET cmdl__qte_fact =?
            WHERE cmdl__id =? ";

        $update = $this->Db->Pdo->prepare($updateFTC);
        $update->execute($data);
      }
    }
  }

  // si un ecart est constaté dans les quantité génère des reliquats automatiquement: 
  public function classicReliquat($cmd)
  {
    $lignes = $this->devisLigne($cmd);
	

    $NewLines = [];
    foreach ($lignes as $ligne) {

      if (intval($ligne->devl_quantite) > intval($ligne->cmdl__qte_fact)) {
        $ligne->devl_quantite = intval($ligne->devl_quantite) - intval($ligne->cmdl__qte_fact);
        array_push($NewLines, $ligne);
      }
    }


    if (!empty($NewLines)) {

      $reliquat = $this->GetById($cmd);
      $request = $this->Db->Pdo->prepare('INSERT INTO cmd ( cmd__date_cmd, cmd__date_devis, cmd__client__id_fact,
      cmd__client__id_livr, cmd__contact__id_fact,  cmd__contact__id_livr,
      cmd__note_client, cmd__note_interne, cmd__code_cmd_client,
      cmd__etat, cmd__user__id_devis, cmd__user__id_cmd , cmd__tva)
      VALUES (:cmd__date_cmd, :cmd__date_devis, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__contact__id_fact, :cmd__contact__id_livr,
      :cmd__note_client, :cmd__note_interne, :cmd__code_cmd_client, :cmd__etat, :cmd__user__id_devis, :cmd__user__id_cmd , :cmd__tva)');

      $date = date("Y-m-d H:i:s");
      $code_cmd = 'RELIQUAT n°' . $reliquat->devis__id . "  " .  $reliquat->cmd__code_cmd_client;

      $request->bindValue(":cmd__date_cmd", $date);
      $request->bindValue(":cmd__date_devis", $date);
      $request->bindValue(":cmd__client__id_fact", $reliquat->client__id);
      $request->bindValue(":cmd__client__id_livr", $reliquat->devis__id_client_livraison);
      $request->bindValue(":cmd__contact__id_fact", $reliquat->devis__contact__id);
      $request->bindValue(":cmd__contact__id_livr", $reliquat->devis__contact_livraison);
      $request->bindValue(":cmd__note_client", $reliquat->devis__note_client);
      $request->bindValue(":cmd__note_interne", $reliquat->devis__note_interne);
      $request->bindValue(":cmd__code_cmd_client", $code_cmd);
      $request->bindValue(":cmd__etat", 'CMD');
      $request->bindValue(":cmd__user__id_devis", $reliquat->devis__user__id);
      $request->bindValue(":cmd__user__id_cmd", $reliquat->cmd__user__id_cmd);
      $request->bindValue(":cmd__tva", $reliquat->cmd__tva);
      $request->execute();
      $idReliquat = $this->Db->Pdo->lastInsertId();
      $count = 0;

      foreach ($NewLines as $lines) {

        $count += 1;
        $insertObject = new stdClass;
        $insertObject->idDevis = $idReliquat;
        $insertObject->prestation = $lines->devl__type;
        $insertObject->designation = $lines->devl__designation;
        $insertObject->etat = $lines->devl__etat;
        $insertObject->garantie = $lines->devl__mois_garantie;
        $insertObject->quantite = $lines->devl_quantite;
        $insertObject->prix = $lines->devl_puht;
        $insertObject->comInt = $lines->devl__note_interne;
        $insertObject->comClient = $lines->devl__note_client;
        $insertObject->idfmm = $lines->id__fmm;
		$insertObject->pn = $lines->devl__modele;
        $insertObject->extension = $lines->cmdl__garantie_option;
        $insertObject->prixGarantie = $lines->cmdl__garantie_puht;
        $createLine = $this->insertLineReliquat($insertObject);

      }

      $command = $this->getById(intval($idReliquat));
      $commandLignes = $this->devisLigne($idReliquat);
      $Client = new Client($this->Db);
      $Contact = new Contact($this->Db);
      $User = new User($this->Db);
      $clientView = $Client->getOne($command->client__id);
      $user = $User->getByID($clientView->client__id_vendeur);
      $userCMD = $User->getByID($command->cmd__user__id_cmd);
      $Stocks = new Stock($this->Db);

      $societeLivraison = false;

      if ($command->devis__id_client_livraison) {
        $societeLivraison = $Client->getOne($command->devis__id_client_livraison);
      }
      $dateTemp = new DateTime($command->cmd__date_cmd);
      //cree une variable pour la date de commande du devis
      $date_time = new DateTime($command->cmd__date_cmd);
      //formate la date pour l'utilisateur:
      $formated_date = $date_time->format('d/m/Y');

      ob_start();
?>
      <style type="text/css">
        strong { color: #000;}
		h3 {color: #666666; }
		h2 {color: #3b3b3b;}
		 table {
			font-size: 13;
			font-style: normal;
			font-variant: normal;
			border-collapse: separate;
			border-spacing: 0 15px;
        }
      </style>
      <page backtop="10mm" backleft="5mm" backright="5mm">
        <table style="width: 100%;">
          <tr>
            <td style="text-align: left;  width: 50%"><img style=" width:60mm" src="public/img/recodeDevis.png" /></td>
            <td style="text-align: left; width:50%">
              <h3>Reparation-Location-Vente</h3>imprimantes- lecteurs codes-barres<br>
              <a>www.recode.fr</a><br><br>
              <br>
            </td>
          </tr>
          <tr>
            <td style="text-align: left;  width: 50% ; margin-left: 25%;">
              <h4>Fiche De travail - <?php echo $command->devis__id ?></h4>
              <barcode dimension="1D" type="C128" label="none" value="<?php echo $command->devis__id ?>" style="width:40mm; height:8mm; color: #3b3b3b; font-size: 4mm"></barcode><br>
              Commandé le : <strong><?php echo $formated_date ?></strong><br>
              Commercial : <strong><?php if (!empty($user)) { echo  $user->nom . ' ' . $user->prenom; } else {echo 'Non renseigné';}?>
              </strong>
              <?php if (!empty($user->postefix)) {echo ' (Tél: ' . $user->postefix . ')';} ?>
              <?php if (!empty($userCMD)) { echo  '<br>Commandé par : <strong>' . $userCMD->nom . ' ' . $userCMD->prenom . '</strong> '; }?>
			  <?php if (!empty($userCMD->postefix)) { echo ' (Tél: ' . $userCMD->postefix . ')';} ?>
            </td>
            <td style="text-align: left; width:50%"><strong><?php
					if ($societeLivraison) {
						if ($command->devis__contact__id) {
							if ($command->devis__contact_livraison) {
								$contact2 = $Contact->getOne($command->devis__contact_livraison);
								echo " <small>livraison : " . $contact2->contact__civ . " " . $contact2->contact__nom . " " . $contact2->contact__prenom . "</small><strong><br>";
								echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
								if (!empty($societeLivraison->client__tel)){
									echo '<br> TEL : ' . $societeLivraison->client__tel . '';
								}
							} else{
								echo "<small>livraison :</small><strong><br>";
								echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
								if (!empty($societeLivraison->client__tel)) {
									echo '<br> TEL : ' . $societeLivraison->client__tel . '';
								}
							}
								$contact = $Contact->getOne($command->devis__contact__id);
								echo "<br><small>facturation : " . $contact->contact__civ . " " . $contact->contact__nom . " " . $contact->contact__prenom . "</small><strong><br>";
								echo Pdfunctions::showSociete($clientView) . " </strong> ";
								if (!empty($clientView->client__tel)) {
									echo '<br> TEL : ' . $clientView->client__tel . '';
								}
								
						} 
						else{
							if ($command->devis__contact_livraison) {
								$contact2 = $Contact->getOne($command->devis__contact_livraison);
								echo " <small>livraison : " . $contact2->contact__civ . " " . $contact2->contact__nom . " " . $contact2->contact__prenom . "</small><strong><br>";
								echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
								if (!empty($societeLivraison->client__tel)){
									echo '<br> TEL : ' . $societeLivraison->client__tel . '';
								}
							} else {
								echo " <small>livraison :</small><strong><br>";
								echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
								if (!empty($societeLivraison->client__tel)){
									echo '<br> TEL : ' . $societeLivraison->client__tel . '';
								}
							}
							echo "<br><small>facturation :</small><strong><br>";
							echo Pdfunctions::showSociete($clientView) . " </strong>";
						}
					} 
					else{
						if ($command->devis__contact__id){
							$contact = $Contact->getOne($command->devis__contact__id);
							echo "<small>livraison & facturation : " . $contact->contact__civ . " " . $contact->contact__nom . " " . $contact->contact__prenom . "</small><strong><br>";
							echo Pdfunctions::showSociete($clientView)  . "</strong>";
							if (!empty($clientView->client__tel)){
								echo '<br> TEL : ' . $clientView->client__tel . '';
							}
						} 
						else{
							echo "<small>livraison & facturation : </small><strong><br>";
							echo Pdfunctions::showSociete($clientView)  . "</strong>";
							if(!empty($clientView->client__tel)) {
								echo '<br>TEL : ' . $clientView->client__tel . '';
							}
						}
					}
					if ($command->cmd__code_cmd_client) {
						echo "<br> Code cmd: " . $command->cmd__code_cmd_client;
					}
				?>
              </strong>
            </td>
          </tr>
        </table>
        <table CELLSPACING=0 style="width: 100%;  margin-top: 80px; ">
          <tr style=" margin-top : 50px; background-color: #dedede;">
            <td style="width: 21%; text-align: left;">Presta<br>Type<br>Gar.</td>
            <td style="width: 60%; text-align: left">Ref Tech<br>Désignation Client<br>Complement techniques</td>
            <td style="text-align: center; width: 7%"><strong>CMD</strong></td>
            <td style="text-align: center; width: 7%"><strong>Dispo</strong></td>
            <td style="text-align: center; width: 7%"><strong>Livré</strong></td>
          </tr>
          <?php
          foreach ($commandLignes as $item) {
            if (empty($ligne->devl__note_client)) $ligne->devl__note_client = "";
            if (empty($ligne->devl__note_interne)) $ligne->devl__note_interne = "";
            
            if ($item->cmdl__garantie_option > $item->devl__mois_garantie){
              $temp = $item->cmdl__garantie_option;
            } 
            else {
              if (!empty($item->devl__mois_garantie)) {
                $temp = $item->devl__mois_garantie;
              } 
              else{
                $temp = "";
              }
            }
            if (!empty($item->cmdl__sous_ref)){
              $background_color = 'background-color: #F1F1F1;';
            } 
            else{
              $background_color = '';
            }
            if (!empty($item->devl__modele)) {
              $spec = $Stocks->select_empty_heritage($item->devl__modele , true , false);
              $pn =  '<br>PN: '.$item->apn__pn_long . " <br>" .  $spec   ;
            }
            else {
              $pn = '';
            }
            if (!empty($item->cmdl__dp)) {
              $item->cmdl__dp  = '<br> Numéro de DP: ' .$item->cmdl__dp ;
            }else $item->cmdl__dp = '';

            echo "<tr style='font-size: 100%; " . $background_color . "'>
                  <td style='border-bottom: 1px #ccc solid'> " . $item->prestaLib . " <br> " . $item->kw__lib . " <br> " . $temp . " mois</td>
                  <td style='border-bottom: 1px #ccc solid; width: 55%;'> 
                    <br> <small>désignation :</small> <b>" . $item->devl__designation . "</b><br>"
              . $item->famille__lib . " " . $item->marque . " Modèle:" . $item->modele . "  " . $pn .  " " . $item->devl__note_interne . " ". $item->devl__note_client. $item->cmdl__dp."
              </td>
                  <td style='border-bottom: 1px #ccc solid; text-align: center'><strong> "  . $item->devl_quantite . " </strong></td>
                    <td style='border-bottom: 1px #ccc solid; border-left: 1px #ccc solid; text-align: right'><strong>  </strong></td>
                  <td style='border-bottom: 1px #ccc solid; border-left: 1px #ccc solid; text-align: right'><strong>  </strong></td>
                  </tr>";
          }
		    ?>
          
        </table>

        <table style=" margin-top: 50px; width: 100%">
          <tr style=" margin-top: 200px; width: 100%">
            <td><small>Commentaire:</small></td>
          </tr>
          <tr>
            <td style='border-bottom: 1px black solid; border-top: 1px black solid; width: 100%'> <?php echo  $command->devis__note_client ?> </td>
          </tr>
        </table>


        <div style=" width: 100%; position: absolute; bottom:1px">


          <table CELLSPACING=0 style=" width: 100%;  ">
            <tr style="background-color: #dedede;">
              <td style="text-align: center; width: 30%"><strong>Préparé par</strong></td>
              <td style="text-align: center; width: 40%"><strong>Réceptionné par : </strong></td>
              <td style="text-align: center; width: 30%"><strong>POIDS</strong></td>
            </tr>
            <tr>
              <td style="border: 1px #ccc solid; height: 150px; text-align:center;">
                <span style="margin-top: 65px; background-color: #dedede;"><strong>Controle qualité</strong></span>
              </td>
              <td style="border: 1px #ccc solid; ">
                <small><i>Nom/signature/tampon</i></small>
              </td>
              <td style="border: 1px #ccc solid; ">
              </td>
            </tr>
          </table>

        </div>

      </page>

      <?php
      $content = ob_get_contents();

      try {
        $doc = new Html2Pdf('P', 'A4', 'fr');
        $doc->setDefaultFont('gothic');
        $doc->pdf->SetDisplayMode('fullpage');
        $doc->writeHTML($content);
        ob_clean();
        if ($_SERVER['HTTP_HOST'] != "localhost:8080") {
          $doc->output('O:\intranet\Auto_Print\FT\Ft_' . $command->devis__id . '.pdf', 'F');
        }

        return $command->devis__id;
      } catch (Html2PdfException $e) {
        die($e);
      }
    }
  }



  // si un ecart est constaté dans les quantité génère des reliquats automatiquement: 
  public function FactureReliquat($cmd)
  {
    $lignes = $this->devisLigne($cmd);

    $NewLines = [];
    foreach ($lignes as $ligne) {

      if (intval($ligne->cmdl__qte_livr) < intval($ligne->cmdl__qte_fact)) {
        $ligne->devl_quantite = intval($ligne->cmdl__qte_fact) - intval($ligne->cmdl__qte_livr);
        array_push($NewLines, $ligne);
      }
    }


    if (!empty($NewLines)) {

      $reliquat = $this->GetById($cmd);
      $request = $this->Db->Pdo->prepare('INSERT INTO cmd ( cmd__date_cmd, cmd__client__id_fact,
      cmd__client__id_livr, cmd__contact__id_fact,  cmd__contact__id_livr,
      cmd__note_client, cmd__note_interne, cmd__code_cmd_client,
      cmd__etat, cmd__user__id_devis, cmd__user__id_cmd)
      VALUES (:cmd__date_cmd, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__contact__id_fact, :cmd__contact__id_livr,
      :cmd__note_client, :cmd__note_interne, :cmd__code_cmd_client, :cmd__etat, :cmd__user__id_devis, :cmd__user__id_cmd)');


      $code_cmd = 'RELIQUAT déja facturé:  n°' . $reliquat->devis__id . "  " .  $reliquat->cmd__code_cmd_client;
      $request->bindValue(":cmd__date_cmd", $reliquat->cmd__date_cmd);
      $request->bindValue(":cmd__client__id_fact", 5);
      $request->bindValue(":cmd__client__id_livr", $reliquat->devis__id_client_livraison);
      $request->bindValue(":cmd__contact__id_fact", $reliquat->devis__contact__id);
      $request->bindValue(":cmd__contact__id_livr", $reliquat->devis__contact_livraison);
      $request->bindValue(":cmd__note_client", $reliquat->devis__note_client);
      $request->bindValue(":cmd__note_interne", $reliquat->devis__note_interne);
      $request->bindValue(":cmd__code_cmd_client", $code_cmd);
      $request->bindValue(":cmd__etat", 'CMD');
      $request->bindValue(":cmd__user__id_devis", $reliquat->devis__user__id);
      $request->bindValue(":cmd__user__id_cmd", $reliquat->cmd__user__id_cmd);
      $request->execute();

      $idReliquat = $this->Db->Pdo->lastInsertId();
      $count = 0;

      
      foreach ($NewLines as $lines) {

        $count += 1;
        $insertObject = new stdClass;
        $insertObject->idDevis = $idReliquat;
        $insertObject->prestation = $lines->devl__type;
        $insertObject->designation = $lines->devl__designation;
        $insertObject->etat = $lines->devl__etat;
        $insertObject->garantie = $lines->devl__mois_garantie;
        $insertObject->quantite = $lines->devl_quantite;
        $insertObject->prix = $lines->devl_puht;
        $insertObject->comInt = $lines->devl__note_interne;
        $insertObject->comClient = $lines->devl__note_client;
        $insertObject->idfmm = $lines->id__fmm;
		$insertObject->pn = $lines->devl__modele;
        $insertObject->extension = $lines->cmdl__garantie_option;
        $insertObject->prixGarantie = $lines->cmdl__garantie_puht;
        $createLine = $this->insertLineReliquat($insertObject);

      }

      $command = $this->getById(intval($idReliquat));
      $commandLignes = $this->devisLigne($idReliquat);
      $Client = new Client($this->Db);
      $Contact = new Contact($this->Db);
	  $Stocks = new Stock($this->Db);
      $clientView = $Client->getOne($command->client__id);
      $User = new User($this->Db);
      $user = $User->getByID($clientView->client__id_vendeur);
      $userCMD = $User->getByID($command->cmd__user__id_cmd);

      $societeLivraison = false;

      if ($command->devis__id_client_livraison) {
        $societeLivraison = $Client->getOne($command->devis__id_client_livraison);
      }
      $dateTemp = new DateTime($command->cmd__date_cmd);
      //cree une variable pour la date de commande du devis
      $date_time = new DateTime($command->cmd__date_cmd);
      //formate la date pour l'utilisateur:
      $formated_date = $date_time->format('d/m/Y');
      ob_start();
      ?>
      <style type="text/css">
        strong {
          color: #000;
        }

        h3 {
          color: #666666;
        }

        h2 {
          color: #3b3b3b;
        }

        table {
          font-size: 13;
          font-style: normal;
          font-variant: normal;
          border-collapse: separate;
          border-spacing: 0 15px;
        }
      </style>

      <page backtop="10mm" backleft="5mm" backright="5mm">
        <table style="width: 100%;">
          <tr>
            <td style="text-align: left;  width: 50%"><img style=" width:60mm" src="public/img/recodeDevis.png" /></td>
            <td style="text-align: left; width:50%">
              <h3>Reparation-Location-Vente</h3>imprimantes- lecteurs codes-barres<br>
              <a>www.recode.fr</a><br><br>
              <br>
            </td>
          </tr>
          <tr>
            <td style="text-align: left;  width: 50% ; margin-left: 25%;">
              <h4>Fiche De travail - <?php echo $command->devis__id ?></h4>
              <barcode dimension="1D" type="C128" label="none" value="<?php echo $command->devis__id ?>" style="width:40mm; height:8mm; color: #3b3b3b; font-size: 4mm"></barcode><br>

              Commandé le : <strong><?php echo $formated_date ?></strong><br>
              Commercial : <strong><?php
                    if (!empty($user)) {
                            echo  $user->nom . ' ' . $user->prenom;
                    } else {
                        echo 'Non renseigné';
                    }
                ?>
              </strong>
              <?php
              if (!empty($user->postefix)) {
                echo ' (Tél: ' . $user->postefix . ')';
              }
              ?>
              <?php
              if (!empty($userCMD)) {
                echo  '<br>Commandé par : <strong>' . $userCMD->nom . ' ' . $userCMD->prenom . '</strong> ';
              }
              ?>
              <?php
              if (!empty($userCMD->postefix)) {
                echo ' (Tél: ' . $userCMD->postefix . ')';
              }
              ?>
            </td>
            <td style="text-align: left; width:50%"><strong>
				<?php
					if ($societeLivraison) {
						if ($command->devis__contact__id) {
							if ($command->devis__contact_livraison) {
								$contact2 = $Contact->getOne($command->devis__contact_livraison);
								echo " <small>livraison : " . $contact2->contact__civ . " " . $contact2->contact__nom . " " . $contact2->contact__prenom . "</small><strong><br>";
								echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
								if (!empty($societeLivraison->client__tel)){
									echo '<br> TEL : ' . $societeLivraison->client__tel . '';
								}
							} else{
								echo "<small>livraison :</small><strong><br>";
								echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
								if (!empty($societeLivraison->client__tel)) {
									echo '<br> TEL : ' . $societeLivraison->client__tel . '';
								}
							}
								$contact = $Contact->getOne($command->devis__contact__id);
								echo "<br><small>facturation : " . $contact->contact__civ . " " . $contact->contact__nom . " " . $contact->contact__prenom . "</small><strong><br>";
								echo Pdfunctions::showSociete($clientView) . " </strong> ";
								if (!empty($clientView->client__tel)) {
									echo '<br> TEL : ' . $clientView->client__tel . '';
								}
								
						} 
						else{
							if ($command->devis__contact_livraison) {
								$contact2 = $Contact->getOne($command->devis__contact_livraison);
								echo " <small>livraison : " . $contact2->contact__civ . " " . $contact2->contact__nom . " " . $contact2->contact__prenom . "</small><strong><br>";
								echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
								if (!empty($societeLivraison->client__tel)){
									echo '<br> TEL : ' . $societeLivraison->client__tel . '';
								}
							} else {
								echo " <small>livraison :</small><strong><br>";
								echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
								if (!empty($societeLivraison->client__tel)){
									echo '<br> TEL : ' . $societeLivraison->client__tel . '';
								}
							}
							echo "<br><small>facturation :</small><strong><br>";
							echo Pdfunctions::showSociete($clientView) . " </strong>";
						}
					} 
					else{
						if ($command->devis__contact__id){
							$contact = $Contact->getOne($command->devis__contact__id);
							echo "<small>livraison & facturation : " . $contact->contact__civ . " " . $contact->contact__nom . " " . $contact->contact__prenom . "</small><strong><br>";
							echo Pdfunctions::showSociete($clientView)  . "</strong>";
							if (!empty($clientView->client__tel)){
								echo '<br> TEL : ' . $clientView->client__tel . '';
							}
						} 
						else{
							echo "<small>livraison & facturation : </small><strong><br>";
							echo Pdfunctions::showSociete($clientView)  . "</strong>";
							if(!empty($clientView->client__tel)) {
								echo '<br>TEL : ' . $clientView->client__tel . '';
							}
						}
					}
					if ($command->cmd__code_cmd_client) {
						echo "<br> Code cmd: " . $command->cmd__code_cmd_client;
					}
				?>
              </strong>
            </td>
          </tr>
        </table>


        <table CELLSPACING=0 style="width: 100%;  margin-top: 80px; ">
          <tr style=" margin-top : 50px; background-color: #dedede;">
            <td style="width: 22%; text-align: left;">Presta<br>Type<br>Gar.</td>
            <td style="width: 60%; text-align: left">Ref Tech<br>Désignation Client<br>Complement techniques</td>
            <td style="text-align: center; width: 9%"><strong>CMD</strong></td>
            <td style="text-align: center; width: 9%"><strong>Livré</strong></td>
          </tr>
		  <?php
			foreach ($commandLignes as $item) {
				if (empty($ligne->devl__note_client)) $ligne->devl__note_client = "";
				if (empty($ligne->devl__note_interne)) $ligne->devl__note_interne = "";
				
				if ($item->cmdl__garantie_option > $item->devl__mois_garantie){
					$temp = $item->cmdl__garantie_option;
				} 
				else {
					if (!empty($item->devl__mois_garantie)) {
						$temp = $item->devl__mois_garantie;
					} 
					else{
						$temp = "";
					}
				}
				if (!empty($item->cmdl__sous_ref)){
					$background_color = 'background-color: #F1F1F1;';
				} 
				else{
					$background_color = '';
				}
				if (!empty($item->devl__modele)) {
					$spec = $Stocks->select_empty_heritage($item->devl__modele , true , false);
					$pn =  '<br>PN: '.$item->apn__pn_long . " <br>" .  $spec   ;
				}
				else {
					$pn = '';
				}
				if (!empty($item->cmdl__dp)) {
					$item->cmdl__dp  = '<br> Numéro de DP: ' .$item->cmdl__dp ;
				}else $item->cmdl__dp = '';

				echo "<tr style='font-size: 100%; " . $background_color . "'>
							<td style='border-bottom: 1px #ccc solid'> " . $item->prestaLib . " <br> " . $item->kw__lib . " <br> " . $temp . " mois</td>
							<td style='border-bottom: 1px #ccc solid; width: 55%;'> 
								<br> <small>désignation :</small> <b>" . $item->devl__designation . "</b><br>"
					. $item->famille__lib . " " . $item->marque . " Modèle:" . $item->modele . "  " . $pn .  " " . $item->devl__note_interne . " ". $item->devl__note_client. $item->cmdl__dp."
					</td>
							<td style='border-bottom: 1px #ccc solid; text-align: center'><strong> "  . $item->devl_quantite . " </strong></td>
							<td style='border-bottom: 1px #ccc solid; border-left: 1px #ccc solid; text-align: right'><strong>  </strong></td>
							<td style='border-bottom: 1px #ccc solid; border-left: 1px #ccc solid; text-align: right'><strong>  </strong></td>
						</tr>";
			}
		?>
        </table>

        <table style=" margin-top: 50px; width: 100%">
          <tr style=" margin-top: 200px; width: 100%">
            <td><small>Commentaire:</small></td>
          </tr>
          <tr>
            <td style='border-bottom: 1px black solid; border-top: 1px black solid; width: 100%'> <?php echo  $command->devis__note_interne ?> </td>
          </tr>
        </table>


        <div style=" width: 100%; position: absolute; bottom:1px">


          <table CELLSPACING=0 style=" width: 100%;  ">
            <tr style="background-color: #dedede;">
              <td style="text-align: center; width: 30%"><strong>Traitement en atelier </strong></td>
              <td style="text-align: center; width: 40%"><strong>Réceptionné par : </strong></td>
              <td style="text-align: center; width: 30%"><strong>POIDS</strong></td>
            </tr>
            <tr>
              <td style="border: 1px #ccc solid; height: 150px;">

              </td>
              <td style="border: 1px #ccc solid; ">
                <small><i>Nom/signature/tampon</i></small>
              </td>
              <td style="border: 1px #ccc solid; ">

              </td>
            </tr>
          </table>

        </div>

      </page>

<?php
      $content = ob_get_contents();

      try {
        $doc = new Html2Pdf('P', 'A4', 'fr');
        $doc->setDefaultFont('gothic');
        $doc->pdf->SetDisplayMode('fullpage');
        $doc->writeHTML($content);
        ob_clean();
        if ($_SERVER['HTTP_HOST'] != "localhost:8080") {
          $doc->output('O:\intranet\Auto_Print\FT\Ft_' . $command->devis__id . '.pdf', 'F');
        }
      } catch (Html2PdfException $e) {
        die($e);
      }
    }
  }



  // creer un nouvel avoir: 
  public function makeAvoir($facture)
  {

    $request = $this->Db->Pdo->prepare('INSERT INTO cmd ( cmd__date_cmd, cmd__client__id_fact,
  cmd__client__id_livr, cmd__contact__id_fact,  cmd__contact__id_livr,
  cmd__note_client, cmd__note_interne, cmd__code_cmd_client,
  cmd__etat, cmd__user__id_devis, cmd__user__id_cmd)
  VALUES (:cmd__date_cmd, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__contact__id_fact, :cmd__contact__id_livr,
  :cmd__note_client, :cmd__note_interne, :cmd__code_cmd_client, :cmd__etat, :cmd__user__id_devis, :cmd__user__id_cmd)');

    $avoirId =  'Avoir Facture N°: ' . $facture->cmd__id_facture . ' ' . $facture->cmd__code_cmd_client;


    $request->bindValue(":cmd__date_cmd", $facture->cmd__date_cmd);
    $request->bindValue(":cmd__client__id_fact", $facture->client__id);
    $request->bindValue(":cmd__client__id_livr", $facture->devis__id_client_livraison);
    $request->bindValue(":cmd__contact__id_fact", $facture->devis__contact__id);
    $request->bindValue(":cmd__contact__id_livr", $facture->devis__contact_livraison);
    $request->bindValue(":cmd__note_client", $facture->devis__note_client);
    $request->bindValue(":cmd__note_interne", $facture->devis__note_interne);
    $request->bindValue(":cmd__code_cmd_client",  $avoirId);
    $request->bindValue(":cmd__etat", 'CMD');
    $request->bindValue(":cmd__user__id_devis", $facture->devis__user__id);
    $request->bindValue(":cmd__user__id_cmd", $facture->cmd__user__id_cmd);
    $request->execute();

    $idfacture = $this->Db->Pdo->lastInsertId();

    return $idfacture;
  }

  public function duplicate_devis($devis)
  {

    $request = $this->Db->Pdo->prepare('INSERT INTO cmd ( cmd__date_devis, cmd__client__id_fact,
  cmd__client__id_livr, cmd__contact__id_fact,  cmd__contact__id_livr,
  cmd__note_client, cmd__note_interne, cmd__code_cmd_client, cmd__tva ,
  cmd__etat, cmd__user__id_devis)
  VALUES (:cmd__date_devis, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__contact__id_fact, :cmd__contact__id_livr,
  :cmd__note_client, :cmd__note_interne, :cmd__code_cmd_client, :cmd__tva, :cmd__etat, :cmd__user__id_devis)');

    $request->bindValue(":cmd__date_devis", $devis->devis__date_crea);
    $request->bindValue(":cmd__client__id_fact", $devis->client__id);
    $request->bindValue(":cmd__client__id_livr", $devis->devis__id_client_livraison);
    $request->bindValue(":cmd__contact__id_fact", $devis->devis__contact__id);
    $request->bindValue(":cmd__contact__id_livr", $devis->devis__contact_livraison);
    $request->bindValue(":cmd__note_client", $devis->devis__note_client);
    $request->bindValue(":cmd__note_interne", $devis->devis__note_interne);
    $request->bindValue(":cmd__code_cmd_client",  $devis->cmd__code_cmd_client);
    $request->bindValue(":cmd__tva", $devis->cmd__tva);
    $request->bindValue(":cmd__etat", 'ATN');
    $request->bindValue(":cmd__user__id_devis", $devis->devis__user__id);
    $request->execute();

    $id_new_devis = $this->Db->Pdo->lastInsertId();

    return $id_new_devis;
  }

  public function duplicate_extension_garantie($tableau_extension, $ligne__id)
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO cmd_garantie ( cmdg__id__cmdl , cmdg__type ,
   cmdg__prix , cmdg__prix_barre , cmdg__ordre )
  VALUES (:cmdg__id__cmdl , :cmdg__type ,
   :cmdg__prix , :cmdg__prix_barre , :cmdg__ordre )');

    $verifOrdre = $this->Db->Pdo->query(
      'SELECT MAX(cmdg__ordre) as maxOrdre from cmd_garantie WHERE cmdg__id__cmdl = ' . $ligne__id . ' '
    );

    $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
    $ordreMax = $ordreMax->maxOrdre + 1;

    $request->bindValue(":cmdg__id__cmdl", $ligne__id);
    $request->bindValue(":cmdg__type", intval($tableau_extension['devg__type']));
    $request->bindValue(":cmdg__prix", $tableau_extension['devg__prix']);
    $request->bindValue(":cmdg__prix_barre", $tableau_extension['cmdg__prix_barre']);
    $request->bindValue(":cmdg__ordre", $ordreMax);
    $request->execute();

    $id_extension = $this->Db->Pdo->lastInsertId();

    return $id_extension;
  }

  // creer un nouvel avoir: 2 eme param = garantie ou retour , 3eme = client : echange reliquat et co (id) , id du tech qui edite la fiche :
  public function makeRetour($facture, $type, $client, $user)
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO cmd ( cmd__date_cmd, cmd__client__id_fact,
  cmd__client__id_livr, cmd__contact__id_fact,  cmd__contact__id_livr,
  cmd__note_client, cmd__note_interne, cmd__code_cmd_client,
  cmd__etat, cmd__user__id_devis, cmd__user__id_cmd)
  VALUES (:cmd__date_cmd, :cmd__client__id_fact, :cmd__client__id_livr, :cmd__contact__id_fact, :cmd__contact__id_livr,
  :cmd__note_client, :cmd__note_interne, :cmd__code_cmd_client, :cmd__etat, :cmd__user__id_devis, :cmd__user__id_cmd)');

    $avoirId =  $type . ' commande :  ' . $facture->devis__id;


    $request->bindValue(":cmd__date_cmd", $facture->cmd__date_cmd);
    $request->bindValue(":cmd__client__id_fact", $client);
    $request->bindValue(":cmd__client__id_livr", $facture->devis__id_client_livraison);
    $request->bindValue(":cmd__contact__id_fact", null);
    $request->bindValue(":cmd__contact__id_livr", $facture->devis__contact_livraison);
    $request->bindValue(":cmd__note_client", $facture->devis__note_client);
    $request->bindValue(":cmd__note_interne", $facture->devis__note_interne);
    $request->bindValue(":cmd__code_cmd_client",  $avoirId);
    $request->bindValue(":cmd__etat", 'CMD');
    $request->bindValue(":cmd__user__id_devis", $facture->devis__user__id);
    $request->bindValue(":cmd__user__id_cmd", $user);
    $request->execute();
    $idfacture = $this->Db->Pdo->lastInsertId();
    return $idfacture;
  }

  //insère une ligne dans un devis :
  public function insertLine($object)
  {
    $requestLigne =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_ligne (
     cmdl__cmd__id, cmdl__prestation,  cmdl__designation ,
     cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  ,  
     cmdl__puht , cmdl__note_client  ,  cmdl__ordre , cmdl__id__fmm , cmdl__garantie_option , cmdl__garantie_puht , cmdl__qte_livr)
     VALUES (
     :devl__devis__id, :devl__type,  :devl__designation,
     :devl__etat, :devl__mois_garantie , :devl_quantite,  
     :devl_puht , :devl__note_client ,  :devl__ordre , :id__fmm , :cmdl__garantie_option , :cmdl__garantie_puht , :cmdl__qte_livr)'
    );


    $verifOrdre = $this->Db->Pdo->query(
      'SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne WHERE cmdl__cmd__id = ' . $object->idDevis . ' '
    );

    $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
    $ordreMax = $ordreMax->maxOrdre + 1;
    $requestLigne->bindValue(":devl__devis__id", $object->idDevis);
    $requestLigne->bindValue(":devl__type", $object->prestation);
    $requestLigne->bindValue(":devl__designation", $object->designation);
    $requestLigne->bindValue(":devl__etat", $object->etat);
    $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
    $requestLigne->bindValue(":devl_quantite", $object->quantite);
    $requestLigne->bindValue(":devl_puht", floatval($object->prix));
    $requestLigne->bindValue(":devl__note_client", $object->comClient);
    $requestLigne->bindValue(":devl__ordre", $ordreMax);
    $requestLigne->bindValue(":id__fmm", $object->idfmm);
    $requestLigne->bindValue(":cmdl__garantie_option", $object->extension);
    $requestLigne->bindValue(":cmdl__garantie_puht", floatVal($object->prixGarantie));
    $requestLigne->bindValue(":cmdl__qte_livr", intval($object->quantite));
    $requestLigne->execute();
    return $requestLigne;
  }


  public function insertLine_fiche($object)
  {
    $requestLigne =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_ligne (
     cmdl__cmd__id, cmdl__prestation,  cmdl__designation ,
     cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  ,  
     cmdl__puht , cmdl__note_client  ,  cmdl__ordre , cmdl__id__fmm , cmdl__garantie_option , cmdl__garantie_puht , cmdl__qte_livr, cmdl__pn )
     VALUES (
     :devl__devis__id, :devl__type,  :devl__designation,
     :devl__etat, :devl__mois_garantie , :devl_quantite,  
     :devl_puht , :devl__note_client ,  :devl__ordre , :id__fmm , :cmdl__garantie_option , :cmdl__garantie_puht , :cmdl__qte_livr, :cmdl__pn )'
    );
    $verifOrdre = $this->Db->Pdo->query('SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne WHERE cmdl__cmd__id = ' . $object->idDevis . ' ');
    $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
    $ordreMax = $ordreMax->maxOrdre + 1;
    $requestLigne->bindValue(":devl__devis__id", $object->idDevis);
    $requestLigne->bindValue(":devl__type", $object->prestation);
    $requestLigne->bindValue(":devl__designation", $object->designation);
    $requestLigne->bindValue(":devl__etat", $object->etat);
    $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
    $requestLigne->bindValue(":devl_quantite", $object->quantite);
    $requestLigne->bindValue(":devl_puht", floatval($object->prix));
    $requestLigne->bindValue(":devl__note_client", $object->comClient);
    $requestLigne->bindValue(":devl__ordre", $ordreMax);
    $requestLigne->bindValue(":id__fmm", $object->idfmm);
    $requestLigne->bindValue(":cmdl__garantie_option", $object->extension);
    $requestLigne->bindValue(":cmdl__garantie_puht", floatVal($object->prixGarantie));
    $requestLigne->bindValue(":cmdl__qte_livr", intval($object->quantite));
    $requestLigne->bindValue(":cmdl__pn", intval($object->pn));
    $requestLigne->execute();
    return $requestLigne;
  }

  //insère une ligne dans un devis :
  public function insertLineDevis($object)
  {
    $requestLigne =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_ligne (
     cmdl__cmd__id, cmdl__prestation,  cmdl__designation ,
     cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  ,  
     cmdl__puht , cmdl__note_client  ,  cmdl__ordre , cmdl__id__fmm ,  cmdl__qte_livr)
     VALUES (
     :devl__devis__id, :devl__type,  :devl__designation,
     :devl__etat, :devl__mois_garantie , :devl_quantite,  
     :devl_puht , :devl__note_client ,  :devl__ordre , :id__fmm , :cmdl__qte_livr)'
    );
    $verifOrdre = $this->Db->Pdo->query(
      'SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne WHERE cmdl__cmd__id = ' . $object->idDevis . ' '
    );

    $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
    $ordreMax = $ordreMax->maxOrdre + 1;
    $requestLigne->bindValue(":devl__devis__id", $object->idDevis);
    $requestLigne->bindValue(":devl__type", $object->prestation);
    $requestLigne->bindValue(":devl__designation", $object->designation);
    $requestLigne->bindValue(":devl__etat", $object->etat);
    $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
    $requestLigne->bindValue(":devl_quantite", $object->quantite);
    $requestLigne->bindValue(":devl_puht", floatval($object->prix));
    $requestLigne->bindValue(":devl__note_client", $object->comClient);
    $requestLigne->bindValue(":devl__ordre", $ordreMax);
    $requestLigne->bindValue(":id__fmm", $object->idfmm);
    $requestLigne->bindValue(":cmdl__qte_livr", intval($object->quantite));
    $requestLigne->execute();
    return $requestLigne;
  }

  //insère une ligne dans un devis :
  public function insertLineReliquat($object)
  {
    $requestLigne =  $this->Db->Pdo->prepare(
    'INSERT INTO  cmd_ligne (
     cmdl__cmd__id, cmdl__prestation,  cmdl__designation ,
     cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  ,  
     cmdl__puht , cmdl__note_client  ,  cmdl__ordre , cmdl__id__fmm , cmdl__garantie_option , cmdl__garantie_puht , cmdl__qte_livr , cmdl__note_interne , cmdl__pn)
     VALUES (
     :devl__devis__id, :devl__type,  :devl__designation,
     :devl__etat, :devl__mois_garantie , :devl_quantite,  
     :devl_puht , :devl__note_client ,  :devl__ordre , :id__fmm , :cmdl__garantie_option , :cmdl__garantie_puht , :cmdl__qte_livr , :cmdl__note_interne , :cmdl__pn)'
    );
    $verifOrdre = $this->Db->Pdo->query(
      'SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne WHERE cmdl__cmd__id = ' . $object->idDevis . ' '
    );
      $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
      $ordreMax = $ordreMax->maxOrdre + 1;
      $requestLigne->bindValue(":devl__devis__id", $object->idDevis);
      $requestLigne->bindValue(":devl__type", $object->prestation);
      $requestLigne->bindValue(":devl__designation", $object->designation);
      $requestLigne->bindValue(":devl__etat", $object->etat);
      $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
      $requestLigne->bindValue(":devl_quantite", $object->quantite);
      $requestLigne->bindValue(":devl_puht", floatval($object->prix));
      $requestLigne->bindValue(":devl__note_client", $object->comClient);
      $requestLigne->bindValue(":cmdl__note_interne", $object->comInt);
      $requestLigne->bindValue(":devl__ordre", $ordreMax);
      $requestLigne->bindValue(":id__fmm", $object->idfmm);
      $requestLigne->bindValue(":cmdl__garantie_option", $object->extension);
      $requestLigne->bindValue(":cmdl__garantie_puht", floatVal($object->prixGarantie));
      $requestLigne->bindValue(":cmdl__qte_livr", null);
	  $requestLigne->bindValue(":cmdl__pn",$object->pn);
      $requestLigne->execute();
      return $requestLigne;
  }

  //insère une ligne dans un devis :
  public function insert_ligne_duplicata($cmdId, $object)
  {
    $requestLigne =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_ligne (
     cmdl__cmd__id, cmdl__prestation,  cmdl__designation ,
     cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  ,  
     cmdl__puht , cmdl__note_client  ,  cmdl__ordre , cmdl__id__fmm , cmdl__note_interne , cmdl__etat_masque , cmdl__image , cmdl__actif , cmdl__sous_ref , cmdl__pn   )
     VALUES (
     :devl__devis__id, :devl__type,  :devl__designation,
     :devl__etat, :devl__mois_garantie , :devl_quantite,  
     :devl_puht , :devl__note_client ,  :devl__ordre , :id__fmm ,  :cmdl__note_interne , :cmdl__etat_masque , :cmdl__image , :cmdl__actif , :cmdl__sous_ref , :cmdl__pn  )'
    );
    $verifOrdre = $this->Db->Pdo->query(
      'SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne WHERE cmdl__cmd__id = ' . $cmdId . ' '
    );
      $ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
      $ordreMax = $ordreMax->maxOrdre + 1;
      $requestLigne->bindValue(":devl__devis__id", $cmdId);
      $requestLigne->bindValue(":devl__type", $object->devl__type);
      $requestLigne->bindValue(":devl__designation", $object->devl__designation);
      $requestLigne->bindValue(":devl__etat", $object->devl__etat);
      $requestLigne->bindValue(":devl__mois_garantie", intval($object->devl__mois_garantie));
      $requestLigne->bindValue(":devl_quantite", $object->devl_quantite);
      $requestLigne->bindValue(":devl_puht", floatval($object->devl_puht));
      $requestLigne->bindValue(":devl__note_client", $object->devl__note_client);
      $requestLigne->bindValue(":devl__ordre", $ordreMax);
      $requestLigne->bindValue(":id__fmm", $object->id__fmm);
      $requestLigne->bindValue(":cmdl__note_interne", $object->devl__note_interne);
      $requestLigne->bindValue(":cmdl__etat_masque", $object->cmdl__etat_masque);
      $requestLigne->bindValue(":cmdl__image", $object->cmdl__image);
      $requestLigne->bindValue(":cmdl__actif", $object->cmdl__actif);
      $requestLigne->bindValue(":cmdl__sous_ref", $object->cmdl__sous_ref);
      $requestLigne->bindValue(":cmdl__pn", $object->devl__modele);
      $requestLigne->execute();
      $id_ligne = $this->Db->Pdo->lastInsertId();

      if(!empty($object->sous_ref)){
        foreach ($object->sous_ref as $sous_ref) {
          $requestLigne =  $this->Db->Pdo->prepare(
            	'INSERT INTO  cmd_ligne (
				cmdl__cmd__id, cmdl__prestation,  cmdl__designation ,
				cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  ,  
				cmdl__puht , cmdl__note_client  ,  cmdl__ordre , cmdl__id__fmm , cmdl__note_interne , cmdl__etat_masque , cmdl__image , cmdl__actif , cmdl__sous_ref , cmdl__pn   )
				VALUES (
				:devl__devis__id, :devl__type,  :devl__designation,
				:devl__etat, :devl__mois_garantie , :devl_quantite,  
				:devl_puht , :devl__note_client ,  :devl__ordre , :id__fmm ,  :cmdl__note_interne , :cmdl__etat_masque , :cmdl__image , :cmdl__actif , :cmdl__sous_ref , :cmdl__pn  )'
          	);
          	$verifOrdre = $this->Db->Pdo->query(
            	'SELECT MAX(cmdl__ordre) as maxOrdre from cmd_ligne WHERE cmdl__cmd__id = ' . $cmdId . ' '
          	);
			$ordreMax = $verifOrdre->fetch(PDO::FETCH_OBJ);
			$ordreMax = $ordreMax->maxOrdre + 1;
			$requestLigne->bindValue(":devl__devis__id", $cmdId);
			$requestLigne->bindValue(":devl__type", $sous_ref->devl__type);
			$requestLigne->bindValue(":devl__designation",$sous_ref->devl__designation);
			$requestLigne->bindValue(":devl__etat", $sous_ref->devl__etat);
			$requestLigne->bindValue(":devl__mois_garantie", intval($sous_ref->devl__mois_garantie));
			$requestLigne->bindValue(":devl_quantite", $sous_ref->devl_quantite);
			$requestLigne->bindValue(":devl_puht", floatval($sous_ref->devl_puht));
			$requestLigne->bindValue(":devl__note_client", $sous_ref->devl__note_client);
			$requestLigne->bindValue(":devl__ordre", $ordreMax);
			$requestLigne->bindValue(":id__fmm", $sous_ref->id__fmm);
			$requestLigne->bindValue(":cmdl__note_interne", $sous_ref->devl__note_interne);
			$requestLigne->bindValue(":cmdl__etat_masque",$sous_ref->cmdl__etat_masque);
			$requestLigne->bindValue(":cmdl__image", $sous_ref->cmdl__image);
			$requestLigne->bindValue(":cmdl__actif", $sous_ref->cmdl__actif);
			$requestLigne->bindValue(":cmdl__sous_ref", $id_ligne);
			$requestLigne->bindValue(":cmdl__pn", $sous_ref->devl__modele);
			$requestLigne->execute();
        }
      }
      return $id_ligne;
  }

  
  //recupère les lignes liées à un devis:
  public function devisLigne($id)
  {
    $request = $this->Db->Pdo->query("SELECT
  cmdl__cmd__id,
  cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
  cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
  cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
  cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
  cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
  cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
  cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
  cmdl__etat_masque, cmdl__image, cmdl__actif , cmdl__sous_ref, cmdl__dp ,
  k.kw__lib , k.kw__value , 
  f.afmm__famille as famille,
  f.afmm__modele as modele, f.afmm__image as ligne_image , 
  k2.kw__lib as prestaLib,
  k3.kw__info as groupe_famille,
  k3.kw__lib as famille__lib,
  a.am__marque as marque ,
  p.apn__pn_long, p.apn__image 
  FROM cmd_ligne 
  LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
  LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
  LEFT JOIN art_pn as p ON cmdl__pn = p.apn__pn
  LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
  LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
  LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
  WHERE cmdl__cmd__id = " . $id . "
  ORDER BY devl__ordre ");

    $data = $request->fetchAll(PDO::FETCH_OBJ);
    foreach ($data as $ligne) 
    {
        if (!empty($ligne->ligne_image)) 
          $ligne->ligne_image = base64_encode($ligne->ligne_image);

        if (!empty($ligne->apn__image)) 
          $ligne->apn__image = base64_encode($ligne->apn__image);
    }
    return $data;
  }

  //recupere les ligne et leur attribue leur filles : 
  public function devisLigne_sous_ref($id)
  {
    $request = $this->Db->Pdo->query("SELECT
    cmdl__cmd__id,
    cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
    cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
    cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
    cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
    cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
    cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
    cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
    cmdl__etat_masque, cmdl__image, cmdl__actif ,cmdl__sous_ref , cmdl__sous_garantie,  cmdl__dp ,
    k.kw__lib , k.kw__value , 
    f.afmm__famille as famille,
    f.afmm__modele as modele, f.afmm__image as ligne_image , 
    k2.kw__lib as prestaLib,
    k3.kw__info as groupe_famille,
    p.apn__pn_long, p.apn__image, p.apn__desc_short ,
    k3.kw__lib as famille__lib,
    a.am__marque as marque
    FROM cmd_ligne 
    LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
    LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
    LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
    LEFT JOIN art_pn as p ON cmdl__pn = p.apn__pn
    LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
    LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
    WHERE cmdl__cmd__id = " . $id . " 
    ORDER BY devl__ordre ");

    $data = $request->fetchAll(PDO::FETCH_OBJ);
    $array_filles = [];

    foreach ($data as $k => $ligne) {
      $ligne->sous_ref = [];
      //encode image en base 64 
      if (!empty($ligne->ligne_image)) {
        $ligne->ligne_image = base64_encode($ligne->ligne_image);
      }
      //reporte les lignes filles dans un tableau a part : 
      if (!empty($ligne->cmdl__sous_ref)) {
        array_push($array_filles, $ligne);
        unset($data[$k]);
      }
    }
    foreach ($array_filles as $filles) {

      foreach ($data as $mere) {

        if ($filles->cmdl__sous_ref == $mere->devl__id) {
          array_push($mere->sous_ref, $filles);
          // var_dump($filles->cmdl__sous_ref ,$mere->devl__id );
        }
      }
    }

    return $data;
  }
  //recupere les ligne et leur attribue leur filles : 
  public function devisLigne_sous_ref_actif($id)
  {
    $request = $this->Db->Pdo->query("SELECT
  cmdl__cmd__id,
  cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
  cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
  cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
  cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
  cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
  cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
  cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
  cmdl__etat_masque, cmdl__image, cmdl__actif ,cmdl__sous_ref , cmdl__sous_garantie , 
  k.kw__lib , k.kw__value , 
  f.afmm__famille as famille,
  f.afmm__modele as modele, f.afmm__image as ligne_image , 
  k2.kw__lib as prestaLib,
  k3.kw__info as groupe_famille,
  k3.kw__lib as famille__lib,
  a.am__marque as marque,
  p.apn__pn_long, p.apn__image , p.apn__desc_short
  FROM cmd_ligne 
  LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
  LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
  LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
  LEFT JOIN art_pn as p ON cmdl__pn = p.apn__pn
  LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
  LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
  WHERE cmdl__cmd__id = " . $id . " AND cmdl__actif > 0
  ORDER BY devl__ordre ");

    $data = $request->fetchAll(PDO::FETCH_OBJ);
    $array_filles = [];
    foreach ($data as $k => $ligne) {
      $ligne->sous_ref = [];
      //encode image en base 64 
      if (!empty($ligne->ligne_image)) {
        $ligne->ligne_image = base64_encode($ligne->ligne_image);
      }
      //reporte les lignes filles dans un tableau a part : 
      if (!empty($ligne->cmdl__sous_ref)) {
        array_push($array_filles, $ligne);
        unset($data[$k]);
      }
    }

    foreach ($array_filles as $filles) {

      foreach ($data as $mere) {

        if ($filles->cmdl__sous_ref == $mere->devl__id) {
          array_push($mere->sous_ref, $filles);
          // var_dump($filles->cmdl__sous_ref ,$mere->devl__id );
        }
      }
    }

    return $data;
  }


  //recupère les lignes liées à un devis:
  public function devisLigne_actif($id)
  {

    $request = $this->Db->Pdo->query("SELECT
    cmdl__cmd__id,
    cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
    cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
    cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
    cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
    cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
    cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
    cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
    cmdl__etat_masque, cmdl__image, cmdl__actif ,
    k.kw__lib , k.kw__value , 
    f.afmm__famille as famille,
    f.afmm__modele as modele, f.afmm__image as ligne_image , 
    k2.kw__lib as prestaLib,
    k3.kw__info as groupe_famille,
    k3.kw__lib as famille__lib,
    a.am__marque as marque, 
    p.apn__pn_long, p.apn__image ,p.apn__desc_short
    FROM cmd_ligne 
    LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
    LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
    LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
    LEFT JOIN art_pn as p ON cmdl__pn = p.apn__pn
    LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
    LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
    WHERE cmdl__cmd__id = " . $id . " AND cmdl__actif > 0 AND cmdl__sous_ref IS NULL
    ORDER BY devl__ordre ");

    $data = $request->fetchAll(PDO::FETCH_OBJ);
    foreach ($data as $ligne) 
    {
      
      if (!empty($ligne->cmdl__image)) 
      {
        if (!empty($ligne->apn__image)) 
        {
            $ligne->ligne_image = base64_encode($ligne->apn__image);
        }
        else
        {
            $ligne->ligne_image = base64_encode($ligne->ligne_image);
        }
       
      }
    }

    return $data;
  }

  //supprime une ligne innactive 
  public function delete_ligne_inactif($id)
  {
    $request = "DELETE FROM cmd_ligne WHERE  cmdl__cmd__id = '" . $id . "' AND  cmdl__actif < 1 ";
    $update = $this->Db->Pdo->prepare($request);
    $update->execute();
    return true;
  }

  //efface les ligne filles d'une ligne inactive
  public function delete_ligne_inactif_filles($id)
  {
    $request = $this->Db->Pdo->query("SELECT cmdl__id , cmdl__cmd__id  FROM cmd_ligne 
  WHERE cmdl__cmd__id = " . $id . " AND  cmdl__actif < 1 
  ORDER BY cmdl__id ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    foreach ($data as $ligne) {
      $request = "DELETE FROM cmd_ligne WHERE cmdl__cmd__id = " . $id . "  AND cmdl__sous_ref = '" . $ligne->cmdl__id . "'";
      $update = $this->Db->Pdo->prepare($request);
      $update->execute();
    }
    return true;
  }
  //met à jour les extension de garantie des filles : 
  public function update_filles_extensions($mere)
  {

    $data = [$mere->cmdl__garantie_option,  $mere->cmdl__cmd__id, $mere->devl__id];
    $request = "UPDATE cmd_ligne SET cmdl__garantie_option = ? , cmdl__garantie_puht = 00.0   WHERE cmdl__cmd__id = ?  AND cmdl__sous_ref = ? AND cmdl__sous_garantie = 1";
    $update = $this->Db->Pdo->prepare($request);
    $update->execute($data);
    return true;
  }




  //recupère les lignes liées à un devis:
  public function devisLigneFacturee($id)
  {
    $request = $this->Db->Pdo->query("SELECT
  cmdl__cmd__id,
  cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
  cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
  cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
  cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
  cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
  cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
  cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
  k.kw__lib , k.kw__value , 
  f.afmm__famille as famille,
  f.afmm__modele as modele,
  k2.kw__lib as prestaLib,
  k3.kw__info as groupe_famille,
  k3.kw__lib as famille__lib,
  a.am__marque as marque
  FROM cmd_ligne 
  LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
  LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
  LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
  LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
  LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
  WHERE cmdl__cmd__id = " . $id . " AND cmdl__qte_fact > 0 
  ORDER BY devl__ordre ");

    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  //recupere le numero de compte du plan comptable pour chaque ligne passée en parametre:
  public function getCompta($ligne, $cmd)
  {

   
    $arrayResponse = [];
    $request = $this->Db->Pdo->query("SELECT * FROM compta
    WHERE cpt__tva_kw = " . $cmd->tva_value . " AND cpt__pres_kw = '" . $ligne->devl__type . "' AND  cpt__letat_kw = '".$ligne->devl__etat."' ");

    $data = $request->fetch(PDO::FETCH_OBJ);

    array_push($arrayResponse, $data);

    if ($ligne->cmdl__garantie_puht != null && intval($ligne->cmdl__garantie_puht) != 0) {
      $request = $this->Db->Pdo->query("SELECT * FROM compta
      WHERE cpt__tva_kw = " . $cmd->tva_value . " AND cpt__pres_kw = 'EXG' ");
      $data = $request->fetch(PDO::FETCH_OBJ);
      array_push($arrayResponse, $data);
    }
    return $arrayResponse;
  }


  //recupère les lignes liées à un devis id_ligne:
  public function devisLigneId($id)
  {
    $request = $this->Db->Pdo->query("SELECT
  cmdl__cmd__id,
  cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
  cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
  cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
  cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
  cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
  cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
  cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__garantie_puht , cmdl__note_facture,
  k.kw__lib , k.kw__value , 
  f.afmm__famille as famille,
  f.afmm__modele as modele,
  k2.kw__lib as prestaLib,
  k3.kw__info as groupe_famille,
  k3.kw__lib as famille__lib,
  a.am__marque as marque
  FROM cmd_ligne 
  LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
  LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
  LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
  LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
  LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
  WHERE cmdl__id = " . $id . "
  ORDER BY devl__ordre ");

    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }

  //attribut les lignes avoirées
  public function makeAvoirLigne($id, $avoirId, $qte)
  {
    $requestLigne =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_ligne (
     cmdl__cmd__id, cmdl__prestation, cmdl__pn, cmdl__designation,
     cmdl__etat, cmdl__garantie_base, cmdl__qte_cmd,
     cmdl__puht, cmdl__note_client, cmdl__note_interne, cmdl__ordre, cmdl__id__fmm,
     cmdl__qte_fact, cmdl__prix_barre, cmdl__note_facture, cmdl__garantie_option, cmdl__garantie_puht)
     SELECT cmdl__cmd__id, cmdl__prestation, cmdl__pn, cmdl__designation,
     cmdl__etat, cmdl__garantie_base, cmdl__qte_cmd, 
     cmdl__puht, cmdl__note_client, cmdl__note_interne, cmdl__ordre, cmdl__id__fmm,
     cmdl__qte_fact, cmdl__prix_barre, cmdl__note_facture, cmdl__garantie_option, cmdl__garantie_puht
     FROM cmd_ligne
     WHERE cmdl__id = ' . $id . ''
    );
    $requestLigne->execute();
    $idLigne = $this->Db->Pdo->lastInsertId();

    $data =
      [
        $avoirId,
        intval($qte),
        $idLigne
      ];

    $updateNewLines =
      "UPDATE cmd_ligne
         SET cmdl__cmd__id =? , cmdl__qte_fact = ? 
         WHERE cmdl__id =? ";

    $update = $this->Db->Pdo->prepare($updateNewLines);
    $update->execute($data);
    return $idLigne;
  }



  //inverse les prix pour chaque ligne: 
  public function reversePrice($idLigne)
  {
    $ligne = $this->devisLigneId($idLigne);

    if (!empty($ligne->cmdl__garantie_puht)) {
      $reverse = $ligne->cmdl__garantie_puht * -1;
      $data = [$reverse, $idLigne];
      $updateNewLines =
        "UPDATE cmd_ligne
     SET cmdl__garantie_puht =?
     WHERE cmdl__id =? ";
      $update = $this->Db->Pdo->prepare($updateNewLines);
      $update->execute($data);
    }

    $reversePrice = $ligne->devl_puht * -1;
    $dataPrice = [$reversePrice, $idLigne];
    $updateNewPrice =
      "UPDATE cmd_ligne
     SET cmdl__puht =?
     WHERE cmdl__id =? ";
    $updateReverse = $this->Db->Pdo->prepare($updateNewPrice);
    $updateReverse->execute($dataPrice);
  }

  // met a jour le status de la commande et le munero de facture:
  public function commande2facture($cmd)
  {
    //recup le dernier numero de facture: 
    $lastFact = $this->Db->Pdo->query('SELECT MAX(cmd__id_facture) as lastFact from cmd ');
    $lastFTC = $lastFact->fetch(PDO::FETCH_OBJ);

    $newfact = $lastFTC->lastFact + 1;

    $data =
      [
        $newfact,
        $cmd
      ];

    $sql =
      "UPDATE cmd
     SET 
     cmd__id_facture =? ,
     cmd__etat = 'VLD'
     WHERE cmd__id =? ";

    $update = $this->Db->Pdo->prepare($sql);
    $update->execute($data);
  }



  //recupère tous les status VLD
  public function getFromStatus()
  {
    $request = $this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id ,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    u.log_nec
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__etat = 'VLD'     
    ORDER BY  cmd__date_devis DESC , c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function getCMD()
  {
    $request = $this->Db->Pdo->query("SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi,
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp, c.client__id_vendeur ,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.user__email_devis , u.nom as nomDevis , u.prenom as prenomDevis , 
      u2.nom as nomCMD , u2.prenom as prenomCMD 
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON c.client__id_vendeur = u.id_utilisateur
      LEFT JOIN utilisateur as u2 ON cmd__user__id_cmd = u2.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id 
      WHERE cmd__etat = 'CMD'  
      ORDER BY  cmd__date_devis DESC , c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  //recupére tous les status cmd
  public function getFromStatusCMD()
  {
    $request = $this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__nom_devis, cmd__modele_devis , 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    u.log_nec , u.user__email_devis as email
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__etat = 'CMD' OR cmd__etat = 'IMP'    
    ORDER BY  cmd__date_devis DESC , c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  //prend l'id facture le plus élevé:
  public function getMaxFacture()
  {
    $request = $this->Db->Pdo->query('SELECT MAX(cmd__id_facture) as cmd__id_facture
    FROM cmd');
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }

  //prend l'id facture le plus élevé:
  public function getMinFacture()
  {
    $request = $this->Db->Pdo->query('SELECT MIN(cmd__id_facture) as cmd__id_facture
    FROM cmd');
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }

  //recupere toute les lignes de cmd entre 2 id cmd 
  public function ligneXport($start, $end)
  {
    $request = $this->Db->Pdo->query("SELECT
      cmd__id
      FROM cmd 
      WHERE cmd__id_facture BETWEEN " . $start . " AND " . $end . "
      ORDER BY cmd__id_facture ASC ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  //recupere toutes les lignes du tableau d'id passé en parametre:
  public function exportFinal($array)
  {
    $response = [];
    foreach ($array as  $value) {
      $temp = $this->devisLigneFacturee($value->cmd__id);
      array_push($response, $temp);
    }
    return $response;
  }


  //prend le status en CHAR de 3 en parametre et renvoi tous les devis:
  public function getFromStatusAll($status)
  {
    $request = $this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id, 
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__note_client as  devis__note_client , 
    cmd__note_interne as devis__note_interne,
    cmd__client__id_livr as devis__id_client_livraison ,
    cmd__contact__id_livr as  devis__contact_livraison , 
    cmd__date_cmd,  cmd__date_envoi,
    cmd__nom_devis, cmd__modele_devis , cmd__modele_facture, cmd__id_facture , cmd__date_fact, 
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    u.log_nec , u.user__email_devis as email
    FROM cmd
    LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
    LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
    LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
    LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
    LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
    WHERE cmd__etat = '" . $status . "'    
    ORDER BY  cmd__id_facture DESC , c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }



  public function insert($array){
    $request = $this->Db->Pdo->prepare(
      'INSERT INTO cmd (
        cmd__date_devis , cmd__user__id_devis, cmd__client__id_fact ,
        cmd__client__id_livr, cmd__contact__id_fact,
        cmd__note_client, 
        cmd__etat )
        VALUES ( 
        :cmd__date_devis, :cmd__user__id_devis, :cmd__client__id_fact, 
        :cmd__client__id_livr, :cmd__contact__id_fact, 
        :cmd__note_client, :cmd__etat )'
    );
    $request->bindValue(":cmd__date_devis", $array['cmd__date_devis']);
    $request->bindValue(":cmd__user__id_devis", $array['cmd__user__id_devis']);
    $request->bindValue(":cmd__client__id_fact", $array['scm__client_id_fact']);
    $request->bindValue(":cmd__client__id_livr", $array['scm__client_id_livr']);
    $request->bindValue(":devis__contact__id", $contact);
    $request->bindValue(":devis__note_client", $comClient);
    $request->bindValue(":devis__note_interne", $comInterne);
    $request->bindValue(":devis__etat", $etat);
    $request->bindValue(":devis__modele", $modele);
    $request->bindValue(":devis__id_contact_livraison", $contact_livraison);
    $request->bindValue(":nom_devis", $titreDevis);
    $request->execute();
    $idDevis = $this->Db->Pdo->lastInsertId();

  }



  //crée un nouveau devis:
  public function insertOne(
    $date,
    $user,
    $client,
    $livraison,
    $contact,
    $comClient,
    $comInterne,
    $etat,
    $modele,
    $arrayOfObject,
    $contact_livraison,
    $titreDevis
  ) {


    if (preg_match('/style="width:[^\"]*"/', $comClient)) {

      preg_match('/style="width:[^\"]*"/', $comClient, $matches);
      $matches[0] = ' ' .  $matches[0];
      $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ', $comClient);
      $comClient = $chaine;
    }



    $request = $this->Db->Pdo->prepare(
      'INSERT INTO cmd (
        cmd__date_devis , cmd__user__id_devis, cmd__client__id_fact ,
        cmd__client__id_livr, cmd__contact__id_fact,
        cmd__note_client, cmd__note_interne,
        cmd__etat , cmd__modele_devis , cmd__contact__id_livr , cmd__nom_devis)
        VALUES ( 
        :devis__date_crea, :devis__user__id, :devis__client__id, 
        :devis__id_client_livraison, :devis__contact__id, 
        :devis__note_client, :devis__note_interne, :devis__etat ,
        :devis__modele , :devis__id_contact_livraison, :nom_devis)'
    );


    $requestLigne =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_ligne (
        cmdl__cmd__id, cmdl__prestation, cmdl__pn , cmdl__designation ,
        cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  , cmdl__prix_barre , 
        cmdl__puht , cmdl__note_client  , cmdl__note_interne  , cmdl__ordre , cmdl__id__fmm)
        VALUES (
        :devl__devis__id, :devl__type, :devl__modele, :devl__designation,
        :devl__etat, :devl__mois_garantie , :devl_quantite, :devl__prix_barre, 
        :devl_puht , :devl__note_client , :devl__note_interne , :devl__ordre , :id__fmm)'
    );

    $requestGarantie =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_garantie ( 
        cmdg__id__cmdl , cmdg__type , cmdg__prix  , cmdg__ordre )
        VALUES (
        :devg__id__devl , :devg__type , :devg__prix, :devg__ordre )'
    );

    $request->bindValue(":devis__date_crea", $date);
    $request->bindValue(":devis__user__id", $user);
    $request->bindValue(":devis__client__id", $client);
    $request->bindValue(":devis__id_client_livraison", $livraison);
    $request->bindValue(":devis__contact__id", $contact);
    $request->bindValue(":devis__note_client", $comClient);
    $request->bindValue(":devis__note_interne", $comInterne);
    $request->bindValue(":devis__etat", $etat);
    $request->bindValue(":devis__modele", $modele);
    $request->bindValue(":devis__id_contact_livraison", $contact_livraison);
    $request->bindValue(":nom_devis", $titreDevis);
    $request->execute();
    $idDevis = $this->Db->Pdo->lastInsertId();
    $count = 0;
    foreach ($arrayOfObject as $object) {

      $verify = $this->Db->Pdo->query('
        SELECT  afmm__famille FROM art_fmm WHERE afmm__id = ' .  $object->id__fmm . '');
      $response  = $verify->fetch(PDO::FETCH_OBJ);
      $count += 1;
      $requestLigne->bindValue(":devl__devis__id", $idDevis);
      $requestLigne->bindValue(":devl__type", $object->prestation);
      $requestLigne->bindValue(":devl__modele", $object->pn);
      $requestLigne->bindValue(":devl__designation", $object->designation);
      $requestLigne->bindValue(":id__fmm", $object->id__fmm);



      if ($response->afmm__famille == 'SER') {
        $requestLigne->bindValue(":devl__mois_garantie", intval(0));
        $requestLigne->bindValue(":devl__etat", 'NC.');
      } else {
        $requestLigne->bindValue(":devl__etat", $object->etat);
        $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
      }

      if (preg_match('/style="width:[^\"]*"/', $object->comClient)) {
        preg_match('/style="width:[^\"]*"/', $object->comClient, $matches);
        $matches[0] = ' ' .  $matches[0];
        $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ', $object->comClient);
        $object->comClient = $chaine;
      }

      $requestLigne->bindValue(":devl_quantite", $object->quantite);
      $requestLigne->bindValue(":devl__prix_barre", floatval($object->prixBarre));
      $requestLigne->bindValue(":devl_puht", floatval($object->prix));
      $requestLigne->bindValue(":devl__note_client", $object->comClient);
      $requestLigne->bindValue(":devl__note_interne", $object->comInterne);
      $requestLigne->bindValue(":devl__ordre", $object->id);
      $requestLigne->execute();
      $idLigne = $this->Db->Pdo->lastInsertId();
      $count2 = 0;
      foreach ($object->xtend as $xtend) {
        $count2 += 1;
        $requestGarantie->bindValue(":devg__id__devl", $idLigne);
        $requestGarantie->bindValue("devg__type", $xtend[0]);
        $requestGarantie->bindValue("devg__prix", floatval($xtend[1]));
        $requestGarantie->bindValue("devg__ordre", $count2);
        $requestGarantie->execute();
      }
    }
    return $idDevis;
  }


  //efface une ligne : 
  public function deleteLine($id, $cmdid)
  {
    $request = "DELETE FROM cmd_ligne WHERE cmdl__id ='" . $id . "' AND cmdl__cmd__id = '" . $cmdid . "'";
    $update = $this->Db->Pdo->prepare($request);
    $update->execute();
    return true;
  }



  //efface et remplace le devis: 
  public function modify(
    $id,
    $date,
    $user,
    $client,
    $livraison,
    $contact,
    $comClient,
    $comInterne,
    $etat,
    $modele,
    $arrayOfObject,
    $contact_livraison,
    $titreDevis
  ) {

    $delete = $this->Db->Pdo->prepare(
      'DELETE  from cmd
     WHERE cmd__id =  :cmd__id'
    );

    if (preg_match('/style="width:[^\"]*"/', $comClient)) {

      preg_match('/style="width:[^\"]*"/', $comClient, $matches);
      $matches[0] = ' ' .  $matches[0];
      $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ', $comClient);
      $comClient = $chaine;
    }


    $request = $this->Db->Pdo->prepare(
      'INSERT INTO cmd (
      cmd__id, cmd__date_devis , cmd__user__id_devis, cmd__client__id_fact ,
      cmd__client__id_livr,  cmd__contact__id_fact,
      cmd__note_client, cmd__note_interne,
      cmd__etat , cmd__modele_devis , cmd__contact__id_livr , cmd__nom_devis)
      VALUES ( 
      :cmd__id , :devis__date_crea, :devis__user__id, :devis__client__id, 
      :devis__id_client_livraison,  :devis__contact__id, 
      :devis__note_client, :devis__note_interne, :devis__etat ,
      :devis__modele , :devis__id_contact_livraison , :nomDevis)'
    );

    $requestLigne =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_ligne (
      cmdl__cmd__id, cmdl__prestation, cmdl__pn , cmdl__designation ,
      cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  , cmdl__prix_barre , 
      cmdl__puht , cmdl__note_client  , cmdl__note_interne  , cmdl__ordre , cmdl__id__fmm)
      VALUES (
      :devl__devis__id, :devl__type, :devl__modele, :devl__designation,
      :devl__etat, :devl__mois_garantie , :devl_quantite, :devl__prix_barre, 
      :devl_puht , :devl__note_client , :devl__note_interne , :devl__ordre , :id__fmm)'
    );

    $requestGarantie =  $this->Db->Pdo->prepare(
      'INSERT INTO  cmd_garantie ( 
      cmdg__id__cmdl , cmdg__type , cmdg__prix  , cmdg__ordre )
      VALUES (
      :devg__id__devl , :devg__type , :devg__prix, :devg__ordre )'
    );

    $delete->bindValue(":cmd__id", $id);
    $delete->execute();
    $request->bindValue(":cmd__id", $id);
    $request->bindValue(":devis__date_crea", $date);
    $request->bindValue(":devis__user__id", $user);
    $request->bindValue(":devis__client__id", $client);
    $request->bindValue(":devis__id_client_livraison", $livraison);
    $request->bindValue(":devis__contact__id", $contact);
    $request->bindValue(":devis__note_client", $comClient);
    $request->bindValue(":devis__note_interne", $comInterne);
    $request->bindValue(":devis__etat", $etat);
    $request->bindValue(":devis__modele", $modele);
    $request->bindValue(":devis__id_contact_livraison", $contact_livraison);
    $request->bindValue(":nomDevis", $titreDevis);
    $request->execute();
    $idDevis = $this->Db->Pdo->lastInsertId();
    $count = 0;
    foreach ($arrayOfObject as $object) {
      $verify = $this->Db->Pdo->query('
        SELECT  afmm__famille FROM art_fmm WHERE afmm__id = ' .  $object->id__fmm . '');
      $respnse =  $verify->fetch(PDO::FETCH_OBJ);

      $count += 1;
      $requestLigne->bindValue(":devl__devis__id", $idDevis);
      $requestLigne->bindValue(":devl__type", $object->prestation);
      $requestLigne->bindValue(":devl__modele", $object->pn);
      $requestLigne->bindValue(":id__fmm", $object->id__fmm);
      $requestLigne->bindValue(":devl__designation", $object->designation);

      if ($respnse->afmm__famille == 'SER') {
        $requestLigne->bindValue(":devl__mois_garantie", intval(0));
        $requestLigne->bindValue(":devl__etat", 'NC.');
      } else {
        $requestLigne->bindValue(":devl__etat", $object->etat);
        $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
      }


      if (preg_match('/style="width:[^\"]*"/', $object->comClient)) {

        preg_match('/style="width:[^\"]*"/', $object->comClient, $matches);
        $matches[0] = ' ' .  $matches[0];
        $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ', $object->comClient);
        $object->comClient = $chaine;
      }



      $requestLigne->bindValue(":devl_quantite", $object->quantite);
      $requestLigne->bindValue(":devl__prix_barre", floatval($object->prixBarre));
      $requestLigne->bindValue(":devl_puht", floatval($object->prix));
      $requestLigne->bindValue(":devl__note_client", $object->comClient);
      $requestLigne->bindValue(":devl__note_interne", $object->comInterne);
      $requestLigne->bindValue(":devl__ordre", $object->id);
      $requestLigne->execute();
      $idLigne = $this->Db->Pdo->lastInsertId();
      $count2 = 0;
      foreach ($object->xtend as $xtend) {
        $count2 += 1;
        $requestGarantie->bindValue(":devg__id__devl", $idLigne);
        $requestGarantie->bindValue("devg__type", $xtend[0]);
        $requestGarantie->bindValue("devg__prix", floatval($xtend[1]));
        $requestGarantie->bindValue("devg__ordre", $count2);
        $requestGarantie->execute();
      }
    }
    return $idDevis;
  }





  public function devisLigneUnit($id)
  {
    $request = $this->Db->Pdo->query("SELECT
      cmdl__cmd__id,
      cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
      cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
      cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
      cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
      cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
      cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
      cmdl__garantie_option, cmdl__qte_livr , cmdl__qte_fact, cmdl__note_facture, cmdl__sous_garantie ,cmdl__actif ,
      k.kw__lib , k.kw__value , 
      f.afmm__famille as famille,
      f.afmm__modele as modele,
      k2.kw__lib as prestaLib,
      k3.kw__info as groupe_famille,
      k3.kw__lib as famille__lib,
      a.am__marque as marque
      FROM cmd_ligne 
      LEFT JOIN keyword as k ON cmdl__etat = k.kw__value AND k.kw__type = 'letat'
      LEFT JOIN keyword as k2 ON cmdl__prestation = k2.kw__value AND k2.kw__type = 'pres'
      LEFT JOIN art_fmm as f ON afmm__id = cmdl__id__fmm
      LEFT JOIN keyword as k3 ON f.afmm__famille = k3.kw__value AND k3.kw__type = 'famil'
      LEFT JOIN art_marque as a ON f.afmm__marque = a.am__id
      WHERE cmdl__id = " . $id . "
      ORDER BY devl__ordre ");

    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }

  public function xtenGarantie($id)
  {
    $request = $this->Db->Pdo->query("SELECT 
      cmdg__id as devg__id,  LPAD(cmdg__type ,2,0)  as  devg__type, cmdg__prix as  devg__prix , cmdg__prix_barre , cmdg__id__cmdl
      FROM cmd_garantie  
      WHERE cmdg__id__cmdl = " . $id . "");
    $data = $request->fetchAll(PDO::FETCH_ASSOC);
    return $data;
  }

  public function magicRequestStatus($string, $status)
  {

    $filtre = str_replace("-", ' ', $string);
    $filtre = str_replace("'", ' ', $filtre);
    $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
    $mots_filtre = str_word_count($filtre, 1, '0123456789');

    if ($nb_mots_filtre > 0) {
      $mode_filtre = true;
    } else {
      $mode_filtre = false;
    }

    $operateur = 'AND ';

    $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi, cmd__modele_facture, cmd__id_facture , cmd__date_fact, 
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

    if ($mode_filtre) {
      $request .=  "WHERE ( cmd__id = '" . $mots_filtre[0] . "' 
        OR cmd__nom_devis LIKE '%" . $mots_filtre[0] . "%' 
        OR u.prenom LIKE '%" . $mots_filtre[0] . "%' 
        OR l.cmdl__designation LIKE '%" . $mots_filtre[0] . "%'
        OR l.cmdl__pn LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__societe LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__id = '" . $mots_filtre[0] . "' ) ";

      for ($i = 1; $i < $nb_mots_filtre; $i++) {
        $request .=  $operateur . " ( cmd__id = '" . $mots_filtre[$i] . "' 
          OR cmd__nom_devis LIKE '%" . $mots_filtre[$i] . "%' 
          OR u.prenom LIKE '%" . $mots_filtre[$i] . "%' 
          OR l.cmdl__designation LIKE '%" . $mots_filtre[$i] . "%'
          OR l.cmdl__pn LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__societe LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__id = '" . $mots_filtre[$i] . "' ) ";
      }
      $request .= "AND ( cmd__etat = '" . $status . "' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
    } else {
      $request .= "AND ( cmd__etat = '" . $status . "' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
    }

    $send = $this->Db->Pdo->query($request);
    $data = $send->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function magicRequest($string)
  {

    $filtre = str_replace("-", ' ', $string);
    $filtre = str_replace("'", ' ', $filtre);
    $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
    $mots_filtre = str_word_count($filtre, 1, '0123456789');

    if ($nb_mots_filtre > 0) {
      $mode_filtre = true;
    } else {
      $mode_filtre = false;
    }

    $operateur = 'AND ';

    $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi,
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp, c.client__bloque ,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis, cmd__id_facture ,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

    if ($mode_filtre) {
      $request .=  "WHERE ( cmd__id = '" . $mots_filtre[0] . "' 
        OR cmd__nom_devis LIKE '%" . $mots_filtre[0] . "%' 
        OR u.prenom LIKE '%" . $mots_filtre[0] . "%' 
        OR l.cmdl__designation LIKE '%" . $mots_filtre[0] . "%'
        OR l.cmdl__pn LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__societe LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__id = '" . $mots_filtre[0] . "' ) ";

      for ($i = 1; $i < $nb_mots_filtre; $i++) {
        $request .=  $operateur . " ( cmd__id = '" . $mots_filtre[$i] . "' 
          OR cmd__nom_devis LIKE '%" . $mots_filtre[$i] . "%' 
          OR u.prenom LIKE '%" . $mots_filtre[$i] . "%' 
          OR l.cmdl__designation LIKE '%" . $mots_filtre[$i] . "%'
          OR l.cmdl__pn LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__societe LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__id = '" . $mots_filtre[$i] . "' ) ";
      }
      $request .= " AND  ( cmd__etat != 'PBL' )  ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
    } else {
      $request .=  " AND  ( cmd__etat != 'PBL' )  ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
    }

    $send = $this->Db->Pdo->query($request);
    $data = $send->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }




  public function magicRequestUserCMD($string, $user)
  {

    $filtre = str_replace("-", ' ', $string);
    $filtre = str_replace("'", ' ', $filtre);
    $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
    $mots_filtre = str_word_count($filtre, 1, '0123456789');

    if ($nb_mots_filtre > 0) {
      $mode_filtre = true;
    } else {
      $mode_filtre = false;
    }

    $operateur = 'AND ';

    $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      k.kw__lib,  cmd__date_envoi,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,  cmd__date_cmd,  cmd__date_envoi,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

    if ($mode_filtre) {
      $request .=  "WHERE ( cmd__id = '" . $mots_filtre[0] . "' 
        OR cmd__nom_devis LIKE '%" . $mots_filtre[0] . "%' 
        OR u.prenom LIKE '%" . $mots_filtre[0] . "%' 
        OR l.cmdl__designation LIKE '%" . $mots_filtre[0] . "%'
        OR l.cmdl__pn LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__societe LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__id = '" . $mots_filtre[0] . "' ) ";

      for ($i = 1; $i < $nb_mots_filtre; $i++) {
        $request .=  $operateur . " ( cmd__id = '" . $mots_filtre[$i] . "' 
          OR cmd__nom_devis LIKE '%" . $mots_filtre[$i] . "%' 
          OR u.prenom LIKE '%" . $mots_filtre[$i] . "%' 
          OR l.cmdl__designation LIKE '%" . $mots_filtre[$i] . "%'
          OR l.cmdl__pn LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__societe LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__id = '" . $mots_filtre[$i] . "' ) ";
      }
      $request .= " AND  ( cmd__etat = 'CMD' )  AND cmd__user__id_devis = '" . $user . "'  ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
    } else {
      $request .=  " AND   ( cmd__etat = 'CMD' )  AND   cmd__user__id_devis = '" . $user . "' ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
    }

    $send = $this->Db->Pdo->query($request);
    $data = $send->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }





  public function magicRequestUser($string, $user)
  {

    $filtre = str_replace("-", ' ', $string);
    $filtre = str_replace("'", ' ', $filtre);
    $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
    $mots_filtre = str_word_count($filtre, 1, '0123456789');

    if ($nb_mots_filtre > 0) {
      $mode_filtre = true;
    } else {
      $mode_filtre = false;
    }

    $operateur = 'AND ';

    $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      k.kw__lib,  cmd__date_envoi,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,  c.client__bloque ,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis, cmd__id_facture ,
      u.log_nec , u.prenom, u.nom
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

    if ($mode_filtre) {
      $request .=  "WHERE ( cmd__id = '" . $mots_filtre[0] . "' 
        OR cmd__nom_devis LIKE '%" . $mots_filtre[0] . "%' 
        OR u.prenom LIKE '%" . $mots_filtre[0] . "%' 
        OR l.cmdl__designation LIKE '%" . $mots_filtre[0] . "%'
        OR l.cmdl__pn LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__societe LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__id = '" . $mots_filtre[0] . "' ) ";

      for ($i = 1; $i < $nb_mots_filtre; $i++) {
        $request .=  $operateur . " ( cmd__id = '" . $mots_filtre[$i] . "' 
          OR cmd__nom_devis LIKE '%" . $mots_filtre[$i] . "%' 
          OR u.prenom LIKE '%" . $mots_filtre[$i] . "%' 
          OR l.cmdl__designation LIKE '%" . $mots_filtre[$i] . "%'
          OR l.cmdl__pn LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__societe LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__id = '" . $mots_filtre[$i] . "' ) ";
      }
      $request .= " AND  ( cmd__etat != 'PBL' )  AND ( cmd__user__id_devis = '" . $user . "' ) ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
    } else {
      $request .=  "  AND  ( cmd__etat != 'PBL' )  AND ( cmd__user__id_devis = '" . $user . "' ) ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
    }

    $send = $this->Db->Pdo->query($request);
    $data = $send->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }








  public function magicRequestCMD($string)
  {

    $filtre = str_replace("-", ' ', $string);
    $filtre = str_replace("'", ' ', $filtre);
    $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
    $mots_filtre = str_word_count($filtre, 1, '0123456789');

    if ($nb_mots_filtre > 0) {
      $mode_filtre = true;
    } else {
      $mode_filtre = false;
    }

    $operateur = 'AND ';

    $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi,
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.prenom, u.nom , 
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

    if ($mode_filtre) {
      $request .=  "WHERE ( cmd__id = '" . $mots_filtre[0] . "' 
        OR cmd__nom_devis LIKE '%" . $mots_filtre[0] . "%' 
        OR u.prenom LIKE '%" . $mots_filtre[0] . "%' 
        OR l.cmdl__designation LIKE '%" . $mots_filtre[0] . "%'
        OR l.cmdl__pn LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__societe LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__id = '" . $mots_filtre[0] . "' ) ";

      for ($i = 1; $i < $nb_mots_filtre; $i++) {
        $request .=  $operateur . " ( cmd__id = '" . $mots_filtre[$i] . "' 
          OR cmd__nom_devis LIKE '%" . $mots_filtre[$i] . "%' 
          OR u.prenom LIKE '%" . $mots_filtre[$i] . "%' 
          OR l.cmdl__designation LIKE '%" . $mots_filtre[$i] . "%'
          OR l.cmdl__pn LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__societe LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__id = '" . $mots_filtre[$i] . "' ) ";
      }
      $request .= "AND ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
    } else {
      $request .=   "AND ( cmd__etat = 'CMD' OR cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
    }

    $send = $this->Db->Pdo->query($request);
    $data = $send->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function magicRequestFunnyBunny($string, $session)
  {

    if (!empty($string)) {

      $filtre = str_replace("-", ' ', $string);
      $filtre = str_replace("'", ' ', $filtre);
      $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
      $mots_filtre = str_word_count($filtre, 1, '0123456789');

      if ($nb_mots_filtre > 0) {
        $mode_filtre = true;
      } else {
        $mode_filtre = false;
      }

      $operateur = 'AND ';

      $request = "SELECT DISTINCT 
      cmd__id as devis__id ,
      cmd__user__id_devis as devis__user__id ,
      cmd__date_devis as devis__date_crea, 
      LPAD(cmd__client__id_fact ,6,0)   as client__id,
      cmd__contact__id_fact  as  devis__contact__id,
      cmd__etat as devis__etat, 
      cmd__note_client as  devis__note_client , 
      cmd__note_interne as devis__note_interne,
      cmd__client__id_livr as devis__id_client_livraison ,
      cmd__contact__id_livr as  devis__contact_livraison , 
      cmd__date_cmd,  cmd__date_envoi,
      k.kw__lib,
      t.contact__nom, t.contact__prenom, t.contact__email,
      c.client__societe, c.client__adr1 , c.client__ville, c.client__cp, c.client__id_vendeur ,
      c2.client__societe as client__livraison_societe,
      c2.client__ville as client__livraison_ville,
      c2.client__cp as client__livraison_cp , 
      c2.client__adr1 as client__livraison__adr1 , 
      cmd__nom_devis,
      u.log_nec , u.user__email_devis , u.nom as nomDevis , u.prenom as prenomDevis , 
      u2.nom as nomCMD , u2.prenom as prenomCMD 
      FROM cmd
      LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
      LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
      LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
      LEFT JOIN keyword as k ON cmd__etat = k.kw__value and k.kw__type = 'stat'
      LEFT JOIN utilisateur as u ON c.client__id_vendeur = u.id_utilisateur
      LEFT JOIN utilisateur as u2 ON cmd__user__id_cmd = u2.id_utilisateur
      LEFT JOIN cmd_ligne as l ON l.cmdl__cmd__id = cmd__id ";

      if ($mode_filtre) {
        $request .=  "WHERE ( cmd__id = '" . $mots_filtre[0] . "' 
        OR u.prenom LIKE '%" . $mots_filtre[0] . "%' 
        OR u.nom LIKE '%" . $mots_filtre[0] . "%' 
        OR l.cmdl__designation LIKE '%" . $mots_filtre[0] . "%'
        OR l.cmdl__pn LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__societe LIKE '%" . $mots_filtre[0] . "%' 
        OR c.client__id = '" . $mots_filtre[0] . "' ) ";

        for ($i = 1; $i < $nb_mots_filtre; $i++) {
          $request .=  $operateur . " ( cmd__id = '" . $mots_filtre[$i] . "' 
          OR u.prenom LIKE '%" . $mots_filtre[0] . "%' 
          OR u.nom LIKE '%" . $mots_filtre[0] . "%' 
          OR l.cmdl__designation LIKE '%" . $mots_filtre[$i] . "%'
          OR l.cmdl__pn LIKE '%" . $mots_filtre[$i] . "%' 
          OR c.client__societe LIKE '%" . $mots_filtre[$i] . "%'
          OR c.client__id = '" . $mots_filtre[$i] . "' ) ";
        }
        if ($session == 'ALL') {
          $request .= "AND ( cmd__etat = 'CMD' OR cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        } elseif ($session == 'FT') {
          $request .= "AND ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        } elseif ($session == 'BL') {
          $request .= "AND ( cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
      } else {
        if ($session == 'ALL') {
          $request .= "AND ( cmd__etat = 'CMD' OR cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        } elseif ($session == 'FT') {
          $request .= "AND ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        } elseif ($session == 'BL') {
          $request .= "AND ( cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
        }
      }

      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    } else {
      $request = "SELECT 
        cmd__id as devis__id ,
        cmd__user__id_devis as devis__user__id ,
        cmd__date_devis as devis__date_crea, 
        LPAD(cmd__client__id_fact ,6,0)   as client__id, 
        cmd__contact__id_fact  as  devis__contact__id,
        cmd__etat as devis__etat, 
        cmd__note_client as  devis__note_client , 
        cmd__note_interne as devis__note_interne,
        cmd__client__id_livr as devis__id_client_livraison ,
        cmd__contact__id_livr as  devis__contact_livraison , 
        cmd__date_cmd,  cmd__date_envoi,
        cmd__nom_devis, cmd__modele_devis , 
        k.kw__lib,
        t.contact__nom, t.contact__prenom, t.contact__email,
        c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,
        c2.client__societe as client__livraison_societe,
        c2.client__ville as client__livraison_ville,
        c2.client__cp as client__livraison_cp , 
        c2.client__adr1 as client__livraison__adr1 , 
        u.log_nec , u.user__email_devis as email
        FROM cmd
        LEFT JOIN contact as t ON  cmd__contact__id_fact = t.contact__id
        LEFT JOIN client as c ON cmd__client__id_fact = c.client__id
        LEFT JOIN client as c2 ON cmd__client__id_livr = c2.client__id
        LEFT JOIN keyword as k ON cmd__etat = k.kw__value AND  k.kw__type = 'stat'
        LEFT JOIN utilisateur as u ON cmd__user__id_devis = u.id_utilisateur ";

      if ($session == 'ALL') {
        $request .= "WHERE  ( cmd__etat = 'CMD' OR cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
      } elseif ($session == 'FT') {
        $request .= "WHERE ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 400  ";
      } elseif ($session == 'BL') {
        $request .= "WHERE ( cmd__etat = 'IMP' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 400  ";
      }


      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }
  }



  public function alertReliquat($Cmd)
  {
    $lignes = $this->devisLigne($Cmd);

    $results = [];

    foreach ($lignes as $ligne) {
      if (intval($ligne->devl_quantite) > intval($ligne->cmdl__qte_fact)) {
        $ligne->devl_quantite = intval($ligne->devl_quantite) - intval($ligne->cmdl__qte_fact);
        array_push($results, $ligne);
      }
    }

    if (!empty($results)) {
      return true;
    } else return false;
  }



  public function  update_ordre_sous_ref(?iterable $tableau_ligne): void
  {
    //tableau qui contiendra les sous références provisoirement : 
    $tableau_sous_ref = [];
    //parcours le tableau de ligne donné en parmètrres : 
    foreach ($tableau_ligne as $key => $ligne) {
      if (!empty($ligne->cmdl__sous_ref)) {
        //je place la sous-ref dans le tableau provisoire :
        array_push($tableau_sous_ref, $ligne);
        //je la detruit dans ce tableau passé en paramètre : 
        unset($tableau_ligne[$key]);
      }
    }

    //une fois le premier tri terminé je boucle une deuxième fois afin de déterminer les ordres : 
    $count = 0;
    foreach ($tableau_ligne as $key => $ligne) {
      $count += 1;
      $data_ligne = [$count, $ligne->devl__id];
      $sql_update_ligne = $this->Db->Pdo->prepare('UPDATE cmd_ligne SET cmdl__ordre = ? WHERE cmdl__id = ?');
      $sql_update_ligne->execute($data_ligne);
      //je parcours le tableau de sous-références: 
      foreach ($tableau_sous_ref as $sous_ref) {
        //l'id sous ref de la sous_ref est égal à l'id de la mère : 
        if ($sous_ref->cmdl__sous_ref == $ligne->devl__id) {
          $count += 1;
          $data = [$count, $sous_ref->devl__id];
          $sql_update = $this->Db->Pdo->prepare('UPDATE cmd_ligne SET cmdl__ordre = ? WHERE cmdl__id = ?');
          $sql_update->execute($data);
        }
      }
    }
  }
}
