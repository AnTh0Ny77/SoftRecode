<?php

namespace App\Tables;

use App\Tables\Table;
use App\Database;
use PDO;

class Client extends Table
{

    public string $Table = 'client';
    public Database $Db;


    public function __construct($db)
    {
        $this->Db = $db;
    }

    public function getAll()
    {
        $request = $this->Db->Pdo->query('SELECT  LPAD(client__id,6,0) as client__id, client__societe ,  client__ville , client__cp  FROM client WHERE client__id  > 10 ORDER BY client__societe ASC LIMIT 50000');
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function get_client_devis()
    {
        $request = $this->Db->Pdo->query(
            'SELECT  LPAD(client__id,6,0) as client__id, client__societe ,  client__ville , client__cp  FROM client WHERE client__id  > 10  ORDER BY client__id DESC '
        );
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function getOne($id)
    {
        $request = $this->Db->Pdo->query("SELECT LPAD(client__id,6,0) as client__id,  client__societe , client__adr1 ,
         client__adr2, client__cp , client__ville , client__tel , client__tva_intracom , 
         client__id_vendeur , client__fax, client__comment , client__tva , 
         client__date_crea, client__dt_last_modif , client__memo_config , client__pays , client__bloque ,
         u.prenom as prenom_vendeur, u.nom as nom_vendeur ,
         k.kw__lib as lib__tva , k.kw__info as taux__tva
        FROM client 
        LEFT JOIN utilisateur as u ON u.id_utilisateur =  client__id_vendeur
        LEFT JOIN keyword as k ON ( k.kw__type = 'tva' and k.kw__value = client__tva ) 
        WHERE client__id = '" . $id . "'");
        $data = $request->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    public function insertOne($name, $adresse, $adresse2, $cp, $ville)
    {
        $request = $this->Db->Pdo->prepare('INSERT INTO ' . $this->Table . "(client__societe , client__adr1 , client__adr2, client__cp , client__ville )
     VALUES (:societe, :adr1, :adr2, :cp, :ville)");
        $request->bindValue(":societe", mb_strtoupper($name, 'UTF8'));
        $request->bindValue(":adr1", $adresse);
        $request->bindValue(":adr2", $adresse2);
        $request->bindValue(":cp", $cp);
        $request->bindValue(":ville", $ville);
        $request->execute();
        return $this->Db->Pdo->lastInsertId();
    }

    public function create_one($societe, $adr1, $adr2, $cp, $ville, $tel, $fax,  $tva, $intracom, $comm, $vendeur, $pays)
    {
        $request = $this->Db->Pdo->prepare('INSERT INTO ' . $this->Table . "
    (client__societe , client__adr1 , client__adr2, client__cp , client__ville , client__tel,
     client__fax, client__tva , client__tva_intracom , client__comment, client__date_crea , client__dt_last_modif  , client__id_vendeur , client__pays)
     VALUES (:societe, :adr1, :adr2, :cp, :ville, :tel, :fax,  :tva, :intracom, :comment, :date, :last_modif , :vendeur , :pays )");

        $date = date("Y-m-d H:i:s");

        $request->bindValue(":societe", mb_strtoupper($societe, 'UTF8'));
        $request->bindValue(":pays", $pays);
        $request->bindValue(":adr1", $adr1);
        $request->bindValue(":adr2", $adr2);
        $request->bindValue(":cp", $cp);
        $request->bindValue(":ville", mb_strtoupper($ville, 'UTF8'));
        $request->bindValue(":tel", $tel);
        $request->bindValue(":fax", $fax);
        $request->bindValue(":tva", $tva);
        $request->bindValue(":intracom", $intracom);
        $request->bindValue(":comment", $comm);
        $request->bindValue(":date", $date);
        $request->bindValue(":last_modif", $date);
        $request->bindValue(":vendeur", $vendeur);
        $request->execute();
        return $this->Db->Pdo->lastInsertId();
    }

    public function getSpecials()
    {
        $request = $this->Db->Pdo->query("SELECT LPAD(client__id,6,0) as client__id,  
  client__societe , client__adr1 , client__adr2, client__cp , client__ville , client__tel , client__tva_intracom  
  FROM " . $this->Table . " WHERE client__id  < 10 ");
        $data = $request->fetchALL(PDO::FETCH_OBJ);
        return $data;
    }

    public function search_client($recherche)
    {
        $filtre = str_replace("-", ' ', $recherche);
        $filtre = str_replace("'", ' ', $filtre);
        $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");

        $mots_filtre = str_word_count($filtre, 1, '0123456789');

        switch ($nb_mots_filtre) {
            case  0:
                $mode_filtre = false;
                break;

            default:
                $mode_filtre = true;
                break;
        }


        $operateur = "AND ";
        $request = "SELECT  LPAD(client__id,6,0) as client__id, client__societe ,  client__ville , client__cp  
    FROM client 
    WHERE client__id  > 10 ";

        if ($mode_filtre) {
            $request .=   $operateur . "( CONCAT('0000',client__id) LIKE '%" . $mots_filtre[0] . "%' 
        OR client__societe LIKE '%" . $mots_filtre[0] . "%' 
        OR client__ville LIKE '%" . $mots_filtre[0] . "%' 
        OR client__cp LIKE '%" . $mots_filtre[0] . "%') ";

            for ($i = 1; $i < $nb_mots_filtre; $i++) {
                $request .=  $operateur . " ( client__id LIKE '%" . $mots_filtre[$i] . "%' 
        OR client__societe LIKE '%" . $mots_filtre[$i] . "%' 
        OR client__ville LIKE '%" . $mots_filtre[$i] . "%' 
        OR client__cp LIKE '%" . $mots_filtre[$i] . "%') ";
            }
            $request .= "ORDER BY  client__societe DESC  LIMIT 7  ";
        } else {
            $request .=  "ORDER BY  client__societe DESC  LIMIT 7 ";
        }

        $send = $this->Db->Pdo->query($request);
        $data = $send->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function search_client_devis($recherche)
    {
        $filtre = str_replace("-", ' ', $recherche);
        $filtre = str_replace("'", ' ', $filtre);
        $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");

        $mots_filtre = str_word_count($filtre, 1, '0123456789');

        switch ($nb_mots_filtre) 
        {
            case  0:
                $mode_filtre = false;
                break;

            default:
                $mode_filtre = true;
                break;
        }


        $operateur = "AND ";
        $request = "SELECT  LPAD(client__id,6,0) as client__id, client__societe ,  client__ville , client__cp  , client__adr1 , client__dt_last_modif , client__tel,
        u.prenom as prenom_vendeur, u.nom as nom_vendeur 
        FROM client 
        LEFT JOIN utilisateur as u ON u.id_utilisateur =  client__id_vendeur
        WHERE client__id  > 10 ";

        if ($mode_filtre) {
            $request .=   $operateur . "( CONCAT('0000',client__id) LIKE '%" . $mots_filtre[0] . "%' 
        OR client__societe LIKE '%" . $mots_filtre[0] . "%' 
        OR client__ville LIKE '%" . $mots_filtre[0] . "%' 
        OR u.nom LIKE '%" . $mots_filtre[0] . "%' 
        OR client__cp LIKE '%" . $mots_filtre[0] . "%') ";

            for ($i = 1; $i < $nb_mots_filtre; $i++) {
                $request .=  $operateur . " ( client__id LIKE '%" . $mots_filtre[$i] . "%' 
        OR client__societe LIKE '%" . $mots_filtre[$i] . "%' 
        OR client__ville LIKE '%" . $mots_filtre[$i] . "%' 
        OR u.nom LIKE '%" . $mots_filtre[$i] . "%' 
        OR client__cp LIKE '%" . $mots_filtre[$i] . "%') ";
            }
            $request .= "ORDER BY  client__societe ASC  LIMIT 30  ";
        } else {
            $request .=  "ORDER BY  client__societe ASC  LIMIT 30 ";
        }

        $send = $this->Db->Pdo->query($request);
        $data = $send->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }
}
