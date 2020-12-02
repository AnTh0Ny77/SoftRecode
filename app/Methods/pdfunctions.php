<?php

namespace App\Methods;

use DateTime;

class Pdfunctions {




 

// fonction d'affichage de la societe : nom (id) adr1 adr2 cp ville : 

public static function showSociete($object)
{
	if ($object->client__adr2) {
		$text = $object->client__societe . " (" . $object->client__id . ")<br>".
		$object->client__adr1 . "<br>". $object->client__adr2 . "<br>" . $object->client__cp . " " . $object->client__ville ;
		return $text ;
	}
	else {
		$text = $object->client__societe . " (" . $object->client__id . ")<br>".
		$object->client__adr1 . "<br>". $object->client__cp . " " . $object->client__ville ;
		return $text ;
	}
   
}

public static function showSocieteFacture($object , $contact)
{
	if (!empty( $contact)) 
	{
		if ($object->client__adr2) 
		{
			$text = $object->client__societe . " (" . $object->client__id . ")<br>".  $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom . '<br>' . 
			$object->client__adr1 . "<br>". $object->client__adr2 . "<br>" . $object->client__cp . " " . $object->client__ville ;
			return $text ;
		}
		else 
		{
			$text = $object->client__societe . " (" . $object->client__id . ")<br>".  $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom . '<br>' .
			$object->client__adr1 . "<br>". $object->client__cp . " " . $object->client__ville ;
			return $text ;
		}
	}
	else 
	{
		if ($object->client__adr2) 
		{
			$text = $object->client__societe . " (" . $object->client__id . ")<br>" .
			$object->client__adr1 . "<br>". $object->client__adr2 . "<br>" . $object->client__cp . " " . $object->client__ville ;
			return $text ;
		}
		else 
		{
			$text = $object->client__societe . " (" . $object->client__id . ")<br>".  
			$object->client__adr1 . "<br>". $object->client__cp . " " . $object->client__ville ;
			return $text ;
		}
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
	$firstW = '12%';
	$secondW = '44%';
	$thirdW ='15%';
	$fourthW = '12%';
	$fifthW = '6%';
	$lastW= '11%';
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
	if (!empty($object->ordre2)) 
	{

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
			$secondCell2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW.";  text-align: left;  '>mise sous garantie du matériel réparé optionnelle  </td>";
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
		$fourthCell2 = "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW."; text-align: center;'>" . $array['devg__type'] ." mois </td>";
		$fifthCell2 ="<td valign='top' style=' width:".$fifthW."; max-width: ".$fifthW."; text-align: center;'>" .$quantité ."</td>";
		$lastCell2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
		$endSecondLine = "</tr> ";

		if ( $array === end($object->ordre2)) 
		{
			$secondLine = "<tr style='font-size: 95%; font-style: italic;'>" ;
			$firstCell2 = "<td valign='top' style=' width:".$firstW."; text-align: left; border-bottom: 1px #ccc solid'>garantie</td>";
			if ($object->devl__type == "REP") {
				$secondCell2 = "<td valign='top' style=' width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:15px '>mise sous garantie du matériel réparé optionnelle  </td>";
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
	if ($countEtat == 0 ) 
		$stringEtat = '';
	
	if ($countGarantie  == 0) 
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


//fonction d'affichage du total dans la vision facture : retourne prix ht , tva taux , tva montant , prix ttc 
public static function totalFacture($objectCmd, $arrayLigne )
{
	$tva = floatval($objectCmd->tva_Taux);
	$array_prix = [];
	$response = [] ; 
	foreach ($arrayLigne as $ligne ) 
	{
		if (!empty($ligne->devl_puht)) 
		{
			if (!empty($ligne->cmdl__qte_fact)) 
			{
				$quantite = intval($ligne->cmdl__qte_fact);
			}
			else
			{
				$quantite = intval($ligne->devl_quantite);
			} 
			
			$prix = floatval($ligne->devl_puht);
			$total_ligne = $quantite * $prix ;

				if (!empty($ligne->cmdl__garantie_option)) 
				{
					$prix_extension = floatval($ligne->cmdl__garantie_puht);
					$total_extension = $quantite * $prix_extension;
					$total_ligne = $total_ligne + $total_extension ;
				}
			 
			array_push($array_prix , $total_ligne);
		}
	}
	
	
	$global_ht = array_sum($array_prix);

	$montant_tva = floatval(($global_ht*$tva)/100);

	$global_ttc = $global_ht + $montant_tva;

	array_push($response , floatval($global_ht) , floatval($tva) , floatval($montant_tva) , floatval($global_ttc));

	return $response;	
}
	





//fonction d'affichage du total dans la vision facture : retourne prix ht , tva taux , tva montant , prix ttc 
public static function totalFacturePDF($objectCmd, $arrayLigne )
{
	$tva = floatval($objectCmd->tva_Taux);
	$array_prix = [];
	$response = [] ; 
	foreach ($arrayLigne as $ligne ) 
	{
		if (!empty($ligne->devl_puht)) 
		{
			
			$quantite = intval($ligne->cmdl__qte_fact);
			
			
			$prix = floatval($ligne->devl_puht);
			$total_ligne = $quantite * $prix ;

				if (!empty($ligne->cmdl__garantie_option)) 
				{
					$prix_extension = floatval($ligne->cmdl__garantie_puht);
					$total_extension = $quantite * $prix_extension;
					$total_ligne = $total_ligne + $total_extension ;
				}
			 
			array_push($array_prix , $total_ligne);
		}
	}
	
	
	$global_ht = array_sum($array_prix);

	$montant_tva = floatval(($global_ht*$tva)/100);

	$global_ttc = $global_ht + $montant_tva;

	array_push($response , floatval($global_ht) , floatval($tva) , floatval($montant_tva) , floatval($global_ttc));

	return $response;	
}



public static function totalFacturePRO($objectCmd, $arrayLigne )
{
	$tva = floatval($objectCmd->tva_Taux);
	$array_prix = [];
	$response = [] ; 
	foreach ($arrayLigne as $ligne ) 
	{
		if (!empty($ligne->devl_puht)) 
		{
			
			$quantite = intval($ligne->devl_quantite);
			
			
			$prix = floatval($ligne->devl_puht);
			$total_ligne = $quantite * $prix ;

				if (!empty($ligne->cmdl__garantie_option)) 
				{
					$prix_extension = floatval($ligne->cmdl__garantie_puht);
					$total_extension = $quantite * $prix_extension;
					$total_ligne = $total_ligne + $total_extension ;
				}
			 
			array_push($array_prix , $total_ligne);
		}
	}
	
	
	$global_ht = array_sum($array_prix);

	$montant_tva = floatval(($global_ht*$tva)/100);

	$global_ttc = $global_ht + $montant_tva;

	array_push($response , floatval($global_ht) , floatval($tva) , floatval($montant_tva) , floatval($global_ttc));

	return $response;	
}



// fonction d'affichage de garantie dans View :
public static function showGarantieView($object)
{
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
public static function totalCon($lignes , $garantieArray , $prixTotal , $tva , $_taux_tva){
	$globalArray = array();
	foreach ($garantieArray as  $value) 
	{
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
						else 
						{
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
						.number_format(Pdfunctions::ttcTVA( floatval($prix), $_taux_tva),2 ,',', ' ').
						" €</td></tr>";
				}
			}       
		}
		
		
			

			if (empty($echoArrays)) 
			{
				
				$finalEcho = '<table CELLSPACING=0  style=" margin-left: 180px;  border: 1px black solid;">
				<tr style="background-color: #dedede; ">
				<td style=" margin-left: 210px; width: 0px; text-align: left"> '. $marqueurType .'</td>
				<td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
				<td style="text-align: center">Total € TTC</td>
				</tr>
				<tr><td style="width: 0px; font-size: 95%; font-style: italic; text-align: left"> </td>
				<td style="text-align: center; font-style: italic;  font-size: 95%;"><strong>  '. number_format($prixTotal,2  ,',', ' ') . ' €</strong></td>
				<td style="text-align: right; font-style: italic; font-size: 95%;"> ' .number_format(Pdfunctions::ttcTVA(floatval($prixTotal), $_taux_tva),2 ,',', ' ').' €</td>
				</tr>' . $echoArrays;

			if (!$tva) 
			{
			  $finalEcho = '<table CELLSPACING=0  style=" margin-left: 200px;  border: 1px black solid;">
			  <tr style="background-color: #dedede;">
			  <td style="width: 0px; text-align: left"> '. $marqueurType .'</td>
			  <td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
			  </tr>
			  <tr><td style=" color: white; width: 0px;font-size: 95%; font-style: italic; text-align: left"></td>
			  <td style="font-style: italic; text-align: center; font-size: 95%;"><strong>  '. number_format($prixTotal,2  ,',', ' ') . '€</strong></td>
			  </tr>' . $echoArrays;'';
			}  

			
			}
			else 
			{

				$finalEcho = '<table CELLSPACING=0  style=" border: 1px black solid;">
				<tr style="background-color: #dedede; ">
				<td style="width: 210px; text-align: left"> '. $marqueurType .'</td>
				<td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
				<td style="text-align: center">Total € TTC</td>
				</tr>
				<tr><td style="width: 210px; font-size: 95%; font-style: italic; text-align: left"> '.$marqueurPresta.'</td>
				<td style="text-align: center; font-style: italic;  font-size: 95%;"><strong>  '. number_format($prixTotal,2  ,',', ' ') . ' €</strong></td>
				<td style="text-align: right; font-style: italic; font-size: 95%;"> ' .number_format(Pdfunctions::ttcTVA(floatval($prixTotal), $_taux_tva),2 ,',', ' ').' €</td>
				</tr>' . $echoArrays;

			if (!$tva) 
			{
				$finalEcho = '<table CELLSPACING=0  style=" border: 1px black solid;">
				<tr style="background-color: #dedede;">
				<td style="width: 250px; text-align: left"> '. $marqueurType .'</td>
				<td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
				</tr>
				<tr><td style="width: 250px;font-size: 95%; font-style: italic; text-align: left"> '.$marqueurPresta.'</td>
				<td style="font-style: italic; text-align: center; font-size: 95%;"><strong>  '. number_format($prixTotal,2  ,',', ' ') . '€</strong></td>
				</tr>' . $echoArrays;'';
			}

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
public static function ttc($price)
{
	$opex = floatval(($price*20)/100);
	$results = $opex + $price;
	return $results;
}

public static function ttcTVA($price,$tva)
{
	$opex = floatval(($price*$tva)/100);
	$results = $opex + $price;
	return $results;
}


public static function magicLineFTC($arrayLigne , $cmd){
	// variables des tailles de cellules afin de pouvoir regler la largeur de la table facilement :
	$firstW = '12%';
	$secondW = '50%';
	$thirdW ='10%';
	$fourthW = '14%';
	$fifthW = '14%';
	
	
	
	// paddind de la premiere ligne : 
	$firstPadding = '0px';
	
	$table = "";
	$countPadding = 0;

	if ($countPadding == 1 ) {
		$firstPadding = '15px';
	}
	else { $firstPadding = '0px';}

	$tva = floatval($cmd->tva_Taux);

	//boucle sur les lignes passées en parametres
	foreach ($arrayLigne as $ligne) 
	{
		
			
		
		// + 1 dans la valeur du padding
		$countPadding += 1;

		if ($countPadding == 1 ) {
			$firstPadding = '15px';
		}
		else { $firstPadding = '0px';}

		$pack = "";

		$pack .= '<tr>';

		$firstCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>"

			. $ligne->prestaLib. "
	
		</td>";

		if (!empty($ligne->cmdl__note_facture)) 
		{
			$secondCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$secondW."; max-width: ".$secondW."; text-align: left; padding-bottom: 5px;  '>"

				. $ligne->devl__designation.  $ligne->cmdl__note_facture."
		
			</td>";
		}
		else 
		{
			$secondCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  '>"

				. $ligne->devl__designation. "
		
			</td>";
		}

		$thirdCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: right;  '>"

		. $ligne->cmdl__qte_fact . "

		</td>";

		$FourthCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

		.  number_format($ligne->devl_puht , 2) . "

		</td>";

		$prixTTc = floatval($ligne->devl_puht * $ligne->cmdl__qte_fact);
	

		$FifthCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

		. number_format($prixTTc , 2) . "

		</td>";
		$pack .= $firstCell . $secondCell . $thirdCell . $FourthCell . $FifthCell ;
		$pack .= '</tr>';

		if (!empty($ligne->cmdl__garantie_option) && $ligne->cmdl__garantie_option!= '00' ) 
		{

			$firstCellXT = "<td valign='top' style='  width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>

			Extension 
	
			</td>";

			$secondCellXT = "<td valign='top' style='   width: ".$secondW."; max-width: ".$secondW."; text-align: left;  '>

			Extension de Garantie à ". $ligne->cmdl__garantie_option ." mois
		
			</td>";

			$thirdCellXT = "<td valign='top' style='   width: ".$thirdW."; max-width: ".$thirdW."; text-align: right;  '>"

			. $ligne->cmdl__qte_fact . "

			</td>";

			$FourthCellXT = "<td valign='top' style='   width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

			.  number_format($ligne->cmdl__garantie_puht , 2) . "

			</td>";

			$prixTTcXT = floatval($ligne->cmdl__garantie_puht* $ligne->cmdl__qte_fact);
			
			

			$FifthCellXT = "<td valign='top' style='   width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

			. number_format($prixTTcXT , 2) . "

			</td>";

			$pack .= '<tr>' . $firstCellXT . $secondCellXT . $thirdCellXT . $FourthCellXT . $FifthCellXT . '</tr>';
			
		}


		if ($ligne->cmdl__qte_fact > 0) 
		{
		$table .= $pack;
		}
	}

	

	$tete =  '<tr style=" margin-top : 70px;  background-color: #dedede; ">
	<td style=" text-align: center;  border: 1px solid black;  padding-top: 4px; padding-bottom: 4px;"><b>Prestation</b></td>
	<td style=" text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Désignation</b></td>
	<td  style=" text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Qté</b></td>
	<td style="text-align: center;  border: 1px solid black;  padding-top: 4px; padding-bottom: 4px;"><b>P.U € HT</b></td>
	<td style="text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>TOTAL € HT</b></td>
	</tr> ';

	echo $tete . $table ;
}




public static function magicLinePRO($arrayLigne , $cmd){
	// variables des tailles de cellules afin de pouvoir regler la largeur de la table facilement :
	$firstW = '12%';
	$secondW = '50%';
	$thirdW ='10%';
	$fourthW = '14%';
	$fifthW = '14%';
	
	
	
	// paddind de la premiere ligne : 
	$firstPadding = '0px';
	
	$table = "";
	$countPadding = 0;

	if ($countPadding == 1 ) {
		$firstPadding = '15px';
	}
	else { $firstPadding = '0px';}

	$tva = floatval($cmd->tva_Taux);

	//boucle sur les lignes passées en parametres
	foreach ($arrayLigne as $ligne) 
	{
		
			
		
		// + 1 dans la valeur du padding
		$countPadding += 1;

		if ($countPadding == 1 ) {
			$firstPadding = '15px';
		}
		else { $firstPadding = '0px';}

		$pack = "";

		$pack .= '<tr>';

		$firstCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>"

			. $ligne->prestaLib. "
	
		</td>";

		// if (!empty($ligne->devl__note_client)) 
		// {
		// 	$secondCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$secondW."; max-width: ".$secondW."; text-align: left; padding-bottom: 5px;  '>"

		// 		. $ligne->devl__designation.  $ligne->devl__note_client."
		
		// 	</td>";
		// }
		// else 
		// {
			$secondCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  '>"

				. $ligne->devl__designation. "
		
			</td>";
		// }

		$thirdCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: right;  '>"

		. $ligne->devl_quantite . "

		</td>";

		$FourthCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

		.  number_format($ligne->devl_puht , 2) . "

		</td>";

		$prixTTc = floatval($ligne->devl_puht * $ligne->devl_quantite);
	

		$FifthCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

		. number_format($prixTTc , 2) . "

		</td>";
		$pack .= $firstCell . $secondCell . $thirdCell . $FourthCell . $FifthCell ;
		$pack .= '</tr>';

		if (!empty($ligne->cmdl__garantie_option) && $ligne->cmdl__garantie_option!= '00' ) 
		{

			$firstCellXT = "<td valign='top' style='  width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>

			Extension 
	
			</td>";

			$secondCellXT = "<td valign='top' style='   width: ".$secondW."; max-width: ".$secondW."; text-align: left;  '>

			Extension de Garantie à ". $ligne->cmdl__garantie_option ." mois
		
			</td>";

			$thirdCellXT = "<td valign='top' style='   width: ".$thirdW."; max-width: ".$thirdW."; text-align: right;  '>"

			. $ligne->devl_quantite . "

			</td>";

			$FourthCellXT = "<td valign='top' style='   width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

			.  number_format($ligne->cmdl__garantie_puht , 2) . "

			</td>";

			$prixTTcXT = floatval($ligne->cmdl__garantie_puht* $ligne->devl_quantite);
			
			

			$FifthCellXT = "<td valign='top' style='   width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

			. number_format($prixTTcXT , 2) . "

			</td>";

			$pack .= '<tr>' . $firstCellXT . $secondCellXT . $thirdCellXT . $FourthCellXT . $FifthCellXT . '</tr>';
			
		}


		if ($ligne->devl_quantite > 0) 
		{
		$table .= $pack;
		}
	}

	

	$tete =  '<tr style=" margin-top : 70px;  background-color: #dedede; ">
	<td style=" text-align: center;  border: 1px solid black;  padding-top: 4px; padding-bottom: 4px;"><b>Prestation</b></td>
	<td style=" text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Désignation</b></td>
	<td  style=" text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Qté</b></td>
	<td style="text-align: center;  border: 1px solid black;  padding-top: 4px; padding-bottom: 4px;"><b>P.U € HT</b></td>
	<td style="text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>TOTAL € HT</b></td>
	</tr> ';

	echo $tete . $table ;
}











public static function magicLineABN($arrayLigne , $cmd , $prestaLib){
	// variables des tailles de cellules afin de pouvoir regler la largeur de la table facilement :
	$firstW = '17%';
	$secondW = '45%';
	$thirdW ='10%';
	$fourthW = '14%';
	$fifthW = '14%';
	
	
	
	// paddind de la premiere ligne : 
	$firstPadding = '0px';
	
	$table = "";
	$countPadding = 0;

	if ($countPadding == 1 ) {
		$firstPadding = '15px';
	}
	else { $firstPadding = '0px';}

	$tva = floatval($cmd->tva_Taux);

	//boucle sur les lignes passées en parametres
	foreach ($arrayLigne as $ligne) 
	{
		
			
		
		// + 1 dans la valeur du padding
		$countPadding += 1;

		if ($countPadding == 1 ) {
			$firstPadding = '15px';
		}
		else { $firstPadding = '0px';}

		$pack = "";

		$pack .= '<tr>';

		$firstCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>"

			. $prestaLib. "
	
		</td>";

		if (!empty($ligne->cmdl__note_facture)) 
		{
			$secondCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$secondW."; max-width: ".$secondW."; text-align: left; padding-bottom: 5px;  '>"

				. $ligne->devl__designation.  $ligne->cmdl__note_facture."
		
			</td>";
		}
		else 
		{
			$secondCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  '>"

				. $ligne->devl__designation. "
		
			</td>";
		}

		$thirdCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: right;  '>

		1

		</td>";

		$FourthCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

		.  number_format($ligne->devl_puht , 2 ,',', ' ') . "

		</td>";

		$prixTTc = floatval($ligne->devl_puht * $ligne->devl_quantite);
	

		$FifthCell = "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: right;  '>"

		. number_format($prixTTc , 2 ,',', ' ') . "

		</td>";
		$pack .= $firstCell . $secondCell . $thirdCell . $FourthCell . $FifthCell ;
		$pack .= '</tr>';


	}

	

	$tete =  '<tr style=" margin-top : 70px;  background-color: #dedede; ">
	<td style=" text-align: center;  border: 1px solid black;  padding-top: 4px; padding-bottom: 4px;"><b>Prestation</b></td>
	<td style=" text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Désignation</b></td>
	<td  style=" text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Qté</b></td>
	<td style="text-align: center;  border: 1px solid black;  padding-top: 4px; padding-bottom: 4px;"><b>P.U € HT</b></td>
	<td style="text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>TOTAL € HT</b></td>
	</tr> ';

	echo $tete . $pack ;
}


public static function totalABN($objectCmd, $arrayLigne )
{
	$tva = floatval($objectCmd->tva_Taux);
	$array_prix = [];
	$response = [] ; 
	foreach ($arrayLigne as $ligne ) 
	{
		if (!empty($ligne->devl_puht)) 
		{
			if (!empty($ligne->cmdl__qte_fact)) 
			{
				$quantite = intval($ligne->cmdl__qte_fact);
			}
			else
			{
				$quantite = intval($ligne->devl_quantite);
			} 
			
			$prix = floatval($ligne->devl_puht);
			$total_ligne = $quantite * $prix ;
			 
			array_push($array_prix , $total_ligne);
		}
	}
	
	
	$global_ht = array_sum($array_prix);

	$montant_tva = floatval(($global_ht*$tva)/100);

	$global_ttc = $global_ht + $montant_tva;

	array_push($response , floatval($global_ht) , floatval($tva) , floatval($montant_tva) , floatval($global_ttc));

	return $response;	
}




public static function magicLineContrat($arrayLigne)
{


	// variables des tailles de cellules afin de pouvoir regler la largeur de la table facilement :
	$firstW = '5%';
	$thirdW ='35%';
	$fifthW = '30%';
	$sixthW = '15%';
	$sevenhW = '15%';

	$pack = '';
	//boucle sur les lignes passées en parametres
	foreach ($arrayLigne as $ligne) 

	{

	$date = new DateTime($ligne->abl__dt_debut);
	$date = $date->format('d/m/Y'); 

	$code = 'M';

	$pack .= '<tr>';

	$cell1 = "<td valign='top' style='  padding-top:5px ; width: ".$firstW."; max-width: ".$firstW."; text-align: center;  '>" . $ligne->abl__type_repair . "</td>";

	$cell5 = "<td valign='top' style='  padding-top:5px ; width: ".$fifthW."; max-width: ".$fifthW."; text-align: center;  '>" . $ligne->abl__designation . "</td>";

	$cell3 = "<td valign='top' style='  padding-top:5px ; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center;  '>" . $ligne->abl__sn . "</td>";

	$cell6 = "<td valign='top' style='  padding-top:5px ; width: ".$sixthW."; max-width: ".$sixthW."; text-align: center;  '>" . $date . "</td>";

	$cell7 = "<td valign='top' style='  padding-top:5px ; width: ".$sevenhW."; max-width: ".$sevenhW."; text-align: right;  '>" . $ligne->abl__prix_mois . "</td>";
	
	$pack .= $cell1  . $cell3  . $cell5 . $cell6 . $cell7 ;

	$pack .= '</tr>';

	}

	$tete =  '<tr style=" margin-top : 30px;  background-color: #dedede; ">
	<td style=" text-align: center;  border: 1px solid black;  padding-top: 4px; padding-bottom: 4px;"><b>Code</b></td>
	<td style="text-align: center;  border: 1px solid black;  padding-top: 4px; padding-bottom: 4px;"><b>Désignation</b></td>
	<td  style=" text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Numéro de série</b></td>
	
	<td style="text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Début</b></td>
	<td style="text-align: center;  border: 1px solid black; padding-top: 4px; padding-bottom: 4px;"><b>Prix/Mois EUR</b></td>
	</tr> ';

	echo $tete . $pack ;
}



//fonction d'affichage du total dans la vision facture : retourne prix ht , tva taux , tva montant , prix ttc 
public static function totalContract($arrayLigne)
{
	$array_prix = [];
	$response = [] ; 

	foreach ($arrayLigne as $ligne ) 
	{
		if (!empty($ligne->abl__prix_mois)) 
		{	
			$prix = floatval($ligne->abl__prix_mois);
			$total_ligne =  $prix ;	 
			array_push($array_prix , $total_ligne);
		}
	}
	$global_ht = array_sum($array_prix);
	array_push($response , floatval($global_ht) );

	return $response;	
}

	

	

}