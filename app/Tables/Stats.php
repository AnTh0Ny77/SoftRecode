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
    $request =$this->Db->Pdo->query("SELECT cmdl__puht as ht , cmdl__qte_fact as qte , cmdl__garantie_puht as htg , cmdl__prestation as presta
    FROM cmd_ligne 
    WHERE cmdl__cmd__id = '".$cmd."'
    ORDER BY cmdl__puht ASC
    ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}


public function returnCmdBetween2Dates($debut , $fin , $abn)
{
    if (!empty($abn)) 
    {
      $stat = "AND cmd__etat = 'VLD' OR cmd__etat = 'VLA' ";
    }
    else 
    {
    $stat = "AND cmd__etat = 'VLD' ";
    }
    $request =$this->Db->Pdo->query("SELECT cmd__id , c.client__id_vendeur 
    FROM cmd 
    LEFT JOIN client as c ON c.client__id  = cmd__client__id_fact
    WHERE  cmd__date_fact > '".$debut."' AND cmd__date_fact < '".$fin."'
    ".$stat."
    ORDER BY cmd__date_fact DESC 
    ");
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
}


public function returnCmdBetween2DatesClientVendeur($debut , $fin , $client , $vendeur ,$abn)
{
    if (!empty($abn)) 
    {
      $stat = "AND (cmd__etat = 'VLD' OR cmd__etat = 'VLA')";
    }
    else 
    {
    $stat = "AND (cmd__etat = 'VLD')";
    }
    if ($client != 'Tous' && $vendeur != 'Tous') 
    {
        $request =$this->Db->Pdo->query("SELECT cmd__id , c.client__id_vendeur
        FROM cmd 
        LEFT JOIN client as c ON c.client__id  = cmd__client__id_fact
        WHERE  cmd__date_fact > '".$debut."' AND cmd__date_fact < '".$fin."'
        AND cmd__client__id_fact = '".$client."'
        AND c.client__id_vendeur = '".$vendeur."'
        ".$stat."
        ORDER BY cmd__date_fact DESC 
        ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    if ($client != 'Tous' && $vendeur = 'Tous') 
    {
        $request =$this->Db->Pdo->query("SELECT cmd__id , c.client__id_vendeur
        FROM cmd 
        LEFT JOIN client as c ON c.client__id  = cmd__client__id_fact
        WHERE  cmd__date_fact > '".$debut."' AND cmd__date_fact < '".$fin."'
        AND cmd__client__id_fact = ".$client."
        ".$stat."
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
        ".$stat."
        ORDER BY cmd__date_fact DESC 
        ");
        
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        
        return $data;
        
    }


    
}


public function camVendeur($debut, $fin , $user)
{

    $request =$this->Db->Pdo->query("SELECT cmd__id , c.client__id_vendeur
        FROM cmd 
        LEFT JOIN client as c ON c.client__id  = cmd__client__id_fact
        WHERE  cmd__date_fact > '".$debut."' AND cmd__date_fact < '".$fin."' AND c.client__id_vendeur = '".$user."'
        ORDER BY cmd__date_fact DESC 
        ");
        $data = $request->fetchAll(PDO::FETCH_OBJ);
        return $data;
}




}