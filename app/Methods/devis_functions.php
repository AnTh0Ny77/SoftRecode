<?php

namespace App\Methods;
use App\Tables\Cmd;

class Devis_functions 
{

    public static function classic_devis_ligne_pdf($array_ligne , $report)
    {
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
        //si c'est la première itération le padding est différent: 
        $countPadding= 0 ;
        $table = "";
        $arrayReparation = []; 
        //si un report des lignes est demandé:
        switch ($report) 
        {
            case 0:
                foreach ($array_ligne as $ligne) 
                {
                    $countPadding += 1 ;
                    $prestation = strtolower($ligne->prestaLib);
                    // pour la premiere ligne de la table le padding-top est de 15px 
                    if ($countPadding == 1 ) 
                    {
                        $firstPadding = '15px';
                    }else 
                    {
                        $firstPadding = '15px';
                    }

                   
                   
                    //si un commentaire client est présent il s'ajoute sous la désignation 
                    if (!empty($ligne->devl__note_client) && intval($ligne->cmdl__image) > 1) 
                    {
                        $ligne->devl__note_client = '<span style="max-width= 100%; "> ' .$ligne->devl__note_client.'</span>';
                        $designation =  $ligne->devl__designation .'<span style="margin-top: -10px;">'. $ligne->devl__note_client .'</span>';
                    }
                    elseif(intval($ligne->cmdl__image) == 1 && !empty($ligne->ligne_image)) 
                    {
                        
                        $designation =  $ligne->devl__designation .'<br>
                                <span style="   max-width: 70px;">
                                    <figure class="image" >
                                        <img src="data:image/png;base64,'.$ligne->ligne_image.'"  width="70" />
                                    </figure>   
                                </span>
                                
                                <span style="margin-top: -10px;"  >
                                        '.$ligne->devl__note_client.'
                                </span>
                         ';
                    }
                    else 
                    { 
                        $designation =  $ligne->devl__designation .'<span style="margin-top: -10px;">'. $ligne->devl__note_client .'</span>';
                    }
                    // garantie
                    $garantie = $ligne->devl__mois_garantie . " mois";
                    //prix barre 
                    if ($ligne->devl__prix_barre > 0 ) 
                    {
                        $barre = "<s>". number_format(floatVal($ligne->devl__prix_barre),2 , ',',' ') ."€</s><br>";
                    }
                    else
                    {
                        $barre = '';
                    }
                    // prix
                    if (!empty($ligne->devl_puht)) 
                    {
                        $prix = number_format(floatVal($ligne->devl_puht),2 , ',',' ')  ."€";
                    }
                    else
                    {
                        $prix =  "offert";
                    } 
                    //quantite 
                    $quantité = $ligne->devl_quantite;
                    
                    //extensions
                    $extension = "";
                    if (!empty($ligne->tableau_extension)) 
                    {
                        //debut du gerbage du html 
                        $balise_tr_ouvrante = 
                        "<tr style='font-size: 95%; font-style: italic;'>";
                        $premiere_cellule = 
                        "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>" . $prestation  . "</td>";
                        $deuxieme_cellule = 
                        "<td valign='top' class='NoBR' style='  padding-top:".$firstPadding.";  width: ".$secondW."; max-width: ".$secondW."; text-align: left;  padding-bottom:10px'>"  . $designation. "</td>";
                        if ($ligne->devl__etat == 'NC.' || $ligne->cmdl__etat_masque > 0) 
                        {
                            $troisieme_cellule =  
                            "<td valign='top' style=' padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW.";  color: white ; text-align: center; '>" .$ligne->kw__lib ."</td>";
                        }
                        else 
                        {
                            $countEtat += 1 ;
                            $troisieme_cellule =  
                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; '>" .$ligne->kw__lib ."</td>";
                        }
                        if ($ligne->devl__mois_garantie > 0)
                        {
                            $countGarantie += 1 ;
                            $quatrieme_cellule = 
                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  text-align: center; '>" . $garantie ." </td>";
                        }
                        else 
                        {
                            $quatrieme_cellule = 
                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; '>" . $garantie ." </td>";
                        }
                        $cinquieme_cellule =
                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fifthW."; max-width: ".$fifthW."; text-align: center;  '>" .$quantité ."</td>";
                        $derniere_cellule = 
                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW.";  text-align: right;   padding-bottom:10px'>" . $barre . " " . $prix ."</td>" ;
                        $balise_tr_fermeture = "</tr>";
                        $counter = 0 ;
                        //boucle sur les extensions de garanties:
                        foreach($ligne->tableau_extension as $array)
                        {
                           
                            $counter = $counter + 1; 
                            $extension_ligne = "";
                            $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                            $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; max-width: ".$firstW."; text-align: left; '>garantie</td>";
                    
                            if ($ligne->devl__type == "REP") 
                            {
                                $seconde_cellule_2 = 
                                "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW. ";  text-align: left;  '>mise sous garantie du matériel réparé, hotline et ateliers en France</td>";
                            }
                            else 
                            {
                                if (is_int(intval($array['devg__type'])/12) ) 
                                {
                                    $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12)." ans hotline et ateliers en France</td>";
                                }
                                else {
                                    $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW. "; text-align: left;  ;'> extension de garantie hotline et ateliers en France</td>";
                                }       
                            }     
                            $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: left; '></td>";
                            $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW."; text-align: center;'>" . $array['devg__type'] ." mois </td>";
                            $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; max-width: ".$fifthW."; text-align: center;'>" .$quantité ."</td>";
                           
                            if (!empty($array['cmdg__prix_barre']) && floatval($array['cmdg__prix_barre']) > 00) 
                            {
                                $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'><s>" .number_format(floatVal($array['cmdg__prix_barre']),2 , ',',' ') ."</s>€<br> ".   number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
                            }
                            else
                            {
                                $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ."€</td>" ;
                            }
                            
                            $balise_tr_fermeture_2 = "</tr> ";
                    
                            if ($array === end($ligne->tableau_extension)) 
                            {
                                $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                                $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; text-align: left; border-bottom: 1px #ccc solid'>garantie</td>";
                                if ($ligne->devl__type == "REP") 
                                {
                                    $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:10px '>mise sous garantie du matériel réparé</td>";
                                }
                                else 
                                {
                                    if (is_int(intval($array['devg__type'])/12) ) 
                                    {
                                        $seconde_cellule_2 = "<td valign='top' style=' border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12). " ans hotline et ateliers en France </td>";
                                    }
                                    else 
                                    {
                                        $seconde_cellule_2 = "<td valign='top' style='border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW. "; text-align: left;  ;'> extension de garantie hotline et ateliers en France</td>";
                                    }
                                }
                    
                                $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; text-align: left; border-bottom: 1px #ccc solid'></td>";
                                $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; text-align: center; border-bottom: 1px #ccc solid'>" . $array['devg__type'] ." mois </td>";
                                $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
                                if (!empty($array['cmdg__prix_barre']) && floatval($array['cmdg__prix_barre']) > 00) 
                                {
                                    $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; border-bottom: 1px #ccc solid'><s>" .number_format(floatVal($array['cmdg__prix_barre']),2 , ',',' ') ."</s>€<br> ".   number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
                                }
                                else
                                {
                                    $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; border-bottom: 1px #ccc solid'>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ."€</td>" ;
                                }
                            }
                            $ligne_extension = $seconde_balise_tr . $premiere_cellule_2 . $seconde_cellule_2 . $troisieme_cellule_2 . $quatrieme_cellule_2 . $cinquieme_cellule_2 . $derniere_cellule_2 . $balise_tr_fermeture_2 ;
                            $extension .= $ligne_extension ;
                            $counter = 0;
                            }
                    }
                    else 
                    {
                        //debut du gerbage du html 
                        $balise_tr_ouvrante = 
                        "<tr style='font-size: 95%; font-style: italic;'>";
                        $premiere_cellule = 
                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$firstW.";  max-width: ".$firstW."; text-align: left; border-bottom: 1px #ccc solid; '>" . $prestation  . "</td>";
                        $deuxieme_cellule = 
                        "<td valign='top' class='NoBR' style='padding-top:".$firstPadding.";  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid ;  padding-bottom:10px'>"  . $designation. "</td>";
                        //condition pour etat = a NC. OU etat masque est demandé: 
                        if ($ligne->devl__etat == 'NC.' || $ligne->cmdl__etat_masque > 0 ) 
                        {
                            $troisieme_cellule =  
                            "<td valign='top' style='padding-top:".$firstPadding.";  width: ".$thirdW."; max-width: ".$thirdW."; color: white ; text-align: center; border-bottom: 1px #ccc solid'>" .$ligne->kw__lib ."</td>";
                        }
                        else
                        {
                            $countEtat += 1 ;
                            $troisieme_cellule =  
                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; border-bottom: 1px #ccc solid'>" .$ligne->kw__lib ."</td>";
                        }
                        //condition pour garantie
                        if ($ligne->devl__mois_garantie > 0)
                        {
                            $countGarantie += 1 ;
                            $quatrieme_cellule = 
                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: center; border-bottom: 1px #ccc solid'>" . $garantie ." </td>";
                        }
                        else 
                        {
                            $quatrieme_cellule = 
                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; border-bottom: 1px #ccc solid'>" . $garantie ." </td>";
                        }
                        $cinquieme_cellule =
                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
                        $derniere_cellule =  
                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW."; text-align: right;  border-bottom: 1px #ccc solid; padding-bottom:10px'>" . $barre . " " . $prix ."</td>" ;
                        $balise_tr_fermeture = "<br></tr> ";
                    }

                //Incrementation de la table pour chaque ligne: 
                $table .=  $balise_tr_ouvrante . $premiere_cellule . $deuxieme_cellule . $troisieme_cellule . $quatrieme_cellule . $cinquieme_cellule . $derniere_cellule . $balise_tr_fermeture . $extension;
                //fin de boucle sur les ligne :  
                }
                if ($countEtat == 0 )
                {
                    $stringEtat = '';
                }
                if ($countGarantie  == 0) 
                {
                    $stringGarantie = '';
                }  
                $tete =  
                '<tr style=" margin-top : 50px;  background-color: #dedede; ">
                <td style=" text-align: left;   padding-top: 4px; padding-bottom: 4px;">Prestation</td>
                <td style=" text-align: left; padding-top: 4px; padding-bottom: 4px;">Désignation</td>
                <td style="text-align: center; padding-top: 4px; padding-bottom: 4px;">'.$stringEtat.'</td>
                <td  style=" text-align: center; padding-top: 4px; padding-bottom: 4px;">'.$stringGarantie.'</td>
                <td style="text-align: center; padding-top: 4px; padding-bottom: 4px;">Qté</td>
                <td style="text-align: right; ; padding-top: 4px; padding-bottom: 4px;">P.u € HT</td>
                </tr> ';
                echo $tete . $table ;
                break;
            
            default:
                # code...
                break;
        }
        
    }





    //function qui converti le rendu de la base de donnée en image: 
    public static function base64_to_image($data) 
    {
        
           
            $data = str_replace( ' ', '+', $data );
            $data = base64_decode($data);
        
            if ($data === false) 
            {
                throw new \Exception('base64_decode failed');
            }
         
        
        file_put_contents("devis.png", $data);
    }


    public static function remise_devis_ligne_pdf($array_ligne,$report)
    {
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
                //si c'est la première itération le padding est différent: 
                $countPadding= 0 ;
                $table = "";
               
                $arrayReparation = []; 
                //si un report des lignes est demandé:
                switch ($report) 
                {
                    case 0:
                        foreach ($array_ligne as $ligne) 
                        {
                            $countPadding += 1 ;
                            $prestation = strtolower($ligne->prestaLib);
                            // pour la premiere ligne de la table le padding-top est de 15px 
                            if ($countPadding == 1 ) 
                            {
                                $firstPadding = '15px';
                            }else 
                            {
                                $firstPadding = '0px';
                            }
                             //si un commentaire client est présent il s'ajoute sous la désignation 
                            if (!empty($ligne->devl__note_client) && intval($ligne->cmdl__image) > 1) 
                            {
                                $ligne->devl__note_client = '<span style="max-width= 100%; "> ' .$ligne->devl__note_client.'</span>';
                                $designation =  $ligne->devl__designation .'<span style="margin-top: -10px;">'. $ligne->devl__note_client .'</span>';
                            }
                            elseif(intval($ligne->cmdl__image) == 1 && !empty($ligne->ligne_image)) 
                            {
                                
                                $designation =  $ligne->devl__designation .'
                                        <span style="   max-width: 70px;">
                                            <figure class="image" >
                                                <img src="data:image/png;base64,'.$ligne->ligne_image.'"  width="70" />
                                            </figure>   
                                        </span>
                                        
                                        <span style="margin-top: -10px;"  >
                                                '.$ligne->devl__note_client.'
                                        </span>
                                ';
                            }
                            else 
                            { 
                                $designation =  $ligne->devl__designation .'<span style="margin-top: -10px;">'. $ligne->devl__note_client .'</span>';
                            }
                            // garantie
                            $garantie = $ligne->devl__mois_garantie . " mois";
                            //prix barre 
                            if ($ligne->devl__prix_barre > 0 ) 
                            {
                                $barre = "". number_format(floatVal($ligne->devl__prix_barre),2 , ',',' ') ."€";
                            }
                            else
                            {
                                $barre = '';
                            }
                            // prix
                            if (!empty($ligne->devl_puht)) 
                            {
                                $prix = number_format(floatVal($ligne->devl_puht),2 , ',',' ')  ."€";
                            }
                            else
                            {
                                $prix =  "offert"; 
                            } 
                            //quantite 
                            $quantité = $ligne->devl_quantite;
                            
                            //extensions
                            $extension = "";
                            if (!empty($ligne->tableau_extension)) 
                            {
                                if ($ligne->devl__prix_barre > 0) 
                                {
                                       
                                        $montant_remise = floatval($ligne->devl__prix_barre) - floatVal($ligne->devl_puht) ;
                                        //debut du gerbage du html 
                                        $balise_tr_ouvrante = 
                                        "<tr style='font-size: 95%; font-style: italic;'>";
                                        $premiere_cellule = 
                                        "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>" . $prestation  . "</td>";
                                        $deuxieme_cellule = 
                                        "<td valign='top' class='NoBR' style='  padding-top:".$firstPadding.";  width: ".$secondW."; max-width: ".$secondW."; text-align: left;  '>"  . $designation. "</td>";
                                        if ($ligne->devl__etat == 'NC.' || $ligne->cmdl__etat_masque > 0) 
                                        {
                                            $troisieme_cellule =  
                                            "<td valign='top' style=' padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW.";  color: white ; text-align: center; '>" .$ligne->kw__lib ."</td>";
                                        }
                                        else 
                                        {
                                            $countEtat += 1 ;
                                            $troisieme_cellule =  
                                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; '>" .$ligne->kw__lib ."</td>";
                                        }
                                        if ($ligne->devl__mois_garantie > 0)
                                        {
                                            $countGarantie += 1 ;
                                            $quatrieme_cellule = 
                                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  text-align: center; '>" . $garantie ." </td>";
                                        }
                                        else 
                                        {
                                            $quatrieme_cellule = 
                                            "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; '>" . $garantie ." </td>";
                                        }
                                    
                                        $cinquieme_cellule ="<td valign='top' style='padding-top:".$firstPadding."; width:".$fifthW."; max-width: ".$fifthW."; text-align: center;'>" .$quantité ."</td>";
                                    
                                        $derniere_cellule = 
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW.";  text-align: right;  '>" . $barre . "</td>" ;
                                        $balise_tr_fermeture = "</tr>";
                                        $ligne_de_remise = "<tr style='font-size: 95%; font-style: italic;'>";
                                        $ligne_de_remise .= 
                                        "<td valign='top' style=' width: ".$firstW.";  max-width: ".$firstW."; text-align: left;  padding-bottom:10px '>remise </td>";
                                        $ligne_de_remise .= 
                                        "<td valign='top' class='NoBR' style='  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; '>Remise exeptionelle de ".number_format(floatval($montant_remise) ,2 , ',',' ') ."€</td>";
                                        $ligne_de_remise .=  
                                            "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: center;  '></td>";
                                        $ligne_de_remise .= 
                                            "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; '></td>";
                                        $ligne_de_remise .=
                                            "<td valign='top' style=' width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; '>" .$quantité ."</td>";
                                        $ligne_de_remise .=  
                                            "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; '>" . number_format(floatVal($ligne->devl_puht) ,2 , ',',' ') ."€</td>" ;
                                        $ligne_de_remise .= "</tr> ";
                                        $balise_tr_fermeture .= $ligne_de_remise;
                                        $counter = 0 ;
                                        //boucle sur les extensions de garanties:
                                        foreach($ligne->tableau_extension as $array)
                                        {
                                            $counter = $counter + 1; 
                                            $extension_ligne = "";
                                            $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                                            $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; max-width: ".$firstW."; text-align: left; '>garantie</td>";
                                    
                                            if ($ligne->devl__type == "REP") 
                                            {
                                                $seconde_cellule_2 = 
                                                "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW.";  text-align: left;  '>mise sous garantie du matériel réparé</td>";
                                            }
                                            else 
                                            {
                                                if (is_int(intval($array['devg__type'])/12) ) 
                                                {
                                                    $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12). " ans hotline et ateliers en France</td>";
                                                }
                                                else {
                                                    $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW. "; text-align: left;  ;'> extension de garantie hotline et ateliers en France</td>";
                                                }       
                                            }     
                                            $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: left; '></td>";
                                            $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW."; text-align: center;'>" . $array['devg__type'] ." mois </td>";
                                            $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; max-width: ".$fifthW."; text-align: center;'>" .$quantité ."</td>";
                                        
                                            if (!empty($array['cmdg__prix_barre']) && floatval($array['cmdg__prix_barre']) > 00) 
                                            {
                                                
                                                $montant_remise_extension = floatval($array['cmdg__prix_barre']) - floatVal($array['devg__prix']);
                                                $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" .number_format(floatval($array['cmdg__prix_barre']),2 , ',',' ') ."€</td>" ;
                                                $ligne_de_remise_extension = "<tr style='font-size: 95%; font-style: italic;'>";
                                                $ligne_de_remise_extension .= 
                                                "<td valign='top' style=' width: ".$firstW.";  max-width: ".$firstW."; text-align: left;   '>remise </td>";
                                                $ligne_de_remise_extension .= 
                                                "<td valign='top' class='NoBR' style='  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; '>Remise exeptionelle de ".number_format(floatval($montant_remise_extension) ,2 , ',',' ') ." €</td>";
                                                $ligne_de_remise_extension .=  
                                                    "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: center;  '></td>";
                                                $ligne_de_remise_extension .= 
                                                    "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; '></td>";
                                                $ligne_de_remise_extension .=
                                                    "<td valign='top' style=' width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; '>" .$quantité ."</td>";
                                                $ligne_de_remise_extension .=  
                                                    "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; '>" . number_format(floatVal($array['devg__prix']) ,2 , ',',' ') ." €</td>" ;
                                                $ligne_de_remise_extension .= "<br></tr> ";

                                                $balise_tr_fermeture_2 = "</tr> ";
                                                $balise_tr_fermeture_2 .= $ligne_de_remise_extension;
                                            }
                                            else
                                            {
                                                $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; padding-bottom:10px '>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
                                                $balise_tr_fermeture_2 = "</tr> ";
                                            }
                                             
                                            if($array === end($ligne->tableau_extension)) 
                                            {
                                                if (!empty($array['cmdg__prix_barre']) && floatval($array['cmdg__prix_barre']) > 00) 
                                                {
                                                    $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                                                    $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; max-width: ".$firstW."; text-align: left; '>garantie</td>";
                                            
                                                    if ($ligne->devl__type == "REP") 
                                                    {
                                                        $seconde_cellule_2 = 
                                                        "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW.";  text-align: left;  '>mise sous garantie du matériel réparé </td>";
                                                    }
                                                    else 
                                                    {
                                                        if (is_int(intval($array['devg__type'])/12) ) 
                                                        {
                                                            $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12). " ans hotline et ateliers en France </td>";
                                                        }
                                                        else {
                                                            $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW. "; text-align: left;  ;'> extension de garantie hotline et ateliers en France</td>";
                                                        }       
                                                    }     
                                                    $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: left; '></td>";
                                                    $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW."; text-align: center;'>" . $array['devg__type'] ." mois </td>";
                                                    $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; max-width: ".$fifthW."; text-align: center;'>" .$quantité ."</td>";  
                                                    $montant_remise_extension = floatval($array['cmdg__prix_barre']) - floatVal($array['devg__prix']);
                                                    $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" .number_format(floatval($array['cmdg__prix_barre']),2 , ',',' ') ."€</td>" ;
                                                    $ligne_de_remise_extension = "<tr style='font-size: 95%; font-style: italic;'>";
                                                    $ligne_de_remise_extension .= 
                                                    "<td valign='top' style=' width: ".$firstW.";  max-width: ".$firstW."; text-align: left; border-bottom: 1px #ccc solid; '>remise </td>";
                                                    $ligne_de_remise_extension .= 
                                                    "<td valign='top' class='NoBR' style='  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid;'>Remise exeptionelle de ".number_format(floatval($montant_remise_extension) ,2 , ',',' ') ." €</td>";
                                                    $ligne_de_remise_extension .=  
                                                    "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; border-bottom: 1px #ccc solid; '></td>";
                                                    $ligne_de_remise_extension .= 
                                                    "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; border-bottom: 1px #ccc solid; '></td>";
                                                    $ligne_de_remise_extension .=
                                                    "<td valign='top' style=' width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; border-bottom: 1px #ccc solid; '>" .$quantité ."</td>";
                                                    $ligne_de_remise_extension .=  
                                                    "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; border-bottom: 1px #ccc solid; padding-bottom:10px; '>" . number_format(floatVal($array['devg__prix']) ,2 , ',',' ') ." €</td>" ;
                                                    $ligne_de_remise_extension .= "</tr> ";
                                                    $balise_tr_fermeture_2 = "</tr> ";
                                                    $balise_tr_fermeture_2 .= $ligne_de_remise_extension;
                                                   
                                                }
                                                else
                                                {
                                                    $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                                                    $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; text-align: left; border-bottom: 1px #ccc solid'>garantie</td>";
                                                    if ($ligne->devl__type == "REP") 
                                                    {
                                                        $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:10px; '>mise sous garantie du matériel réparé </td>";
                                                    }
                                                    else 
                                                    {
                                                        if (is_int(intval($array['devg__type'])/12) ) 
                                                        {
                                                            $seconde_cellule_2 = "<td valign='top' style=' border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12). " ans hotline et ateliers en France</td>";
                                                        }
                                                        else 
                                                        {
                                                            $seconde_cellule_2 = "<td valign='top' style='border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW. "; text-align: left; padding-bottom:10px ;'> extension de garantie hotline et ateliers en France</td>";
                                                        }
                                                    }
                                        
                                                    $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; text-align: left; border-bottom: 1px #ccc solid'></td>";
                                                    $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; text-align: center; border-bottom: 1px #ccc solid'>" . $array['devg__type'] ." mois </td>";
                                                    $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
                                                    if (!empty($array['cmdg__prix_barre']) && floatval($array['cmdg__prix_barre']) > 00) 
                                                    {
                                                        $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; border-bottom: 1px #ccc solid'><s>" .number_format(floatVal($array['cmdg__prix_barre']),2 , ',',' ') ."</s>€<br> ".   number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
                                                    }
                                                    else
                                                    {
                                                        $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; border-bottom: 1px #ccc solid'>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
                                                    }
                                                }
                                                
                                            }
                                            $ligne_extension = $seconde_balise_tr . $premiere_cellule_2 . $seconde_cellule_2 . $troisieme_cellule_2 . $quatrieme_cellule_2 . $cinquieme_cellule_2 . $derniere_cellule_2 . $balise_tr_fermeture_2  ;
                                            $extension .= $ligne_extension ;
                                            $counter = 0;
                                        }
                                }
                                else 
                                {
                                    //debut du gerbage du html 
                                    $balise_tr_ouvrante = 
                                    "<tr style='font-size: 95%; font-style: italic;'>";
                                    $premiere_cellule = 
                                    "<td valign='top' style='  padding-top:".$firstPadding."; width: ".$firstW."; max-width: ".$firstW."; text-align: left;  '>" . $prestation  . "</td>";
                                    $deuxieme_cellule = 
                                    "<td valign='top' class='NoBR' style='  padding-top:".$firstPadding.";  width: ".$secondW."; max-width: ".$secondW."; text-align: left;  '>"  . $designation. "</td>";
                                    if ($ligne->devl__etat == 'NC.' || $ligne->cmdl__etat_masque > 0) 
                                    {
                                        $troisieme_cellule =  
                                        "<td valign='top' style=' padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW.";  color: white; text-align: center; '>" .$ligne->kw__lib ."</td>";
                                    }
                                    else 
                                    {
                                        $countEtat += 1 ;
                                        $troisieme_cellule =  
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; '>" .$ligne->kw__lib ."</td>";
                                    }
                                    if ($ligne->devl__mois_garantie > 0)
                                    {
                                        $countGarantie += 1 ;
                                        $quatrieme_cellule = 
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  text-align: center; '>" . $garantie ." </td>";
                                    }
                                    else 
                                    {
                                        $quatrieme_cellule = 
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; '>" . $garantie ." </td>";
                                    }
                                
                                    $cinquieme_cellule ="<td valign='top' style=' width:".$fifthW."; max-width: ".$fifthW."; text-align: center;'>" .$quantité ."</td>";
                                
                                    $derniere_cellule = 
                                    "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW.";  text-align: right;   padding-bottom:10px;'>" . $barre . " " . $prix ."</td>" ;
                                    $balise_tr_fermeture = "</tr>";

                                   
                                    $counter = 0 ;
                                    //boucle sur les extensions de garanties:
                                    foreach($ligne->tableau_extension as $array)
                                    {
                                    
                                        $counter = $counter + 1; 
                                        $extension_ligne = "";
                                        $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                                        $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; max-width: ".$firstW."; text-align: left; '>garantie</td>";
                                
                                        if ($ligne->devl__type == "REP") 
                                        {
                                            $seconde_cellule_2 = 
                                            "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW.";  text-align: left;  '>mise sous garantie du matériel réparé</td>";
                                        }
                                        else 
                                        {
                                            if (is_int(intval($array['devg__type'])/12) ) 
                                            {
                                                $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12)." ans </td>";
                                            }
                                            else {
                                                $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie</td>";
                                            }       
                                        }     
                                        $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: left; '></td>";
                                        $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW."; text-align: center;'>" . $array['devg__type'] ." mois </td>";
                                        $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; max-width: ".$fifthW."; text-align: center;'>" .$quantité ."</td>";
                                    
                                        if (!empty($array['cmdg__prix_barre']) && floatval($array['cmdg__prix_barre']) > 00) 
                                        {
                                            
                                            $montant_remise_extension = floatval($array['cmdg__prix_barre']) - floatVal($array['devg__prix']);
                                            $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" .number_format(floatval($array['cmdg__prix_barre']),2 , ',',' ') ."€</td>" ;
                                            $ligne_de_remise_extension = "<tr style='font-size: 95%; font-style: italic;'>";
                                            $ligne_de_remise_extension .= 
                                            "<td valign='top' style=' width: ".$firstW.";  max-width: ".$firstW."; text-align: left;  '>remise </td>";
                                            $ligne_de_remise_extension .= 
                                            "<td valign='top' class='NoBR' style='  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; '>Remise exeptionelle de ".number_format(floatval($montant_remise_extension) ,2 , ',',' ') ." €</td>";
                                            $ligne_de_remise_extension .=  
                                                "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: center;  '></td>";
                                            $ligne_de_remise_extension .= 
                                                "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; '></td>";
                                            $ligne_de_remise_extension .=
                                                "<td valign='top' style=' width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; '>" .$quantité ."</td>";
                                            $ligne_de_remise_extension .=  
                                                "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; '>" . number_format(floatVal($array['devg__prix']) ,2 , ',',' ') ." €</td>" ;
                                            $ligne_de_remise_extension .= "</tr> ";

                                            $balise_tr_fermeture_2 = "<br></tr> ";
                                            $balise_tr_fermeture_2 .= $ligne_de_remise_extension;
                                        }
                                        else
                                        {
                                            $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
                                            $balise_tr_fermeture_2 = "</tr> ";
                                        }
                                    
                                
                                        if ($array === end($ligne->tableau_extension)) 
                                        {
                                            if (!empty($array['cmdg__prix_barre']) && floatval($array['cmdg__prix_barre']) > 00) 
                                            {
                                                $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                                                $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; max-width: ".$firstW."; text-align: left; '>garantie</td>";
                                        
                                                if ($ligne->devl__type == "REP") 
                                                {
                                                    $seconde_cellule_2 = 
                                                    "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW.";  text-align: left;  '>mise sous garantie du matériel réparé</td>";
                                                }
                                                else 
                                                {
                                                    if (is_int(intval($array['devg__type'])/12) ) 
                                                    {
                                                        $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12). " ans hotline et ateliers en France</td>";
                                                    }
                                                    else {
                                                        $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW. "; text-align: left;  ;'> extension de garantie hotline et ateliers en France</td>";
                                                    }       
                                                }     
                                                $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: left; '></td>";
                                                $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW."; text-align: center;'>" . $array['devg__type'] ." mois </td>";
                                                $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; max-width: ".$fifthW."; text-align: center;'>" .$quantité ."</td>";  
                                                $montant_remise_extension = floatval($array['cmdg__prix_barre']) - floatVal($array['devg__prix']);
                                                $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" .number_format(floatval($array['cmdg__prix_barre']),2 , ',',' ') ."€</td>" ;
                                                $ligne_de_remise_extension = "<tr style='font-size: 95%; font-style: italic;'>";
                                                $ligne_de_remise_extension .= 
                                                "<td valign='top' style=' width: ".$firstW.";  max-width: ".$firstW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:10px; '>remise </td>";
                                                $ligne_de_remise_extension .= 
                                                "<td valign='top' class='NoBR' style='  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid;'>Remise exeptionelle de ".number_format(floatval($montant_remise_extension) ,2 , ',',' ') ." €</td>";
                                                $ligne_de_remise_extension .=  
                                                "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; border-bottom: 1px #ccc solid; '></td>";
                                                $ligne_de_remise_extension .= 
                                                "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; border-bottom: 1px #ccc solid; '></td>";
                                                $ligne_de_remise_extension .=
                                                "<td valign='top' style=' width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; border-bottom: 1px #ccc solid; '>" .$quantité ."</td>";
                                                $ligne_de_remise_extension .=  
                                                "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; border-bottom: 1px #ccc solid; '>" . number_format(floatVal($array['devg__prix']) ,2 , ',',' ') ." €</td>" ;
                                                $ligne_de_remise_extension .= "</tr> ";
                                                $balise_tr_fermeture_2 = "</tr> ";
                                                $balise_tr_fermeture_2 .= $ligne_de_remise_extension;
                                               
                                            }
                                            else
                                            {
                                                $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                                                $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; text-align: left; border-bottom: 1px #ccc solid'>garantie</td>";
                                                if ($ligne->devl__type == "REP") 
                                                {
                                                    $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:10px; '>mise sous garantie du matériel réparé</td>";
                                                }
                                                else 
                                                {
                                                    if (is_int(intval($array['devg__type'])/12) ) 
                                                    {
                                                        $seconde_cellule_2 = "<td valign='top' style=' border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12). " ans hotline et ateliers en France</td>";
                                                    }
                                                    else 
                                                    {
                                                        $seconde_cellule_2 = "<td valign='top' style='border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW. "; text-align: left;  ;'> extension de garantie hotline et ateliers en France</td>";
                                                    }
                                                }
                                    
                                                $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; text-align: left; border-bottom: 1px #ccc solid'></td>";
                                                $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; text-align: center; border-bottom: 1px #ccc solid'>" . $array['devg__type'] ." mois </td>";
                                                $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
                                                $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; border-bottom: 1px #ccc solid'>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ."€</td>" ;
                                                
                                            }
                                        }
                                        $ligne_extension = $seconde_balise_tr . $premiere_cellule_2 . $seconde_cellule_2 . $troisieme_cellule_2 . $quatrieme_cellule_2 . $cinquieme_cellule_2 . $derniere_cellule_2 . $balise_tr_fermeture_2  ;
                                        $extension .= $ligne_extension ;
                                        $counter = 0;
                                        }

                                }
                                
                            }
                            else 
                            {
                                
                                if ($ligne->devl__prix_barre > 0) 
                                {   
                                    $montant_remise = floatval($ligne->devl__prix_barre) - floatVal($ligne->devl_puht) ;
                                   //debut du gerbage du html 
                                    $balise_tr_ouvrante = 
                                    "<tr style='font-size: 95%; font-style: italic;'>";
                                    $premiere_cellule = 
                                    "<td valign='top' style='padding-top:".$firstPadding."; width: ".$firstW.";  max-width: ".$firstW."; text-align: left;  '>" . $prestation  . "</td>";
                                    $deuxieme_cellule = 
                                    "<td valign='top' class='NoBR' style='padding-top:".$firstPadding.";  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; '>"  . $designation. "</td>";
                                    //condition pour etat = a NC. OU etat masque est demandé: 
                                    if ($ligne->devl__etat == 'NC.' || $ligne->cmdl__etat_masque > 0 ) 
                                    {
                                        $troisieme_cellule =  
                                        "<td valign='top' style='padding-top:".$firstPadding.";  width: ".$thirdW."; max-width: ".$thirdW."; color: white; text-align: center; '>" .$ligne->kw__lib ."</td>";
                                    }
                                    else
                                    {
                                        $countEtat += 1 ;
                                        $troisieme_cellule =  
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center;  '>" .$ligne->kw__lib ."</td>";
                                    }
                                    //condition pour garantie
                                    if ($ligne->devl__mois_garantie > 0)
                                    {
                                        $countGarantie += 1 ;
                                        $quatrieme_cellule = 
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: center;  '>" . $garantie ." </td>";
                                    }
                                    else 
                                    {
                                        $quatrieme_cellule = 
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; '>" . $garantie ." </td>";
                                    }
                                    $cinquieme_cellule =
                                    "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; '>" .$quantité ."</td>";
                                    $derniere_cellule =  
                                    "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" . number_format( floatval($ligne->devl__prix_barre) ,2 , ',',' ') ."€</td>" ;
                                    $balise_tr_fermeture = "</tr> ";

                                    $ligne_de_remise = "<tr style='font-size: 95%; font-style: italic;'>";
                                    $ligne_de_remise .= 
                                    "<td valign='top' style=' width: ".$firstW.";  max-width: ".$firstW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:10px; '>remise </td>";
                                    $ligne_de_remise .= 
                                    "<td valign='top' class='NoBR' style='  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid;'>Remise exeptionelle de ".number_format(floatval($montant_remise) ,2 , ',',' ') ."€</td>";
                                    $ligne_de_remise .=  
                                        "<td valign='top' style=' width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; border-bottom: 1px #ccc solid; '></td>";
                                    $ligne_de_remise .= 
                                        "<td valign='top' style=' width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; border-bottom: 1px #ccc solid; '></td>";
                                    $ligne_de_remise .=
                                        "<td valign='top' style=' width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; border-bottom: 1px #ccc solid;'>" .$quantité ."</td>";
                                    $ligne_de_remise .=  
                                        "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right; border-bottom: 1px #ccc solid;'>" . number_format(floatVal($ligne->devl_puht) ,2 , ',',' ') ."€</td>" ;
                                    $ligne_de_remise .= "</tr> ";
                                    $balise_tr_fermeture .= $ligne_de_remise;
                                }
                                else
                                {
                                    //debut du gerbage du html 
                                    $balise_tr_ouvrante = 
                                    "<tr style='font-size: 95%; font-style: italic;'>";
                                    $premiere_cellule = 
                                    "<td valign='top' style='padding-top:".$firstPadding."; width: ".$firstW.";  max-width: ".$firstW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:10px;  '>" . $prestation  . "</td>";
                                    $deuxieme_cellule = 
                                    "<td valign='top' class='NoBR' style='padding-top:".$firstPadding.";  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid ;  '>"  . $designation. "</td>";
                                    //condition pour etat = a NC. OU etat masque est demandé: 
                                    if ($ligne->devl__etat == 'NC.' || $ligne->cmdl__etat_masque > 0 ) 
                                    {
                                        $troisieme_cellule =  
                                        "<td valign='top' style='padding-top:".$firstPadding.";  width: ".$thirdW."; max-width: ".$thirdW."; color: white; text-align: center; border-bottom: 1px #ccc solid'>" .$ligne->kw__lib ."</td>";
                                    }
                                    else
                                    {
                                        $countEtat += 1 ;
                                        $troisieme_cellule =  
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$thirdW."; max-width: ".$thirdW."; text-align: center; border-bottom: 1px #ccc solid'>" .$ligne->kw__lib ."</td>";
                                    }
                                    //condition pour garantie
                                    if ($ligne->devl__mois_garantie > 0)
                                    {
                                        $countGarantie += 1 ;
                                        $quatrieme_cellule = 
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW."; text-align: center; border-bottom: 1px #ccc solid'>" . $garantie ." </td>";
                                    }
                                    else 
                                    {
                                        $quatrieme_cellule = 
                                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fourthW."; max-width: ".$fourthW.";  color: white; text-align: center; border-bottom: 1px #ccc solid'>" . $garantie ." </td>";
                                    }
                                    $cinquieme_cellule =
                                    "<td valign='top' style='padding-top:".$firstPadding."; width: ".$fifthW."; max-width: ".$fifthW."; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
                                    $derniere_cellule =  
                                    "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW."; text-align: right;  border-bottom: 1px #ccc solid; '>" . $barre . " " . $prix ."</td>" ;
                                    $balise_tr_fermeture = "</tr> ";
                                }
                                
                            }
        
                        //Incrementation de la table pour chaque ligne: 
                        $table .=  $balise_tr_ouvrante . $premiere_cellule . $deuxieme_cellule . $troisieme_cellule . $quatrieme_cellule . $cinquieme_cellule . $derniere_cellule . $balise_tr_fermeture  . $extension ;
                        //fin de boucle sur les ligne :  
                        }
                        if ($countEtat == 0 )
                        {
                            $stringEtat = '';
                        }
                        if ($countGarantie  == 0) 
                        {
                            $stringGarantie = '';
                        }  
                        $tete =  
                        '<tr style=" margin-top : 50px;  background-color: #dedede; ">
                        <td style=" text-align: left;   padding-top: 4px; padding-bottom: 4px;">Prestation</td>
                        <td style=" text-align: left; padding-top: 4px; padding-bottom: 4px;">Désignation</td>
                        <td style="text-align: center; padding-top: 4px; padding-bottom: 4px;">'.$stringEtat.'</td>
                        <td  style=" text-align: center; padding-top: 4px; padding-bottom: 4px;">'.$stringGarantie.'</td>
                        <td style="text-align: center; padding-top: 4px; padding-bottom: 4px;">Qté</td>
                        <td style="text-align: right; ; padding-top: 4px; padding-bottom: 4px;">P.u € HT</td>
                        </tr> ';
                        echo $tete . $table;
                        break;
                    
                    default:
                        # code...
                        break;
                }
                
    }

    public static function classic_total_devis_pdf($cmd , $array_ligne)
    {
        $tva = floatval($cmd->tva_Taux);
        $array_prix = [];
        $response = [] ; 
        foreach ($array_ligne as $ligne) 
	    {
            if (!empty($ligne->devl_puht)) 
            {   
                $quantite = intval($ligne->devl_quantite); 
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


    public static function ttc($price,$tva)
    {
        $opex = floatval(($price*$tva)/100);
        $results = $opex + $price;
        return $results;
    }


    public static function classic_total_devis($lignes , $garantieArray , $prixTotal , $tva , $_taux_tva)
    {
	$globalArray = array();
	foreach ($garantieArray as  $value) 
	{
		// création d'un tableau multidimensionnel pour chaque valeur présente dans le tableau : 
		$type = intval($value->kw__value);
		$globalArray[$type]  = [$type];
	}  
		// pour sur chaque ligne de garantie 
        foreach ($lignes as $ligne ) 
        { 
			// variable $xtend déclaré pour chaque tableau d'extension de garanties : 
			$xtend =  $ligne->tableau_extension;
			// si il ne s'agit pas d'un service pour sur chaque tableau d'extension du tableau des extensions de  garantie : 
        
                foreach($xtend as $array) 
                {
					//sur chaque valeur du tableau des garantie dans keyword : 
                    foreach ($globalArray as  $value) 
                    {
						// si la valeur du nombre de mois dans l'extension correspond à la valeur du  tableau de la liste keyword : 
                        if (intval($array['devg__type']) == $value[0]) 
                        {
							// la variable $results est le résultat du prix de l'extension correspondante * la quantité 
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

		$marqueurPresta = ' <input type="checkbox"> garantie 6/12 mois';
		$marqueurType = '';
        foreach ($lignes  as $ligne) 
        {
             if ($ligne->devl__type == 'REP') 
             {
			 $marqueurPresta = '<input type="checkbox"> hors garantie' ;
			 }
		 }
         $echoArrays = "";
         
        foreach ($globalArray as  $resultsArray) 
        {
            if (sizeof($resultsArray) > 1)
            {
                // si la taille du tableau correspond au nombre de ligne +1 (index 0 )alors chaque ligne possède la garantie : 
                $marqueurType = "Type de garantie";
                //on retire l'index 0 corespondant à la valeur de la garantie :
                $prixTemp =  floatval(array_sum($resultsArray) - $resultsArray[0]);
                // on additionne au prix total  :
                $prix = $prixTemp + $prixTotal;
                // renvoi dans le template html => 
                if (!$tva) 
                {
                    $echoArrays .=  "<tr><td style='width: 250px; font-size: 95%; font-style: italic; text-align: left'><input type='checkbox'> garantie " .$resultsArray[0] ." mois </td><td style=' font-size: 95%; font-style: italic; text-align: center'><strong>  "
                    . number_format($prix,2  ,',', ' ').
                    " €</strong></td></tr>";
                } 
                else 
                {
                    $echoArrays .=  "<tr><td style='width: 210px; font-size: 95%; font-style: italic;  text-align: left'><input type='checkbox'> garantie " .$resultsArray[0] ." mois </td><td style='font-size: 95%; font-style: italic; text-align: center'><strong>  "
                    . number_format($prix,2  ,',', ' ').
                    " €</strong></td><td style='font-size: 95%; font-style: italic; text-align: right'> " 
                    .number_format(Devis_functions::ttc( floatval($prix), $_taux_tva),2 ,',', ' ').
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
                <td style="text-align: right; font-style: italic; font-size: 95%;"> ' .number_format(Devis_functions::ttc(floatval($prixTotal), $_taux_tva),2 ,',', ' ').' €</td>
			    </tr>' . $echoArrays;

                if(!$tva) 
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
				<td style="text-align: right; font-style: italic; font-size: 95%;"> ' .number_format(Devis_functions::ttc(floatval($prixTotal), $_taux_tva),2 ,',', ' ').' €</td>
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
			echo  $finalEcho . '</table>';
}

    public static function remise_total_devis_pdf($cmd , $array_ligne)
    {
        $tva = floatval($cmd->tva_Taux);
        $array_prix = [];
        $array_remise = [];
        $response = [] ; 
        foreach ($array_ligne as $ligne) 
	    {
            if (!empty($ligne->devl_puht)) 
            {   
                $quantite = intval($ligne->devl_quantite); 
                $prix = floatval($ligne->devl_puht);
                $total_ligne = $quantite * $prix ;
                array_push($array_prix , $total_ligne);
            }
            if (!empty($ligne->devl__prix_barre)) 
            {   
                $quantite = intval($ligne->devl_quantite); 
                $prix = floatval($ligne->devl_puht);
                $remise = floatval($ligne->devl__prix_barre);
                $total_remise = ($quantite * $prix) - ($quantite * $remise) ;
                array_push($array_remise , $total_remise);
            }
        }
        $global_ht = array_sum($array_prix);
        $global_remise = array_sum($array_remise);
        $montant_tva = floatval(($global_ht*$tva)/100);
        $global_ttc = $global_ht + $montant_tva;
        array_push($response , floatval($global_ht) , floatval($tva) , floatval($montant_tva) , floatval($global_ttc) , floatval($global_remise));
        return $response;	
    }



    public static function classic_devis_ligne_pdf_image($array_ligne, $report)
    {
        // variables des tailles de cellules afin de pouvoir regler la largeur de la table facilement :
        $firstW = '12%';
        $secondW = '44%';
        $thirdW = '15%';
        $fourthW = '12%';
        $fifthW = '6%';
        $lastW = '11%';
        // variable type et garantie [ en fonction de l'existence des valeurs ]
        $stringEtat = "Type";
        $stringGarantie = "Garantie";
        // paddind de la premiere ligne : 
        $firstPadding = '0px';
       //count etat est incrémenté de 1 a chaque etat visible ou différent de NC si elle est égale à zero en fin de boucle le mot Etat n'est pas afficher en tete de colonne
        $countEtat = 0;
        //meme système pour les garanties : 
        $countGarantie = 0;
        //si c'est la première itération le padding est différent: 
        $countPadding = 0;
        $table = "";

       
                //boucle sur chaque ligne 
                foreach ($array_ligne as $ligne) 
                {
                    $countPadding += 1;
                    //presattion transformé en minuscule : 
                    $prestation = strtolower($ligne->prestaLib);
                    // pour la premiere ligne de la table le padding-top est de 15px 
                    if ($countPadding == 1) 
                    {
                        $firstPadding = '15px';
                    } 
                    else 
                    {
                        $firstPadding = '15px';
                    }

                    //gestion de la désignation en fonction de la présence de photo ou de commentaire :
                    $ligne_photo = '';
                    $border_bottom_photo = '';
                    if (empty($ligne->tableau_extension)) 
                    {
                        $border_bottom_photo = 'border-bottom: 1px #ccc solid;';
                    }

                    //si un commentaire client est présent il s'ajoute sous la désignation de façon classique 
                    if (!empty($ligne->devl__note_client) && intval($ligne->cmdl__image) < 1) 
                    {
                        $ligne_photo = "
                        <tr style='font-size: 95%; font-style: italic; padding-top: -10px;'>

                            <td valign='top' colspan='5'  style=' ".$border_bottom_photo." width: 80%; max-width: 80%; '>
                                <table>
                                    <tr style='font-size: 95%; font-style: italic;'>
                                        <td style='width: 120px; max-width: 120px;'>
                                            
                                        </td>
                                        <td style='width: 450px; max-width: 450px; padding-top: -12px;'>
                                            ".$ligne->devl__note_client." 
                                        </td>
                                    </tr>
                                </table> 
                            </td>

                            <td valign='top' colspan='1' style=' ".$border_bottom_photo." text-align: left;  '>
                            </td>             
                        </tr>";
                    } 
                    //si une photo est présente : 
                    elseif (intval($ligne->cmdl__image) == 1 && !empty($ligne->ligne_image)) 
                    {
                        $ligne_photo = "
                        <tr style='font-size: 95%; font-style: italic; padding-top: -10px;'>

                            <td valign='top' colspan='5'  style=' ".$border_bottom_photo." width: 80%; max-width: 80%; '>
                                <table>
                                    <tr style='font-size: 95%; font-style: italic;'>
                                        <td style='width: 120px; max-width: 120px; '>
                                            <figure class='image'>
                                                <img src='data:image/png;base64,".$ligne->ligne_image."'  width='100'/>
                                            </figure> 
                                        </td>
                                        <td style='width: 450px; max-width: 450px; padding-top: -12px;'>
                                            ".$ligne->devl__note_client." 
                                        </td>
                                    </tr>
                                </table> 
                            </td>

                            <td valign='top' colspan='1' style=' ".$border_bottom_photo." text-align: left;  '>
                            </td>             
                        </tr>";
                    } 
                        
                    $designation =  $ligne->devl__designation ;
                    
                    // variable affichage de la garantie : 
                    $garantie = $ligne->devl__mois_garantie . " mois";
                    // variable d'afficchage du prix barre :  
                    if ($ligne->devl__prix_barre > 0) 
                    {
                        $barre = "<s>" . number_format(floatVal($ligne->devl__prix_barre), 2, ',', ' ') . "€</s><br>";
                    } 
                    else 
                    {
                        $barre = '';
                    }
                    //variable d'affichage du  prix : 
                    if (!empty($ligne->devl_puht)) 
                    {
                        $prix = number_format(floatVal($ligne->devl_puht), 2, ',', ' ')  . "€";
                    } 
                    else 
                    {
                        $prix =  "offert";
                    }
                    //variable d'affichage de la quantité :
                    $quantité = $ligne->devl_quantite;

                    //variable d 'affichage du tableau des extensions : 
                    $extension = "";

                    //si le tableau d'extension ou la photo existe j efface le border bottom :
                    $border_bottom = 'border-bottom: 1px #ccc solid;';
                    if (!empty($ligne->tableau_extension) || intval($ligne->cmdl__image) == 1  || !empty($ligne->devl__note_client)) 
                    {
                        $border_bottom = '';
                    }
                  

                        //debut du gerbage du html 
                        $balise_tr_ouvrante =
                            "<tr style='font-size: 95%; font-style: italic;'>";
                        $premiere_cellule =
                            "<td valign='top' style='padding-top:" . $firstPadding . ";  ".$border_bottom."'>" . $prestation  . "</td>";
                        $deuxieme_cellule =
                            "<td valign='top' class='NoBR' style='padding-top:" . $firstPadding . ";  width: ".$secondW."; max-width: ".$secondW.";  text-align: left; ".$border_bottom." padding-bottom:10px'>"  . $designation . "</td>";
                        //condition pour etat = a NC. OU etat masque est demandé: 
                        if ($ligne->devl__etat == 'NC.' || $ligne->cmdl__etat_masque > 0) 
                        {
                            $troisieme_cellule =
                            "<td valign='top' style='padding-top:" . $firstPadding . ";  color: white ; text-align: center; ".$border_bottom."'>" . $ligne->kw__lib . "</td>";
                        } 
                        else 
                        {
                            $countEtat += 1;
                            $troisieme_cellule = "
                            <td valign='top' style='padding-top:" . $firstPadding . "; text-align: center; ".$border_bottom."'>" . $ligne->kw__lib . "</td>";
                        }
                        //condition pour garantie
                        if ($ligne->devl__mois_garantie > 0) 
                        {
                            $countGarantie += 1;
                            $quatrieme_cellule = "<td valign='top' style='padding-top:" . $firstPadding . ";  text-align: center; ".$border_bottom."'>" . $garantie . " </td>";
                        } 
                        else 
                        {
                            $quatrieme_cellule = "<td valign='top' style='padding-top:" . $firstPadding . "; color: white; text-align: center; ".$border_bottom."'>" . $garantie . " </td>";
                        }

                        $cinquieme_cellule  = "<td valign='top' style='padding-top:" . $firstPadding . "; text-align: center; ".$border_bottom." '>" . $quantité . "</td>";
                        $derniere_cellule = "<td valign='top' style='padding-top:" . $firstPadding . "; text-align: right; ".$border_bottom." padding-bottom:10px'>" . $barre . " " . $prix . "</td>";
                        $balise_tr_fermeture = "<br></tr> ";
                        $counter = 0;

                        //boucle sur les extensions de garanties:
                        foreach ($ligne->tableau_extension as $array) 
                        {
                            //variable qui indique le border bottom si cela est nécéssaire : 
                            $border_extension = '';
                            if ($array === end($ligne->tableau_extension)) 
                            {
                                $border_extension = 'border-bottom: 1px #ccc solid;';
                            }
                            //incremente counter de 1 : 
                            $counter = $counter + 1;
                            //creation d'une nouvelle ligne pour les extension de garantie : 
                            $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>";
                            //premiere cellule de la ligne des extensions de garantie :  
                            $premiere_cellule_2 = "<td valign='top' style='text-align: left; ".$border_extension." '>garantie</td>";
                            //si il s'agit d'une prestation de réparation l 'extension de garantie devient une mise sous-garantie ; 
                            if ($ligne->devl__type == "REP") 
                            {
                                $seconde_cellule_2 = "<td valign='top' style=' text-align: left; ".$border_extension." '>mise sous garantie du matériel réparé</td>";
                            }
                            else 
                            { 
                                if (is_int(intval($array['devg__type']) / 12 )) 
                                {
                                    $seconde_cellule_2 = "<td valign='top' style='  text-align: left;  ".$border_extension."'> extension de garantie à " . intval($array['devg__type'] / 12) . " ans - Hotline & ateliers en France</td>";
                                }
                                else
                                {
                                    $seconde_cellule_2 = "<td valign='top' style=' text-align: left;  ".$border_extension. "'> extension de garantie - Hotline & ateliers en France</td>";
                                }
                                
                            }
                            $troisieme_cellule_2 =  "<td valign='top' style=' text-align: left; ".$border_extension."'></td>";

                            $quatrieme_cellule_2 = "<td valign='top' style='  text-align: center; ".$border_extension."'>" . $array['devg__type'] . " mois </td>";
                            $cinquieme_cellule_2 = "<td valign='top' style='  text-align: center; ".$border_extension."'>" . $quantité . "</td>";
                            // si un prix barre d'extension de garantie est présent : 
                            if (!empty($array['cmdg__prix_barre']) && floatval($array['cmdg__prix_barre']) > 00) 
                            {
                                $derniere_cellule_2 = "<td valign='top' style='  text-align: right; ".$border_extension."'><s>" . number_format(floatVal($array['cmdg__prix_barre']), 2, ',', ' ') . "</s>€<br> " .   number_format(floatVal($array['devg__prix']), 2, ',', ' ') . " €</td>";
                            } 
                            else 
                            {
                                $derniere_cellule_2 = "<td valign='top' style='  text-align: right; ".$border_extension."'>" . number_format(floatVal($array['devg__prix']), 2, ',', ' ') . "€</td>";
                            }
                            //fermeture de la balise tr 
                            $balise_tr_fermeture_2 = "</tr> ";
                            //creation de la ligne d'extension: 
                            $ligne_extension = $seconde_balise_tr . $premiere_cellule_2 . $seconde_cellule_2 . $troisieme_cellule_2 . $quatrieme_cellule_2 . $cinquieme_cellule_2 . $derniere_cellule_2 . $balise_tr_fermeture_2;
                            $extension .= $ligne_extension;
                            $counter = 0;
                                    
                        }
                        
                    //Incrementation de la table pour chaque ligne: 
                    $table .=  $balise_tr_ouvrante . $premiere_cellule . $deuxieme_cellule . $troisieme_cellule . $quatrieme_cellule . $cinquieme_cellule . $derniere_cellule . $balise_tr_fermeture .$ligne_photo . $extension;
                    //fin de boucle sur les ligne :  
                }
                
                if ($countGarantie  == 0) 
                {
                    $stringGarantie = '';
                }
                if ($countEtat == 0)
                {
                    $stringEtat = '';
                }
                $tete =
                '<tr style=" margin-top : 50px;  background-color: #dedede; ">
                    <td style=" text-align: left;   padding-top: 4px; padding-bottom: 4px;  width: ' . $firstW . '; max-width: '. $firstW .';">Prestation</td>
                    <td style=" text-align: left; padding-top: 4px; padding-bottom: 4px; width: ' . $secondW . '; max-width: '. $secondW .';">Désignation</td>
                    <td style="text-align: center; padding-top: 4px; padding-bottom: 4px; width: ' . $thirdW . '; max-width: '. $thirdW .';">' . $stringEtat . '</td>
                    <td  style=" text-align: center; padding-top: 4px; padding-bottom: 4px; width: ' . $fourthW . '; max-width: '. $fourthW .';">' . $stringGarantie . '</td>
                    <td style="text-align: center; padding-top: 4px; padding-bottom: 4px; width: ' . $fifthW . '; max-width: '. $fifthW .';">Qté</td>
                    <td style="text-align: right; ; padding-top: 4px; padding-bottom: 4px; width: ' .$lastW . '; max-width: '. $lastW .';">P.u € HT</td>
                </tr> ';
                echo $tete . $table;
              
    }
}