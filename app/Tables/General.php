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
    $recodeRaison = 'Recode By Eurocomputeur';
    $recodeAdresse1 = 'PA de la Siagne, 112 Allee Francois Coli';
    $recodeAdresse2 = '';
    $recodeTel = '0493472500';
    $recodeCP = '06210';
    $recodeCommune = 'Mandelieu la Napoule';
    
    $commande->client__livraison_societe = $this->replaceSpecialChar($commande->client__livraison_societe);
    $commande->client__livraison__adr1 = $this->replaceSpecialChar($commande->client__livraison__adr1);
    $commande->client__livraison__adr2 = $this->replaceSpecialChar($commande->client__livraison__adr2);

    $responseText = ';02008066;'.$commande->cmd__date_envoi.';;;'.$paquets.';'.$poids.';'.$paquets.';'.$poids.';E;;'.$commande->client__livraison_societe.';'.$commande->client__livraison__adr1.';'.$commande->client__livraison__adr2.';'.$commande->client__livraison_cp.';'.$commande->client__livraison_ville.';;;'.$commande->nom__livraison.';'.$commande->nom__livraison.';;'.$commande->fixe__livraison.';'.$commande->gsm__livraison.';'.$commande->mail__livraison.';'.$recodeRaison.';'.$recodeAdresse1.';'.$recodeAdresse2.';'.$recodeCP.';'.$recodeCommune.';'.$recodeTel.';;'.$recodeRaison.';'.$recodeAdresse1.';'.$recodeAdresse2.';'.$recodeCP.';'.$recodeCommune.';'.$recodeTel.';J;;;;;;;;;;
';
    
    return $responseText;
  }
  

}