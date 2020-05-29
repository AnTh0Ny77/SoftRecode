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
            $extension .= "<br>" . number_format(floatVal($array['devg__prix']),2 , ',' , ' ') . " €";
        }
    }
    return $barre . " " . $price . $sautDeLigne . $extension;
}


//fonction d'affichage de l'etat : 
public static function showEtat($object){
    $none = "";
    if ($object == 'Non Concerné') {
       
       return $none;
    }
    else {
        return $object;
    }

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




public static function magicLine($object){
    // presation
    $presta = strtolower($object->prestaLib);

    // designation
    if (!empty($object->devl__note_client)) {
        $designation =  $object->devl__designation . " <br> ".$object->devl__note_client;
    }
    else { $designation = $object->devl__designation; }

    
        $garantie = $object->devl__mois_garantie . " mois";
  
   

     // prix et prix barre 
    if ($object->devl__prix_barre > 0 ) {
        $barre = "<s>". number_format(floatVal($object->devl__prix_barre),2 , ',',' ') ." €</s>";
     } 
     else {
        $barre = '';
     }
    

     if (!empty($object->devl_puht)) {
        $price = number_format(floatVal($object->devl_puht),2 , ',',' ')  ." €";
    }
    else{ $price =  "offert"; }

     //quantite 
    $quantité = $object->devl_quantite;



   

    $fisrtLine = "<tr style='font-size: 85%; font-style: italic;'>";

    $firstCell = "<td valign='top' style=' width: 13%; text-align: left; border-bottom: 1px #ccc solid'>" . $presta  . "</td>";

    $secondCell = "<td valign='top' style='  width: 33%; text-align: left; border-bottom: 1px #ccc solid ; padding-bottom:15px'>"  . $designation. "</td>";

    if ($object->kw__lib == 'Non Concerné') {
        $thirdCell =  "<td valign='top' style='  color: white ; text-align: left; border-bottom: 1px #ccc solid'>" .$object->kw__lib ."</td>";
    }
    else {
        $thirdCell =  "<td valign='top' style=' text-align: left; border-bottom: 1px #ccc solid'>" .$object->kw__lib ."</td>";
    }

    if ($object->devl__mois_garantie > 0){
        $fourthCell = "<td valign='top' style=' text-align: center; border-bottom: 1px #ccc solid'>" . $garantie ." </td>";
    }
    else {
        $fourthCell = "<td valign='top' style='  color: white; text-align: center; border-bottom: 1px #ccc solid'>" . $garantie ." </td>";
    }

    

    $fifthCell ="<td valign='top' style='text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";

    $lastCell = "<td valign='top' style=' width: 15%; text-align: right;  border-bottom: 1px #ccc solid; padding-bottom:15px'>" . $barre . " " . $price ."</td>" ;

    $endline = "<br></tr> ";

    //extensions 
    $extension = "";
    if (!empty($object->ordre2)) {
        $fisrtLine = "<tr style='font-size: 85%; font-style: italic;'>";

        $firstCell = "<td valign='top' style=' width: 13%; text-align: left;'>" . $presta  . "</td>";
    
        $secondCell = "<td valign='top' style=' width: 33%; text-align: left;  padding-bottom:15px'>"  . $designation. "</td>";
    
        if ($object->kw__lib == 'Non Concerné') {
            $thirdCell =  "<td valign='top' style='  color: white ; text-align: left; '>" .$object->kw__lib ."</td>";
        }
        else {
            $thirdCell =  "<td valign='top' style=' text-align: left; '>" .$object->kw__lib ."</td>";
        }

         if ($object->devl__mois_garantie > 0){
        $fourthCell = "<td valign='top' style=' text-align: center; '>" . $garantie ." </td>";
            }
         else {
        $fourthCell = "<td valign='top' style='  color: white; text-align: center; '>" . $garantie ." </td>";
            }
    
     
    
        $fifthCell ="<td valign='top' style=' width: 7%; text-align: center;  '>" .$quantité ."</td>";
    
        $lastCell = "<td valign='top' style=' width: 17%; text-align: right;   padding-bottom:15px'>" . $barre . " " . $price ."</td>" ;
    
        
        $endline = "</tr>";
        $counter = 0 ;
        foreach($object->ordre2 as $array){
            $counter = $counter + 1; 
            $extensionLine = "";
            $secondLine = "<tr style='font-size: 85%; font-style: italic;'>" ;
            $firstCell2 = "<td valign='top' style=' width: 13%; text-align: left; '>garantie</td>";
            if ($presta == "reparation") {
                $secondCell2 = "<td valign='top' style=' width: 33%; text-align: left;  '>mise sous garantie du matériel réparé optionnelle - retour atelier & renvoi sous 24h </td>";
            }
            else {
                $secondCell2 = "<td valign='top' style=' width: 33%; text-align: left;  ;'> extension de garantie</td>";
            }
            
            $thirdCell2 =  "<td valign='top' style=' width: 13%; text-align: left; '></td>";
            $fourthCell2 = "<td valign='top' style=' text-align: center; '>" . $array['devg__type'] ." mois </td>";
            $fifthCell2 ="<td valign='top' style=' width: 7%; text-align: center;  '>" .$quantité ."</td>";
            $lastCell2 = "<td valign='top' style=' width: 17%; text-align: right;   '>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
            $endSecondLine = "</tr> ";
            if ( $array === end($object->ordre2)) {
                $secondLine = "<tr style='font-size: 85%; font-style: italic;'>" ;
                $firstCell2 = "<td valign='top' style=' text-align: left; border-bottom: 1px #ccc solid'>garantie</td>";
                if ($presta == "reparation") {
                    $secondCell2 = "<td valign='top' style=' width: 33%; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:15px '>mise sous garantie du matériel réparé optionnelle - retour atelier & renvoi sous 24h </td>";
                }
                else {
                    $secondCell2 = "<td valign='top' style=' width: 33%; text-align: left; border-bottom: 1px #ccc solid  ; padding-bottom:15px'> extension de garantie</td>";
                }
                $thirdCell2 =  "<td valign='top' style=' width: 13%; text-align: left; border-bottom: 1px #ccc solid'></td>";
                $fourthCell2 = "<td valign='top' style=' text-align: center; border-bottom: 1px #ccc solid'>" . $array['devg__type'] ." mois </td>";
                $fifthCell2 ="<td valign='top' style=' width: 7%; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
                $lastCell2 = "<td valign='top' style=' width: 17%; text-align: right;  border-bottom: 1px #ccc solid; padding-bottom:15px'>" . number_format(floatVal($array['devg__prix']),2 , "," , " ") ." €</td>" ;
            }
            $extensionLine = $secondLine . $firstCell2 . $secondCell2 . $thirdCell2 . $fourthCell2 . $fifthCell2 . $lastCell2 . $endSecondLine ;
            $extension .= $extensionLine ;
            $counter = 0;
        }
    }

    return $fisrtLine . $firstCell . $secondCell . $thirdCell . $fourthCell . $fifthCell . $lastCell . $endline . $extension;
   


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


// function du calcul des extension de garanties avec keyword : 
public static function magicXtend($lignes, $garantiesArray , $prixTotal ){
    // pour chaque type de garanties dans keyword push dans le tableau global array ;
    $globalArray = array();
    foreach ($garantiesArray as  $value) {
        // création d'un tableau multidimensionnel pour chaque valeur présente dans le tableau : 
        $type = intval($value->kw__value);
        $globalArray[$type]  = [$type];
    }  
    //variable $marqueurServices repésente les services présent dans les lignes de devis , ex: port :
    $marqueurServices = 0 ;
    // pour sur chaque ligne de garantie 
    foreach ($lignes as $ligne ) { 
        // si il s'agit d''un service incremente le marqueur : 
        if ($ligne->famille == 'SER') {
            $marqueurServices += 1;
        }
        // variable $xtend déclaré pour chaque tableau d'extension de garanties : 
        $xtend =  $ligne->ordre2;
        // si il ne s'agit pas d'un service pour sur chaque tableau d'extension du tableau des extensions de  garantie : 
        if ($ligne->famille != 'SER') {
            foreach ( $xtend as $array) {
                //  sur chaque valeur du tableau des garantie dans keyword : 
                foreach ($globalArray as  $value) {
                    // si la valeur du nombre de mois dans l'extension correspond à la valeur du  tableau de la liste keyword : 
                    if ( intval($array['devg__type']) == $value[0] ) {
                        // la variable $results est le résultat du prix de l'extension correspondante X la quantité 
                        $results = floatval($array['devg__prix']) * intval($ligne->devl_quantite);
                        //  pousse dans le tableau correspondant à la valeur de la garantie :
                        array_push( $globalArray[$value[0]] , $results );     
                    } 
                    else {
                        // sinon détruit la valeur : 
                        unset($value);
                    }
                }    
            } 
        }
    }
   // égal le nombre de ligne - les services :
   $compare = sizeof($lignes) - $marqueurServices;
    //pour chaque tableau de résultat présent dans le tableau global 
    foreach ($globalArray as  $resultsArray) {
       // si la taille du tableau correspond au nombre de ligne +1 (index 0 )alors chaque ligne possède la garantie : 
        if (sizeof($resultsArray)  ==  $compare + 1) {
            //on retire l'index 0 corespondant à la valeur de la garantie :
            $prixTemp =  floatval(array_sum($resultsArray) - $resultsArray[0]);
            // on additionne au prix total  :
            $prix = $prixTemp + $prixTotal;
            // renvoi dans le template html => 
            echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'> garantie " .$resultsArray[0] ." mois </td><td style='text-align: center'><strong>  "
            . number_format($prix,2  ,',', ' ').
             " €</strong></td><td style='text-align: right'> " 
             .number_format(Pdfunctions::ttc( floatval($prix)),2 ,',', ' ').
             " €</td></tr>";
        }       
    }

     
    

   
    

}





// function 20% 
public static function ttc($price){
   
    $opex = floatval(($price*20)/100);
    $results = $opex + $price;
    return $results;

}

    

    

}