<?php

namespace App\Methods;

 class Pdfunctions {

 // fontion d'affichage du prix : 
 public static function showPrice($object){
    $barre = '';
    $extension = "";
    $sautDeLigne = "";
    if (!empty($object->prixBarre)) {
       $barre = "<s>". $object->prixBarre ." €</s>";
    }
    if (!empty($object->prix)) {
        $price =  $object->prix ." €";
    }else{ $price =  "00,00 €"; }
    if (!empty($object->xtend)) {
        $sautDeLigne = "<br>";
        foreach($object->xtend as $array=>$value){
            $extension .= "<br>" . $value[1] . " €";
        }
    }
    return $barre . " " . $price . $sautDeLigne . $extension;
}
// fonction d'affichage  prestation :
public static function showPrestation($object){
    $prestation = $object->prestation;
    $extension = "";
    $sautDeLigne = "";
    if (!empty($object->xtend)) {
        $size = sizeof($object->xtend);
        $sautDeLigne = "<br>";
        for ($i=0; $i < $size ; $i++) { 
            $extension .= "<br>garantie";
        }
    }
    return $prestation . $sautDeLigne . $extension;
} 

// fonction d'affichage designation : 
public static function showdesignation($object){
    $designation = $object->designation;
    $extension = "";
    $sautDeLigne = "";
    $sautDecom = "";
    $commentaire = "";
    if (!empty($object->xtend)) {
        $size = sizeof($object->xtend);
        $sautDeLigne = "<br>";
        for ($i=0; $i < $size ; $i++) { 
            $extension .= "<br>extension de garantie";
        }
    }
    if (!empty($object->comClient)) {
        $sautDecom = '<br>';
        $commentaire = $object->comClient;
    }
    return $designation . $sautDeLigne . $extension . $sautDecom .$commentaire;
}
// fonction d'affichage de garantie :
public static function showGarantie($object){
    $garantie = $object->garantie . " mois";
    $extension = "";
    $sautDeLigne = "";
    if (!empty($object->xtend)) {
        $sautDeLigne = "<br>";
        foreach($object->xtend as $array=>$value){
            $extension .= "<br>" . $value[0] . " mois";
        }
    }
    return $garantie . $sautDeLigne . $extension;
}
// fonction d'afficchage de la quatité : 
public static function showQuantite($object){
    $quantité = $object->quantite;
    $extension = "";
    $sautDeLigne = "";
    if (!empty($object->xtend)) {
        $size = sizeof($object->xtend);
        $sautDeLigne = "<br>";
        for ($i=0; $i < $size ; $i++) { 
            $extension .= "<br>" . $quantité;
        }
    }
    return $quantité . $sautDeLigne . $extension;
}
// function d'affichage des farits de ports : 
public static function showPort($post){
    $port = " ";
    if (!empty($post)) {
       $port = $post;
    } 
    else {$port = "00,00";} 
    return $port . " €";
}

// function de calcul du total des extension: 
public static function xTendTotal($xtendArray){
    $priceArray = [[],[],[],[]];
    foreach($xtendArray as $array){
            switch ($array[0]) {
                case '12':
                    array_push($priceArray[0],floatval($array[1]));
                break;
                case '24':
                    array_push($priceArray[1],floatval($array[1]));
                break;
                case '36':
                    array_push($priceArray[2],floatval($array[1]));
                break;
                case '48':
                    array_push($priceArray[3],floatval($array[1]));
                break;
            }
    } 
    return $priceArray;
}
// function 20% 
public static function ttc($price){
    $opex = ($price*20)/100;
    $results = $opex + $price;
    return $results;

}

    

    

}