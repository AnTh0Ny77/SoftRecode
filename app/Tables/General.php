<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;


class General extends Table 
{
  public Database $Db;

  //constructeur
  public function __construct($db) 
  {
    $this->Db = $db;
  }

  //fonction générique qui update la colonne voulue dans la table voulue:
  // 1-> table,
  // 2-> data, 
  // 5 -> colone de la condition,
  // 6 -> clause  
  public function updateAll( string $table, $data, string $column, string $condition, string $clause )
  {
    $update = $this->Db->Pdo->prepare
      ('UPDATE  '.$table.'
      SET '. $column .' = ? 
      WHERE '.$condition.' = ?');
        
    $update->execute([$data, $clause]);

  }

  public function findBy(string $table , string $field , $search ,int $limit , ?string $order ) : array 
  {
    $SQL = 'SELECT * 
		FROM '. $table. '
		WHERE ? =  ? 
    LIMIT ? 
    ORDER BY ? 
    ';
    $request = $this->Db->Pdo->prepare($SQL);
    $request->execute(array($field ,$search , $limit  , $order));
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

 public function replaceSpecialChar($str) {
    $ch0 = array( 
            "œ"=>"oe",
            "Œ"=>"OE",
            "æ"=>"ae",
            "Æ"=>"AE",
            "À" => "A",
            "Á" => "A",
            "Â" => "A",
            "à" => "A",
            "Ä" => "A",
            "Å" => "A",
            "à" => "a",
            "á" => "a",
            "â" => "a",
            "à" => "a",
            "ä" => "a",
            "å" => "a",
            "Ç" => "C",
            "ç" => "c",
            "Ð" => "D",
            "È" => "E",
            "É" => "E",
            "Ê" => "E",
            "Ë" => "E",
            "è" => "e",
            "é" => "e",
            "ê" => "e",
            "ë" => "e",
            "Ì" => "I",
            "Í" => "I",
            "Î" => "I",
            "Ï" => "I",
            "Ñ" => "N",
            "ñ" => "n",
            "Ò" => "O",
            "Ó" => "O",
            "Ô" => "O",
            "Õ" => "O",
            "Ö" => "O",
            "Ø" => "O",
            "ò" => "o",
            "ó" => "o",
            "ô" => "o",
            "õ" => "o",
            "ö" => "o",
            "ø" => "o",
            "ð" => "o",
            "Ù" => "U",
            "Ú" => "U",
            "Û" => "U",
            "Ü" => "U",
            "ú" => "u",
            "û" => "u",
            "ü" => "u",
            "Ý" => "Y",
            "?" => "Y",
            "ý" => "y",
            "ÿ" => "y"
            );
        $str = strtr($str,$ch0);
        return $str;
    }

  //gere l'export a tnt expedio param : commande (objet), poids (post) , Nb de paquets (post) 
  public function exportTNT($commande, $poids, $paquets)
  {
    $recodeRaison = 'Recode By Eurocomputer';
    $recodeAdresse1 = 'PA de la Siagne, 112 Allee Francois Coli';
    $recodeAdresse2 = '';
    $recodeTel = '0493472500';
    $recodeCP = '06210';
    $recodeCommune = 'Mandelieu la Napoule';

    $nomContact = $commande->civ__Livraison . ' ' . $commande->nom__livraison . ' ' . $commande->prenom__livraison ;
    
    $commande->client__livraison_societe = $this->replaceSpecialChar($commande->client__livraison_societe);
    $commande->client__livraison__adr1 = $this->replaceSpecialChar($commande->client__livraison__adr1);
    $commande->client__livraison__adr2 = $this->replaceSpecialChar($commande->client__livraison__adr2);

    if (intval($paquets) > 1) 
    {
      $pounds = $poids/$paquets;
      $responseText = "";
      for ($i=0; $i <= $paquets; $i++) 
      { 
       $responseText .= ';02008066;'.$commande->cmd__date_envoi.';;'.$commande->devis__id .';'.$i.';'.$pounds.';'.$paquets.';'.$poids.';E;;'.$commande->client__livraison_societe.';'.$commande->client__livraison__adr1.';'.$commande->client__livraison__adr2.';'.$commande->client__livraison_cp.';'.$commande->client__livraison_ville.';;;'.$nomContact.';;;'.$commande->telLivraion.';;'.$commande->mail__livraison.';'.$recodeRaison.';'.$recodeAdresse1.';'.$recodeAdresse2.';'.$recodeCP.';'.$recodeCommune.';'.$recodeTel.';;'.$recodeRaison.';'.$recodeAdresse1.';'.$recodeAdresse2.';'.$recodeCP.';'.$recodeCommune.';'.$recodeTel.';J;;;;;;;;;;
';
      }
     
    }
    else
    {
      $responseText = ';02008066;'.$commande->cmd__date_envoi.';;'.$commande->devis__id .';'.$paquets.';'.$poids.';'.$paquets.';'.$poids.';E;;'.$commande->client__livraison_societe.';'.$commande->client__livraison__adr1.';'.$commande->client__livraison__adr2.';'.$commande->client__livraison_cp.';'.$commande->client__livraison_ville.';;;'.$nomContact.';;;'.$commande->telLivraion.';;'.$commande->mail__livraison.';'.$recodeRaison.';'.$recodeAdresse1.';'.$recodeAdresse2.';'.$recodeCP.';'.$recodeCommune.';'.$recodeTel.';;'.$recodeRaison.';'.$recodeAdresse1.';'.$recodeAdresse2.';'.$recodeCP.';'.$recodeCommune.';'.$recodeTel.';J;;;;;;;;;;
';
    }

    
    
    return $responseText;
  }



  public function restore()
  {
    ini_set('memory_limit', '-1');
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
    cmd__nom_devis, cmd__modele_devis , cmd__date_fact , 
    cmd__date_cmd, cmd__date_envoi, cmd__code_cmd_client, cmd__tva, cmd__user__id_cmd, LPAD(cmd__id_facture ,7,0) as cmd__id_facture ,
    cmd__modele_facture, cmd__id_facture , cmd__date_fact, cmd__trans, cmd__mode_remise, cmd__report_xtend,
    k.kw__lib,
    t.contact__nom, t.contact__prenom, t.contact__email,
    c.client__societe, c.client__adr1 , c.client__ville, c.client__cp,  c.client__tel , 
    c2.client__societe as client__livraison_societe,
    c2.client__ville as client__livraison_ville,
    c2.client__cp as client__livraison_cp , 
    c2.client__adr1 as client__livraison__adr1 , 
    c2.client__adr2 as client__livraison__adr2 , c2.client__tel as telLivraion,
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
    WHERE cmd__id_facture = 4210930");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }
  

}