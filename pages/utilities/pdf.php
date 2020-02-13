<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
session_start();


// Si un devis a été validé : 
if (!empty($_POST)) {
    $devisData = json_decode($_POST["dataDevis"]);


// fontion d'affichage du prix : 
    function showPrice($object){
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
    function showPrestation($object){
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
    };
// fonction d'affichage designation : 
    function showdesignation($object){
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
        return $designation . $sautDeLigne . $extension;
    }
// fonction d'affichage de garantie :
    function showGarantie($object){
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
    function showQuantite($object){
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

    ob_start();
    ?>
    <style type="text/css">
         strong{ color:#000;}
         h3{ color:#666666;}
         h2{ color:#3b3b3b;}
         table{ 
          border-collapse:separate; 
          border-spacing: 0 15px; 
            } 
            
        
    </style>
    <page backtop="5mm" backleft="20mm" backright="20mm">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;  width: 50%"><img  style=" width:65mm" src="http://localhost:8080/DevisRecode/public/img/recodeDevis.png"/></td>
                <td style="text-align: left; width:50%"><h3>Reparation-Location-Vente</h3>imprimantes- lecteurs codes-barres<br><a>www.recode.fr</a><br><br><br>REF CLIENT :<?php echo $_SESSION['Client']->client__id ?></td>
            </tr>
            <tr>
                <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h2>Devis- 3190808</h2><br>07/07/07<br><?php echo $_SESSION['user']->email ?><p><small>Notre offre est valable une semaine à dater du : 07/07/07</small></p></td>
                <td style="text-align: left; width:50%"><small>livraison & facturation</small><strong><br><?php echo $_SESSION['Client']->client__societe ?><br><?php echo $_SESSION['Client']->client__adr1 ?><br><?php echo $_SESSION['Client']->client__adr2 ?><br><?php echo $_SESSION['Client']->client__cp . $_SESSION['Client']->client__ville ?></strong></td>
            </tr>
        </table>
        <table CELLSPACING=0 style="width: 100%;  margin-top: 30px; ">
                <tr style=" margin-top : 50px; background-color: #dedede; " >
                   <td style="width: 18%; text-align: left;">Prestation</td><td style="width: 37%; text-align: left">Designation</td><td style="text-align: center">Type matériel</td><td  style="width: 12%; text-align: center">Garantie</td><td style="text-align: center; ">Qté</td><td style="text-align: center; width: 17%">P.u € HT</td>
                </tr> 

                <?php 
                    foreach($devisData as $value=>$obj){
                            echo "<tr style='font-size: 85%;'><td style='width: 18%; text-align: left; border-bottom: 1px #ccc solid'>" .showPrestation($obj)."</td>
                            <td valign='top' style='width: 37%; text-align: left; border-bottom: 1px #ccc solid'>" .showdesignation($obj). "</td>
                            <td valign='top' style='text-align: left; border-bottom: 1px #ccc solid'>" .$obj->etat ."</td>
                            <td valign='top' style='width: 12%; text-align: center; border-bottom: 1px #ccc solid'>" .showGarantie($obj) ."</td>
                            <td valign='top' style='text-align: center; border-bottom: 1px #ccc solid '>" .showQuantite($obj) ."</td>
                            <td valign='top' style='text-align: center; width: 20%; border-bottom: 1px #ccc solid'>" .showPrice($obj) ."</td>
                            <br></tr>";   
                    };
                ?>
        </table>
        <table style=" margin-top: 15px">
            <tr>
            <td style="width: 290px"></td>
            <td>
                <table CELLSPACING=0  style=" border: 1px black solid;">
                    <tr style="background-color: #dedede;"><td style="width: 155px; text-align: left">Type de Garantie</td><td style="text-align: center"><strong>total € HT</strong></td><td style="text-align: center">Total € TTC</td></tr>
                </table>
            </td>
            </tr>
        </table>
        <table style=" margin-top: 15px">
            <tr><td><strong>Conditions de paiement</strong> : Virement à la réception</td></tr>
            <?php
            // commentaire client global 
            ?>
        </table>
        <table CELLSPACING=0 style=" width: 100%; margin-top: 5px">
            <tr style="background-color: #dedede;"><td style="text-align: left;  width: 50%"><strong>BON POUR COMMANDE</strong><BR>NOM DU SIGNATAIRE: <br>VOTRE N° DE CDE :<br>DATE:</td><td style="text-align: right;  width: 50%; vertical-align:top;">CACHET & SIGNATURE</td></tr>
        </table>

        <table style=" margin-top: 10px; color: #8c8c8c; width: 100%;">
            <tr><td style="font-size: 80%;"><small>Le client accepte la présente proposition ainsi que les condition générales de vente Recode.<br>Recode conserve la propriété du matériel jusqu'a 
            paiement intégral du prix et des frais annexes.</small></td></tr>
            <tr>
                <td><small><strong>Coordonnées bancaires(Banque Populaire Méditéranée)</strong><br>
                 IBAN : FR76 1460 7003 6569 0218 9841 804- BIC: CCBPFRPPMAR</small></td>
            </tr>
            <tr >
                <td  style="text-align: center; font-size: 80%; width: 100%;"><br><small>New Eurocomputer-TVA FR33b 397 934 068 Siret 397 934 068 00016 - APE9511Z - SAS au capital 38112.25 €<br>
                <strong>RECODE by eurocomputeur - 112 allée François Coli -06210 Mandelieu</strong></small></td>
            </tr>
        </table>      
    </page>
    <?php
    $content = ob_get_contents();
    
    try {
        $doc = new Html2Pdf('P','A4','fr');
        $doc->pdf->SetDisplayMode('fullpage');
        $doc->writeHTML($content);
        ob_clean();
        $doc->output('exemple.pdf');
    } catch (Html2PdfException $e) {
      die($e); 
    }}
    else{ var_dump($_POST);}

   