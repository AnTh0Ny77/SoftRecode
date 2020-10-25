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


public function WLstatsGlobal($cmd)
{
    $request =$this->Db->Pdo->query("SELECT cmdl__puht as ht , cmdl__qte_fact as qte , cmdl__garantie_puht as htg
    FROM cmd_ligne 
    WHERE cmdl__cmd__id = '".$cmd."'
    AND cmdl__puht > 0 
    ORDER BY cmdl__puht ASC
    ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}


public function returnCmdBetween2Dates($debut , $fin)
{
    $request =$this->Db->Pdo->query("SELECT cmd__id 
    FROM cmd 
    WHERE  cmd__date_fact > '".$debut."' AND cmd__date_fact < '".$fin."'
    ORDER BY cmd__date_fact DESC 
    ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}


public function returnCmdBetween2DatesClientVendeur($debut , $fin , $client , $vendeur)
{
    if ($client != 'Tous' && $vendeur != 'Tous') 
    {
        $request =$this->Db->Pdo->query("SELECT cmd__id , c.client__id_vendeur
        FROM cmd 
        LEFT JOIN client as c ON c.client__id  = cmd__client__id_fact
        WHERE  cmd__date_fact > '".$debut."' AND cmd__date_fact < '".$fin."'
        AND cmd__client__id_fact = '".$client."'
        AND c.client__id_vendeur = '".$vendeur."'
        ORDER BY cmd__date_fact DESC 
        ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    if ($client != 'Tous' && $vendeur = 'Tous') 
    {
        $request =$this->Db->Pdo->query("SELECT cmd__id
        FROM cmd 
        WHERE  cmd__date_fact > '".$debut."' AND cmd__date_fact < '".$fin."'
        AND cmd__client__id_fact = ".$client."
        ORDER BY cmd__date_fact DESC 
        ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    if ($client = 'Tous' && $vendeur != 'Tous') 
    {
        $request =$this->Db->Pdo->query("SELECT cmd__id , c.client__id_vendeur
        FROM cmd 
        LEFT JOIN client as c ON c.client__id  = cmd__client__id_fact
        WHERE  cmd__date_fact > '".$debut."' AND cmd__date_fact < '".$fin."'
        AND c.client__id_vendeur = '".$vendeur."'
        ORDER BY cmd__date_fact DESC 
        ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }


    
}






}