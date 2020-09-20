<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Abonnement extends Table 
{ 

    public Database $Db;

    public function __construct($db) 
    {
        $this->Db = $db;
    }


    public function createOne($cmd , $client , $actif , $auto , $presta , $note , $mois )
    {
        $request = $this->Db->Pdo->prepare('INSERT INTO abonnement ( ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_auto,  ab__presta,
        ab__note, ab__mois_engagement)
        VALUES (:ab__cmd__id, :ab__client__id_fact, :ab__actif, :ab__fact_auto, :ab__presta,
        :ab__note, :ab__mois_engagement)');

        $request->bindValue(":ab__cmd__id", $cmd);
        $request->bindValue(":ab__client__id_fact", $client);
        $request->bindValue(":ab__actif", $actif);
        $request->bindValue(":ab__fact_auto", $auto);
        $request->bindValue(":ab__presta",$presta);
        $request->bindValue(":ab__note", $note);   
        $request->bindValue(":ab__mois_engagement", $mois);

        $request->execute();

        $idABN = $this->Db->Pdo->lastInsertId();

        return $idABN;
    }

    public function getAll()
    {
        $request =$this->Db->Pdo->query("SELECT  ab__cmd__id, ab__client__id_fact,
        ab__actif, ab__fact_auto,  ab__presta,
        FROM abonnement
        ORDER BY  ab__cmd__id DESC  LIMIT 200 ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }
}