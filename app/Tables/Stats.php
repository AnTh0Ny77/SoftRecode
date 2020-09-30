<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;

class Stats extends Table {

public Database $Db;

public function __construct($db) {
    $this->Db = $db;
}

public function devisATN($com) { 
        $date15 =  date('ymd', strtotime('-15 day'));
   
        $request =$this->Db->Pdo->query("SELECT cmd__etat
        FROM cmd
        WHERE cmd__etat = 'ATN' 
        AND cmd__user__id_devis = " . $com ."
        AND cmd__date_devis > ". $date15 ."
        ORDER BY  cmd__etat DESC LIMIT 200 ");
       
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $request->rowCount();
      
}

public function devisVLD($com) { 
    $date15 =  date('ymd', strtotime('-15 day'));
    $request =$this->Db->Pdo->query("SELECT cmd__etat
    
    FROM cmd
    WHERE cmd__etat = 'CMD' 
    AND cmd__user__id_devis = " . $com ."
    AND cmd__date_devis > ". $date15 ."
    ORDER BY  cmd__etat DESC LIMIT 200 ");
   
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $request->rowCount();
  
}

public function devisAll($com) { 
    $date15 =  date('ymd', strtotime('-15 day'));
    $request =$this->Db->Pdo->query("SELECT cmd__etat
    
    FROM cmd
    WHERE  cmd__user__id_devis = " . $com ."
    AND cmd__date_devis > ". $date15 ."
    ORDER BY  cmd__etat DESC LIMIT 200 ");
   
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $request->rowCount();
  
}

public function devisRFS($com) { 
   
    $request =$this->Db->Pdo->query("SELECT cmd__etat
    
    FROM cmd
    WHERE cmd__etat = 'RFS' 
    AND cmd__user__id_devis = " . $com ."
    ORDER BY  cmd__etat DESC LIMIT 200 ");
   
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $request->rowCount();
  
}








}