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
                        $firstPadding = '0px';
                    }
                    //si un commentaire client est présent il s'ajoute sous la désignation 
                    if (!empty($ligne->devl__note_client)) 
                    {
                        $designation =  $ligne->devl__designation .'<span style="margin-top: -10px;">'. $ligne->devl__note_client .'</span>';
                    }
                    else 
                    { 
                        $designation = $ligne->devl__designation; 
                    }
                    // garantie
                    $garantie = $ligne->devl__mois_garantie . " mois";
                    //prix barre 
                    if ($ligne->devl__prix_barre > 0 ) 
                    {
                        $barre = "<s>". number_format(floatVal($ligne->devl__prix_barre),2 , ',',' ') ." €</s><br>";
                    }
                    else
                    {
                        $barre = '';
                    }
                    // prix
                    if (!empty($ligne->devl_puht)) 
                    {
                        $prix = number_format(floatVal($ligne->devl_puht),2 , ',',' ')  ." €";
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
                        "<td valign='top' class='NoBR' style='  padding-top:".$firstPadding.";  width: ".$secondW."; max-width: ".$secondW."; text-align: left;  padding-bottom:15px'>"  . $designation. "</td>";
                        if ($ligne->devl__etat == 'NC.') 
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
                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW.";  text-align: right;   padding-bottom:15px'>" . $barre . " " . $prix ."</td>" ;
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
                                "<td valign='top' style=' width: ".$secondW."; max-width: ".$secondW.";  text-align: left;  '>mise sous garantie du matériel réparé optionnelle  </td>";
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
                            $derniere_cellule_2 = "<td valign='top' style=' width: ".$lastW."; max-width: ".$lastW."; text-align: right;'>" . number_format(floatVal($array['devg__prix']),2 , ',',' ') ." €</td>" ;
                            $balise_tr_fermeture_2 = "</tr> ";
                    
                            if ($array === end($ligne->tableau_extension)) 
                            {
                                $seconde_balise_tr = "<tr style='font-size: 95%; font-style: italic;'>" ;
                                $premiere_cellule_2 = "<td valign='top' style=' width:".$firstW."; text-align: left; border-bottom: 1px #ccc solid'>garantie</td>";
                                if ($ligne->devl__type == "REP") 
                                {
                                    $seconde_cellule_2 = "<td valign='top' style=' width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid; padding-bottom:15px '>mise sous garantie du matériel réparé optionnelle  </td>";
                                }
                                else 
                                {
                                    if (is_int(intval($array['devg__type'])/12) ) 
                                    {
                                        $seconde_cellule_2 = "<td valign='top' style=' border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie à ".intval($array['devg__type']/12)." ans </td>";
                                    }
                                    else 
                                    {
                                        $seconde_cellule_2 = "<td valign='top' style='border-bottom: 1px #ccc solid; width: ".$secondW."; max-width: ".$secondW."; text-align: left;  ;'> extension de garantie</td>";
                                    }
                            }
                    
                            $troisieme_cellule_2 =  "<td valign='top' style=' width: ".$thirdW."; text-align: left; border-bottom: 1px #ccc solid'></td>";
                            $quatrieme_cellule_2 = "<td valign='top' style=' width: ".$fourthW."; text-align: center; border-bottom: 1px #ccc solid'>" . $array['devg__type'] ." mois </td>";
                            $cinquieme_cellule_2 ="<td valign='top' style=' width:".$fifthW."; text-align: center; border-bottom: 1px #ccc solid '>" .$quantité ."</td>";
                            $derniere_cellule_2 = "<td valign='top' style=' width:".$lastW."; text-align: right;  border-bottom: 1px #ccc solid; padding-bottom:15px'>" . number_format(floatVal($array['devg__prix']),2 , "," , " ") ." €</td>" ;
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
                        "<td valign='top' class='NoBR' style='padding-top:".$firstPadding.";  width: ".$secondW.";  max-width: ".$secondW."; text-align: left; border-bottom: 1px #ccc solid ;  padding-bottom:15px'>"  . $designation. "</td>";
                        //condition pour etat = a NC. OU etat masque est demandé: 
                        if ($ligne->devl__etat == 'NC' || $ligne->cmdl__etat_masque > 0 ) 
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
                        "<td valign='top' style='padding-top:".$firstPadding."; width: ".$lastW."; max-width: ".$lastW."; text-align: right;  border-bottom: 1px #ccc solid; padding-bottom:15px'>" . $barre . " " . $prix ."</td>" ;
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























    public static function remise_devis_ligne_pdf()
    {

    }

    public static function classic_total_devis_pdf()
    {

    }

    public static function remise_total_devis_pdf()
    {
        
    }
}