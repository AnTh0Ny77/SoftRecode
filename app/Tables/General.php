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

  //gere l'export a tnt expedio param : commande (objet), poids (post) , Nb de paquets (post) 
  public function exportTNT($commande, $poids, $paquets)
  {
    $recodeRaison = 'Recode By Eurocomputeur';
    $recodeAdresse1 = 'PA de la Siagne, 112 Allée François Coli';
    $recodeAdresse2 = '';
    $recodeTel = '0493472500';
    $recodeCP = '06210';
    $recodeCommune = 'Mandelieu la Napoule';
    $responseText = ';02008066;'.$commande->cmd__date_envoi.';;Référence Colis;Numero du Colis;'.$poids.';'.$paquets.';POIDS TOTAL;E;'.$commande->client__livraison_societe.';'.$commande->client__livraison__adr1.';'.$commande->client__livraison__adr2.';'.$commande->client__livraison_cp.';'.$commande->client__livraison_ville.';;;;'.$commande->nom__livraison.';'.$commande->nom__livraison.';;'.$commande->fixe__livraison.';'.$commande->gsm__livraison.';'.$commande->mail__livraison.';'.$recodeRaison.';'.$recodeAdresse1.';'.$recodeAdresse2.';'.$recodeCP.';'.$recodeCommune.';'.$recodeTel.';;'.$recodeRaison.';'.$recodeAdresse1.';'.$recodeAdresse2.';'.$recodeCP.';'.$recodeCommune.';'.$recodeTel.';J;;02052015;;;O;;;;;';
    
    return $responseText;
  }
  

}