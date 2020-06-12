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




public static function magicLine($arrayLigne){
	// variables des tailles de cellules afin de pouvoir regler la largeur de la table facilement :
	$firstW = '80px';
	$secondW = '290px';
	$thirdW ='100px';
	$fourthW = '80px';
	$fifthW = '40px';
	$lastW= '70px';
	// variable type et garantie [ en fonction de l'existence des valeurs ]
	$stringEtat = "Type";
	$stringGarantie = "Garantie";
	// paddind de la premiere ligne : 
	$firstPadding = '0px';
	// different compteur pour conditions dans les itérations 
	$countService = 0 ;
	$countEtat = 0 ;
	$countGarantie = 0 ;
	$countPadding= 0 ;
	
	$table = "";

	$arrayReparation = []; 

	// boucle sur le tableau de ligne de devis 
	foreach ($arrayLigne as $object) {


		$countPadding += 1 ;
		$presta = strtolower($object->prestaLib);
		// on compte le services 
		if ($object->famille == 'SER') {
			$countService += 1 ;
		}
		// pour la premiere ligne de la table le padding-top est de 15px ; 
		if ($countPadding == 1 ) {
			$firstPadding = '15px';
		}
		else { $firstPadding = '0px';}
		
	// designation et garantie
	if (!empty($object->devl__note_client)) {
		$designation =  $object->devl__designation .'<span style="margin-top: -10px;">'. $object->devl__note_client .'</span>';
	}
	else { $designation = $object->devl__designation; }
	$garantie = $object->devl__mois_garantie . " mois";
  
	// prix et prix barre 
	if ($object->devl__prix_barre > 0 ) {
		$barre = "<s>". number_format(floatVal($object->devl__prix_barre),2 , ',',' ') ." €</s><br>";
	 } 
	else { $barre = '';}
	if (!empty($object->devl_puht)) {
		$price = number_format(floatVal($object->devl_puht),2 , ',',' ')  ." €";
	}
	else{ $price =  "offert"; }

	//quantite 
	$quantité = $object->devl_quantite;

	$fisrtLine = "<tr style='font-size: 95%; font-style: italic;'>";

	$firstCell = "<td valign='top' style='padding-top:".$firstPadding."; width: ".$firstW.";  max-width: ".$firstW."; text-align: left; border-bottom: 1px #ccc solid; '>" . $presta  . "</td>";

	$secondCell = "<td valign='top' class='NoBR' style='padding-top:".$firstPadding.";  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid ;  padding-bottom:15px'>"  . $designation. "</td>";

	if ($object->devl__etat == 'NC.') {
		
		$thirdCell =  "<td valign='top' style='padding-top:".$firstPadding.";  width: ".$thirdW."; max-width: ".$thirdW."; color: white ; text-align: center; border-bottom: 1px #ccc solid'>" .$object->kw__lib ."</td>";
	}
	else {
		$countEtat += 1 ;
		$thirdCell =  "<td valign='top' style='padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; border-bottom: 1px #ccc solid'>" .$object->kw__lib ."</td>";
	}
	if ($object->devl__mois_garantie > 0){
		$countGarantie += 1 ;
		$fourthCell = "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: center; border-bottom: 1px #ccc solid'>" . $garantie ." </td>";
	}
	else {
		$fourthCell = "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; border-bottom: 1px #ccc solid'>" . $garantie ." </td>";
	}
	$fifthCell ="<td valign='top' style='padding-top:".$firstPadding."; width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
	$lastCell = "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW."; text-align: right;  border-bottom: 1px #ccc solid; padding-bottom:15px'>" . $barre . " " . $price ."</td>" ;
	$endline = "<br></tr> ";

	//extensions 
	$extension = "";
	if (!empty($object->ordre2)) {

	$fisrtLine = "<tr style='font-size: 95%; font-style: italic;'>";
	$firstCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>" . $presta  . "</td>";
	$secondCell = "<td valign='top' class='NoBR' style='  padding-top:".$firstPadding.";  width: ".$secondW."; max-width: ".$secondW."; text-align: left;  padding-bottom:15px'>"  . $designation. "</td>";

	if ($object->devl__etat == 'NC.') {
		
		$thirdCell =  "<td valign='top' style=' padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW.";  color: white ; text-align: center; '>" .$object->kw__lib ."</td>";
	}
	else {
		$countEtat += 1 ;
		$thirdCell =  "<td valign='top' style='padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; '>" .$object->kw__lib ."</td>";
	}

	if ($object->devl__mois_garantie > 0){
		$countGarantie += 1 ;
		$fourthCell = "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  text-align: center; '>" . $garantie ." </td>";
	}
	else {
		$fourthCell = "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; '>" . $garantie ." </td>";
	}
	
	$fifthCell ="<td valign='top' style='padding-top:".$firstPadding."; width: ".$fifthW."; max-width: ".$fifthW."; text-align: center;  '>" .$quantité ."</td>";
	
	$lastCell = "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW.";  text-align: right;   padding-bottom:15px'>" . $barre . " " . $price ."</td>" ;
	 
	$endline = "</tr>";
	$counter = 0 ;

	foreach($object->ordre2 as $array){
		$counter = $counter + 1; 
		$extensionLine = "";
		$secondLine = "<tr style='font-size: 95%; font-style: italic;'>" ;
		$firstCell2 = "<td valign='top' style=' width:".$firstW."; max-width: ".$firstW."; text-align: left; '>garantie</td>";

		if ($object->devl__type == "REP") {
			$secondCell2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW.";  text-align: left;  '>mise sous garantie du matériel réparé optionnelle - retour atelier & renvoi sous 24h </td>";
		}
		else {
			if (is_int(intval($array['devg__type'])/12) ) {
				$secondCell2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12)." ans </td>";
			}
			else {
				$secondCell2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie</td>";
			}
				
		}     
		$thirdCell2 =  "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: left; '></td>";
		$fourthCell2 = "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW."; text-align: center; '>" . $array['devg__type'] ." mois </td>";
		$fifthCell2 ="<td valign='top' style=' width:".$fifthW."; max-width: ".$fifthW."; text-align: center;  '>" .$quantité ."</td>";
		$lastCell2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;   '>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
		$endSecondLine = "</tr> ";

		if ( $array === end($object->ordre2)) 
		{
			$secondLine = "<tr style='font-size: 95%; font-style: italic;'>" ;
			$firstCell2 = "<td valign='top' style=' width:".$firstW."; text-align: left; border-bottom: 1px #ccc solid'>garantie</td>";
			if ($presta == "reparation") {
				$secondCell2 = "<td valign='top' style=' width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:15px '>mise sous garantie du matériel réparé optionnelle - retour atelier & renvoi sous 24h </td>";
		}
		else 
		{
			if (is_int(intval($array['devg__type'])/12) ) {
				$secondCell2 = "<td valign='top' style=' border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12)." ans </td>";
			}
			else {
				$secondCell2 = "<td valign='top' style='border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie</td>";
			}
		}

		$thirdCell2 =  "<td valign='top' style=' width: ".$thirdW."; text-align: left; border-bottom: 1px #ccc solid'></td>";
		$fourthCell2 = "<td valign='top' style=' width: ".$fourthW."; text-align: center; border-bottom: 1px #ccc solid'>" . $array['devg__type'] ." mois </td>";
		$fifthCell2 ="<td valign='top' style=' width:".$fifthW."; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
		$lastCell2 = "<td valign='top' style=' width:".$lastW."; text-align: right;  border-bottom: 1px #ccc solid; padding-bottom:15px'>" . number_format(floatVal($array['devg__prix']),2 , "," , " ") ." €</td>" ;
		}
		$extensionLine = $secondLine . $firstCell2 . $secondCell2 . $thirdCell2 . $fourthCell2 . $fifthCell2 . $lastCell2 . $endSecondLine ;
		$extension .= $extensionLine ;
		$counter = 0;
		}
	}

	$table .=  $fisrtLine . $firstCell . $secondCell . $thirdCell . $fourthCell . $fifthCell . $lastCell . $endline . $extension;
	}
	if ($countEtat - $countService  <= 0 ) 
		$stringEtat = '';
	
	if ($countGarantie  <= 0) 
		$stringGarantie = '';


	$tete =  '<tr style=" margin-top : 50px;  background-color: #dedede; ">
	<td style=" text-align: left;   padding-top: 4px; padding-bottom: 4px;">Prestation</td>
	<td style=" text-align: left; padding-top: 4px; padding-bottom: 4px;">Désignation</td>
	<td style="text-align: center; padding-top: 4px; padding-bottom: 4px;">'.$stringEtat.'</td>
	<td  style=" text-align: center; padding-top: 4px; padding-bottom: 4px;">'.$stringGarantie.'</td>
	<td style="text-align: center; padding-top: 4px; padding-bottom: 4px;">Qté</td>
	<td style="text-align: right; ; padding-top: 4px; padding-bottom: 4px;">P.u € HT</td>
	</tr> ';

	echo $tete . $table ;
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


// fonction d'affichage du total à la con : 
public static function totalCon($lignes , $garantieArray , $prixTotal , $tva){
	$globalArray = array();
	foreach ($garantieArray as  $value) {
		// création d'un tableau multidimensionnel pour chaque valeur présente dans le tableau : 
		$type = intval($value->kw__value);
		$globalArray[$type]  = [$type];
	}  

		// pour sur chaque ligne de garantie 
		foreach ($lignes as $ligne ) { 
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

		$marqueurPresta = ' <input type="checkbox"> garantie standard';
		$marqueurType = '';
		foreach ($lignes  as $ligne) {
			 if ($ligne->devl__type == 'REP') {
			 $marqueurPresta = '<input type="checkbox"> hors garantie' ;
			 }
		 }
		 $echoArrays = "";
		 foreach ($globalArray as  $resultsArray) {

			if (sizeof($resultsArray)  > 1){

				// si la taille du tableau correspond au nombre de ligne +1 (index 0 )alors chaque ligne possède la garantie : 
					$marqueurType = "Type de garantie";
					//on retire l'index 0 corespondant à la valeur de la garantie :
					$prixTemp =  floatval(array_sum($resultsArray) - $resultsArray[0]);
					// on additionne au prix total  :
					$prix = $prixTemp + $prixTotal;
					// renvoi dans le template html => 
					if (!$tva) {
						$echoArrays .=  "<tr><td style='width: 250px; font-size: 95%; font-style: italic; text-align: left'><input type='checkbox'> garantie " .$resultsArray[0] ." mois </td><td style=' font-size: 95%; font-style: italic; text-align: center'><strong>  "
						. number_format($prix,2  ,',', ' ').
						" €</strong></td></tr>";
					} else {
						$echoArrays .=  "<tr><td style='width: 210px; font-size: 95%; font-style: italic;  text-align: left'><input type='checkbox'> garantie " .$resultsArray[0] ." mois </td><td style='font-size: 95%; font-style: italic; text-align: center'><strong>  "
						. number_format($prix,2  ,',', ' ').
						" €</strong></td><td style='font-size: 95%; font-style: italic; text-align: right'> " 
						.number_format(Pdfunctions::ttc( floatval($prix)),2 ,',', ' ').
						" €</td></tr>";
					}
				}       
			}
			$finalEcho = '<table CELLSPACING=0  style=" border: 1px black solid;">
			<tr style="background-color: #dedede; ">
			<td style="width: 210px; text-align: left"> '. $marqueurType .'</td>
			<td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
			<td style="text-align: center">Total € TTC</td>
			</tr>
			<tr><td style="width: 210px; font-size: 95%; font-style: italic; text-align: left"> '.$marqueurPresta.'</td>
			<td style="text-align: center; font-style: italic;  font-size: 95%;"><strong>  '. number_format($prixTotal,2  ,',', ' ') . ' €</strong></td>
			<td style="text-align: right; font-style: italic; font-size: 95%;"> ' .number_format(Pdfunctions::ttc(floatval($prixTotal)),2 ,',', ' ').' €</td>
			</tr>' . $echoArrays;

			if (!$tva) {
			  $finalEcho = '<table CELLSPACING=0  style=" border: 1px black solid;">
			  <tr style="background-color: #dedede;">
			  <td style="width: 250px; text-align: left"> '. $marqueurType .'</td>
			  <td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
			  </tr>
			  <tr><td style="width: 250px;font-size: 95%; font-style: italic; text-align: left"> '.$marqueurPresta.'</td>
			  <td style="font-style: italic; text-align: center; font-size: 95%;"><strong>  '. number_format($prixTotal,2  ,',', ' ') . '€</strong></td>
			  </tr>' . $echoArrays;'';
			}

			 echo $finalEcho;
}










// function du calcul des extension de garanties avec keyword : 
public static function magicXtend($lignes, $garantiesArray , $prixTotal , $tva ){
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

   $marqueurPresta = ' <input type="checkbox"> garantie standard';
   $marqueurType = '';
   foreach ($lignes  as $ligne) {
		if ($ligne->devl__type == 'REP') {
		$marqueurPresta = '<input type="checkbox"> hors garantie' ;
		}
	}

   // égal le nombre de ligne - les services :
   $compare = sizeof($lignes) - $marqueurServices;
	//pour chaque tableau de résultat présent dans le tableau global 
   $echoArrays = "";
	foreach ($globalArray as  $resultsArray) {

	   // si la taille du tableau correspond au nombre de ligne +1 (index 0 )alors chaque ligne possède la garantie : 
		if (sizeof($resultsArray)  ==  $compare + 1) {
			$marqueurType = "Type de garantie";
			//on retire l'index 0 corespondant à la valeur de la garantie :
			$prixTemp =  floatval(array_sum($resultsArray) - $resultsArray[0]);
			// on additionne au prix total  :
			$prix = $prixTemp + $prixTotal;
			// renvoi dans le template html => 
			if (!$tva) {
				$echoArrays .=  "<tr><td style='width: 250px; font-size: 95%; font-style: italic; text-align: left'><input type='checkbox'> garantie " .$resultsArray[0] ." mois </td><td style=' font-size: 95%; font-style: italic; text-align: center'><strong>  "
				. number_format($prix,2  ,',', ' ').
				" €</strong></td></tr>";
			} else {
				$echoArrays .=  "<tr><td style='width: 210px; font-size: 95%; font-style: italic;  text-align: left'><input type='checkbox'> garantie " .$resultsArray[0] ." mois </td><td style='font-size: 95%; font-style: italic; text-align: center'><strong>  "
				. number_format($prix,2  ,',', ' ').
				" €</strong></td><td style='font-size: 95%; font-style: italic; text-align: right'> " 
				.number_format(Pdfunctions::ttc( floatval($prix)),2 ,',', ' ').
				" €</td></tr>";
			}
		}       
	}

	
	$finalEcho = '<table CELLSPACING=0  style=" border: 1px black solid;">
	<tr style="background-color: #dedede; ">
	<td style="width: 210px; text-align: left"> '. $marqueurType .'</td>
	<td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
	<td style="text-align: center">Total € TTC</td>
	</tr>
	<tr><td style="width: 210px; font-size: 95%; font-style: italic; text-align: left"> '.$marqueurPresta.'</td>
	<td style="text-align: center; font-style: italic;  font-size: 95%;"><strong>  '. number_format($prixTotal,2  ,',', ' ') . ' €</strong></td>
	<td style="text-align: right; font-style: italic; font-size: 95%;"> ' .number_format(Pdfunctions::ttc(floatval($prixTotal)),2 ,',', ' ').' €</td>
	</tr>' . $echoArrays;

	if (!$tva) {
	  $finalEcho = '<table CELLSPACING=0  style=" border: 1px black solid;">
	  <tr style="background-color: #dedede;">
	  <td style="width: 250px; text-align: left"> '. $marqueurType .'</td>
	  <td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
	  </tr>
	  <tr><td style="width: 250px;font-size: 95%; font-style: italic; text-align: left"> '.$marqueurPresta.'</td>
	  <td style="font-style: italic; text-align: center; font-size: 95%;"><strong>  '. number_format($prixTotal,2  ,',', ' ') . '€</strong></td>
	  </tr>' . $echoArrays;'';
	}

	 echo $finalEcho;
}





// function 20% 
public static function ttc($price){
   
	$opex = floatval(($price*20)/100);
	$results = $opex + $price;
	return $results;

}

	

	

}