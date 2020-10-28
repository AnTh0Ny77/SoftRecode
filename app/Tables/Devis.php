<?php


namespace App\Tables;
use App\Tables\Table;
use App\Database;
use App\Methods\Pdfunctions;
use PDO;


class Devis extends Table {
  
  public Database $Db;
  
  public function __construct($db) {
    $this->Db = $db;
  }



  public function createDevis($date, $user, $clientF, $clientL, $contactF, $contactL, $noteC, $noteI, $model, $tva, $nom ,$code)
  {
    $request = $this->Db->Pdo->prepare('INSERT INTO cmd 
    (cmd__date_devis, 
    cmd__user__id_devis, 
    cmd__client__id_fact, 
    cmd__client__id_livr, 
    cmd__contact__id_fact, 
    cmd__contact__id_livr, 
    cmd__code_cmd_client, 
    cmd__note_client,
    cmd__note_interne,
    cmd__etat,
    cmd__modele_devis,
    cmd__tva,
    cmd__nom_devis)
    VALUES (:cmd__date_devis, 
    :cmd__user__id_devis, 
    :cmd__client__id_fact, 
    :cmd__client__id_livr, 
    :cmd__contact__id_fact, 
    :cmd__contact__id_livr, 
    :cmd__code_cmd_client, 
    :cmd__note_client,
    :cmd__note_interne,
    :cmd__etat,
    :cmd__modele_devis,
    :cmd__tva,
    :cmd__nom_devis)');
    $request->bindValue(":cmd__date_devis", $date);
    $request->bindValue(":cmd__user__id_devis", $user);
    $request->bindValue(":cmd__client__id_fact", $clientF);
    $request->bindValue(":cmd__client__id_livr", $clientL);
    $request->bindValue(":cmd__contact__id_fact", $contactF);
    $request->bindValue(":cmd__contact__id_livr", $contactL);
    $request->bindValue(":cmd__note_client", $noteC);
    $request->bindValue(":cmd__note_interne", $noteI);
    $request->bindValue(":cmd__code_cmd_client", $code);
    $request->bindValue(":cmd__modele_devis", $model);
    $request->bindValue(":cmd__tva", $tva);
    $request->bindValue(":cmd__nom_devis", $nom);
    $request->bindValue(":cmd__etat", 'PBL');
    $request->execute();
    $id = $this->Db->Pdo->lastInsertId();
    return $id;
  }

}