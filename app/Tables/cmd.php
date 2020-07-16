<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Cmd extends Table {

  public Database $Db;

  public function __construct($db) {
    $this->Db = $db;
  }



  public function GetById($id){
    $request =$this->Db->Pdo->query("SELECT
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
    cmd__nom_devis, cmd__modele_devis , 
    cmd__date_cmd,
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
    WHERE cmd__id = ". $id ."");
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }




  public function getUserDevis($id){
    $request =$this->Db->Pdo->query("SELECT 
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
    WHERE  cmd__user__id_devis = " . $id ." ORDER BY  devis__date_crea DESC , c.client__societe ASC LIMIT 200");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function getAll(){
    $request =$this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_cmd,
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


  public function updateStatus($etat,$id){
    $update = $this->Db->Pdo->prepare(
    'UPDATE cmd
     SET cmd__etat=? 
     WHERE cmd__id =?');
    $update->execute([$etat,$id]);
  }

  public function updateDate( $column , $date,  $id){
    $update = $this->Db->Pdo->prepare(
    'UPDATE cmd 
     SET ' . $column. ' = ? 
     WHERE cmd__id =?    
    ');
    $update->execute([ $date , $id ]);
  }

  public function updateAuthor($column , $author , $id){
    $update = $this->Db->Pdo->prepare(
    'UPDATE cmd 
     SET '. $column .' = ? 
     WHERE cmd__id = ? 
    ');
     $update->execute([ $author , $id ]);
  }

  public function updateGarantie($mois, $prix , $comInterne , $qte,  $id , $ordre){
    $data = 
    [
      $mois, 
      floatval($prix) , 
      $comInterne , 
      $qte,
      $id , 
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

  public function getFromStatus(){
    $request =$this->Db->Pdo->query("SELECT 
    cmd__id as devis__id ,
    cmd__user__id_devis as devis__user__id ,
    cmd__date_devis as devis__date_crea, 
    LPAD(cmd__client__id_fact ,6,0)   as client__id ,
    cmd__contact__id_fact  as  devis__contact__id,
    cmd__etat as devis__etat, 
    cmd__date_cmd,
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


  public function getFromStatusCMD(){
    $request =$this->Db->Pdo->query("SELECT 
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
    cmd__date_cmd,
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
    WHERE cmd__etat = 'CMD'     
    ORDER BY  cmd__date_devis DESC , c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function getNotCMD(){
    $request =$this->Db->Pdo->query("SELECT 
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
    WHERE cmd__etat <> 'CMD'     
    ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC  LIMIT 200 ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }


  public function insertOne(
    $date , $user, $client , $livraison, $contact, $comClient,
    $comInterne, $etat, $modele , $arrayOfObject , $contact_livraison , $titreDevis )
    {


      if ( preg_match('/style="width:[^\"]*"/',$comClient)) 
    {
      
      preg_match('/style="width:[^\"]*"/',$comClient ,$matches);
      $matches[0] = ' ' .  $matches[0];
      $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ' , $comClient  );
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
        :devis__modele , :devis__id_contact_livraison, :nom_devis)');


    $requestLigne =  $this->Db->Pdo->prepare(
       'INSERT INTO  cmd_ligne (
        cmdl__cmd__id, cmdl__prestation, cmdl__pn , cmdl__designation ,
        cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  , cmdl__prix_barre , 
        cmdl__puht , cmdl__note_client  , cmdl__note_interne  , cmdl__ordre , cmdl__id__fmm)
        VALUES (
        :devl__devis__id, :devl__type, :devl__modele, :devl__designation,
        :devl__etat, :devl__mois_garantie , :devl_quantite, :devl__prix_barre, 
        :devl_puht , :devl__note_client , :devl__note_interne , :devl__ordre , :id__fmm)');

    $requestGarantie =  $this->Db->Pdo->prepare(
       'INSERT INTO  cmd_garantie ( 
        cmdg__id__cmdl , cmdg__type , cmdg__prix  , cmdg__ordre )
        VALUES (
        :devg__id__devl , :devg__type , :devg__prix, :devg__ordre )');

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
    $count = 0 ;
    foreach ($arrayOfObject as $object){
      
        $verify = $this->Db->Pdo->query('
        SELECT  afmm__famille FROM art_fmm WHERE afmm__id = '.  $object->id__fmm .'');
        $response  = $verify->fetch(PDO::FETCH_OBJ);
        $count+= 1 ;
        $requestLigne->bindValue(":devl__devis__id", $idDevis);
        $requestLigne->bindValue(":devl__type", $object->prestation);
        $requestLigne->bindValue(":devl__modele", $object->pn);
        $requestLigne->bindValue(":devl__designation", $object->designation);
        $requestLigne->bindValue(":id__fmm", $object->id__fmm);


        
        if ($response->afmm__famille == 'SER') {
          $requestLigne->bindValue(":devl__mois_garantie", intval( 0 ));
          $requestLigne->bindValue(":devl__etat",'NC.');
        }
        else {
          $requestLigne->bindValue(":devl__etat", $object->etat);
          $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
        }

        if ( preg_match('/style="width:[^\"]*"/', $object->comClient)) 
        { 
          preg_match('/style="width:[^\"]*"/', $object->comClient ,$matches);
          $matches[0] = ' ' .  $matches[0];
          $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ' , $object->comClient  );
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
        foreach($object->xtend as $xtend){
            $count2+= 1 ;
            $requestGarantie->bindValue(":devg__id__devl", $idLigne);
            $requestGarantie->bindValue("devg__type", $xtend[0]);
            $requestGarantie->bindValue("devg__prix", floatval($xtend[1]));
            $requestGarantie->bindValue("devg__ordre", $count2);
            $requestGarantie->execute();
        }
    } 
   return $idDevis;
}



public function modify(
    $id , $date , $user, $client , $livraison,  $contact, $comClient,
    $comInterne, $etat, $modele , $arrayOfObject , $contact_livraison , $titreDevis)
    {

    $delete = $this->Db->Pdo->prepare(
    'DELETE  from cmd
     WHERE cmd__id =  :cmd__id');

    if ( preg_match('/style="width:[^\"]*"/',$comClient)) 
    {

      preg_match('/style="width:[^\"]*"/',$comClient ,$matches);
      $matches[0] = ' ' .  $matches[0];
      $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ' , $comClient  );
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
      :devis__modele , :devis__id_contact_livraison , :nomDevis)');

    $requestLigne =  $this->Db->Pdo->prepare(
     'INSERT INTO  cmd_ligne (
      cmdl__cmd__id, cmdl__prestation, cmdl__pn , cmdl__designation ,
      cmdl__etat  ,cmdl__garantie_base , cmdl__qte_cmd  , cmdl__prix_barre , 
      cmdl__puht , cmdl__note_client  , cmdl__note_interne  , cmdl__ordre , cmdl__id__fmm)
      VALUES (
      :devl__devis__id, :devl__type, :devl__modele, :devl__designation,
      :devl__etat, :devl__mois_garantie , :devl_quantite, :devl__prix_barre, 
      :devl_puht , :devl__note_client , :devl__note_interne , :devl__ordre , :id__fmm)');

    $requestGarantie =  $this->Db->Pdo->prepare(
     'INSERT INTO  cmd_garantie ( 
      cmdg__id__cmdl , cmdg__type , cmdg__prix  , cmdg__ordre )
      VALUES (
      :devg__id__devl , :devg__type , :devg__prix, :devg__ordre )');

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
    $request->bindValue(":devis__id_contact_livraison",$contact_livraison);
    $request->bindValue(":nomDevis", $titreDevis);
    $request->execute();
    $idDevis = $this->Db->Pdo->lastInsertId();
    $count = 0 ;
    foreach ($arrayOfObject as $object){
        $verify = $this->Db->Pdo->query('
        SELECT  afmm__famille FROM art_fmm WHERE afmm__id = '.  $object->id__fmm .'');
        $respnse =  $verify->fetch(PDO::FETCH_OBJ);
        
        $count+= 1 ;
        $requestLigne->bindValue(":devl__devis__id", $idDevis);
        $requestLigne->bindValue(":devl__type", $object->prestation);
        $requestLigne->bindValue(":devl__modele", $object->pn);
        $requestLigne->bindValue(":id__fmm", $object->id__fmm);
        $requestLigne->bindValue(":devl__designation", $object->designation);
        
        if ($respnse->afmm__famille == 'SER') {
          $requestLigne->bindValue(":devl__mois_garantie", intval( 0 ));
          $requestLigne->bindValue(":devl__etat",'NC.');
        }
        else {
          $requestLigne->bindValue(":devl__etat", $object->etat);
          $requestLigne->bindValue(":devl__mois_garantie", intval($object->garantie));
        }


        if ( preg_match('/style="width:[^\"]*"/', $object->comClient)) 
        {
          
          preg_match('/style="width:[^\"]*"/', $object->comClient ,$matches);
          $matches[0] = ' ' .  $matches[0];
          $chaine = preg_replace('/img/',  'img' .  $matches[0] . ' ' , $object->comClient  );
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
        foreach($object->xtend as $xtend){
            $count2+= 1 ;
            $requestGarantie->bindValue(":devg__id__devl", $idLigne);
            $requestGarantie->bindValue("devg__type", $xtend[0]);
            $requestGarantie->bindValue("devg__prix", floatval($xtend[1]));
            $requestGarantie->bindValue("devg__ordre", $count2);
            $requestGarantie->execute();
        }
    } 
    return $idDevis;
    }

    public function devisLigne($id){
      $request =$this->Db->Pdo->query("SELECT
      cmdl__cmd__id,
      cmdl__id as devl__id ,cmdl__prestation as  devl__type, 
      cmdl__pn as devl__modele,  cmdl__designation as devl__designation,
      cmdl__etat as devl__etat, LPAD(cmdl__garantie_base,2,0) as devl__mois_garantie,
      cmdl__qte_cmd as devl_quantite, cmdl__prix_barre as  devl__prix_barre, 
      cmdl__puht as  devl_puht, cmdl__ordre as devl__ordre , cmdl__id__fmm as id__fmm, 
      cmdl__note_client as devl__note_client,  cmdl__note_interne as devl__note_interne , 
      cmdl__garantie_option,
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
      WHERE cmdl__cmd__id = ". $id ."
      ORDER BY devl__ordre ");
     
      $data = $request->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }

    public function xtenGarantie($id){
      $request =$this->Db->Pdo->query("SELECT 
      cmdg__id as devg__id,  LPAD(cmdg__type ,2,0)  as  devg__type, cmdg__prix as  devg__prix
      FROM cmd_garantie  
      WHERE cmdg__id__cmdl = ". $id ."");
      $data = $request->fetchAll(PDO::FETCH_ASSOC);
      return $data;
    }

    public function magicRequest($string){

      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

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
      cmd__date_cmd,
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
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
        $request .= "ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      else {
        $request.=  "ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }




    public function magicRequestUser($string , $user){

      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

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
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
        $request .= "AND ( cmd__user__id_devis = '".$user."' ) ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      else {
        $request.=  "AND ( cmd__user__id_devis = '".$user."' ) ORDER BY  cmd__date_devis DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }








    public function magicRequestCMD($string){

      $filtre = str_replace("-" , ' ', $string);
      $filtre = str_replace("'" , ' ' , $filtre);
      $nb_mots_filtre = str_word_count($filtre , 0 , "0123456789");
      $mots_filtre = str_word_count($filtre, 1 ,'0123456789');

      if ($nb_mots_filtre > 0 ) {
        $mode_filtre = true ;
      }else { $mode_filtre = false ;}

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
      cmd__date_cmd,
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
       $request .=  "WHERE ( cmd__id = '".$mots_filtre[0]."' 
        OR cmd__nom_devis LIKE '%".$mots_filtre[0]."%' 
        OR u.prenom LIKE '%".$mots_filtre[0]."%' 
        OR l.cmdl__designation LIKE '%".$mots_filtre[0]."%'
        OR l.cmdl__pn LIKE '%".$mots_filtre[0]."%' 
        OR c.client__societe LIKE '%".$mots_filtre[0]."%' 
        OR c.client__id = '".$mots_filtre[0]."' ) ";

       for ($i=1; $i < $nb_mots_filtre ; $i++) { 
          $request .=  $operateur. " ( cmd__id = '".$mots_filtre[$i]."' 
          OR cmd__nom_devis LIKE '%".$mots_filtre[$i]."%' 
          OR u.prenom LIKE '%".$mots_filtre[$i]."%' 
          OR l.cmdl__designation LIKE '%".$mots_filtre[$i]."%'
          OR l.cmdl__pn LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__societe LIKE '%".$mots_filtre[$i]."%' 
          OR c.client__id = '".$mots_filtre[$i]."' ) ";
       }
       $request .= "AND ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      else {
        $request.=   "AND ( cmd__etat = 'CMD' ) ORDER BY  cmd__date_cmd DESC ,  c.client__societe ASC LIMIT 200  ";
      }
      
      $send = $this->Db->Pdo->query($request);
      $data = $send->fetchAll(PDO::FETCH_OBJ);
      return $data;
    }



}