<?php

namespace App\Methods;

 class Pdfunctions {

 

// fonction d'affichage de la societe : nom (id) adr1 adr2 cp ville : 

public static function showSociete($object){
    if ($object->client__adr2) {
        $text = $object->client__societe . " (" . $object->client__id . ") <br>" .
        $object->client__adr1 . "<br>" . $object->client__adr2 . "<br>" . $object->client__cp . " " . $object->client__ville ;
        return $text ;
    }
    else {
        $text = $object->client__societe . " (" . $object->client__id . ") <br>" .
        $object->client__adr1 . "<br>"  . $object->client__cp . " " . $object->client__ville ;
        return $text ;
    }
   
}

// fontion d'affichage du prix dans View : 
public static function showPriceView($object){
    $barre = '';
    $extension = "";
    $sautDeLigne = "";
    if ($object->devl__prix_barre < 0 ) {
       $barre = "<s>". $object->devl__prix_barre ." €</s>";
    }
    if (!empty($object->devl_puht)) {
        $price =  $object->devl_puht ." €";
    }else{ $price =  "00,00 €"; }
    if (!empty($object->ordre)) {
        $sautDeLigne = "<br>";
        foreach($object->ordre as $array){
            $extension .= "<br>" . number_format(floatVal($array['devg__prix']),2) . " €";
        }
    }
    return $barre . " " . $price . $sautDeLigne . $extension;
}




// fonction d'affichage  prestation dans View :
public static function showPrestationView($object){
    $prestation = strtolower($object->prestaLib);
    $extension = "";
    $sautDeLigne = "";
    if (!empty($object->ordre)) {
        $size = sizeof($object->ordre);
        $sautDeLigne = "<br>";
        for ($i=0; $i < $size ; $i++) { 
            $extension .= "<br>garantie";
        }
    }
    return $prestation . $sautDeLigne . $extension;
} 



// fonction d'affichage designation View : 
public static function showdesignationView($object){
    $designation = $object->devl__designation;
    $extension = "";
    $sautDeLigne = "";
    $sautDecom = "";
    $commentaire = "";
    if (!empty($object->ordre)) {
        $size = sizeof($object->ordre);
        $sautDeLigne = "<br>";
        for ($i=0; $i < $size ; $i++) { 
            $extension .= "<br>extension de garantie";
        }
    }
    if (!empty($object->devl__note_client)) {
        $sautDecom = '<br>';
        $commentaire = $object->devl__note_client;
    }
    return $designation . $sautDeLigne . $extension . $sautDecom .$commentaire;
}









// fonction d'affichage de garantie dans View :
public static function showGarantieView($object){
    if ($object->devl__mois_garantie > 0) {
        $garantie = $object->devl__mois_garantie . " mois";
        $extension = "";
        $sautDeLigne = "";
        if (!empty($object->ordre)) {
            $sautDeLigne = "<br>";
            foreach($object->ordre as $array){
                $extension .= "<br>" . $array['devg__type'] . " mois";
            }
        }
        return $garantie . $sautDeLigne . $extension;
    }
    
}



// fonction d'afficchage de la quatité dans view : 
public static function showQuantiteView($object){
    $quantité = $object->devl_quantite;
    $extension = "";
    $sautDeLigne = "";
    if (!empty($object->ordre)) {
        $size = sizeof($object->ordre);
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
    return floatVal($port) ;
}



// function de calcul du total des extension dans View: 
public static function xTendTotalView($xtendArray){
    $priceArray = [[],[],[],[]];
    foreach($xtendArray as $array){
            switch ($array['devg__type']) {
                case '12':
                    array_push($priceArray[0],floatval($array['devg__prix']));
                break;
                case '24':
                    array_push($priceArray[1],floatval($array['devg__prix']));
                break;
                case '36':
                    array_push($priceArray[2],floatval($array['devg__prix']));
                break;
                case '48':
                    array_push($priceArray[3],floatval($array['devg__prix']));
                break;
            }
    } 
    return $priceArray;
}



// function 20% 
public static function ttc($price){
    
    $opex = floatval(($price*20)/100);
    $results = $opex + $price;
    return $results;

}

    

    

}