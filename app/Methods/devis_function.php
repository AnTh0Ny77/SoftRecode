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
                    //extensions
                    $extension = "";
                    if (!empty($ligne->tableau_extension)) 
                    {

                    }
                    
                    
                }
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