<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Devis = new App\Tables\Devis($Database);
$Client = new App\Tables\Client($Database);
$Contact = new \App\Tables\Contact($Database);


if (empty($_SESSION['user'])) {
    header('location: login');
 }


// Si un devis a été validé : 
if (!empty($_POST)) {
    $devisData = json_decode($_POST["dataDevis"]);
    $date = date("Y-m-d H:i:s");
    $client = $Client->getOne($_POST['clientSelect']);
   

    // corrige la notice lié a l'accesion d'un non objet -> :
    $contactId = NULL;
    $livraisonId = NULL;
    $livraisonContact = NULL;
    if (!empty( $_POST['contactSelect'])) {
      $contactId = $_POST['contactSelect'];
      $contact = $Contact->getOne($_POST['contactSelect']);
    }
    if (!empty($_POST['livraisonSelect'])) {
        $livraisonId = $_POST['livraisonSelect'];
    }

    if (!empty($_POST['contact_livraison'])) {
        $livraisonContact = $_POST['contact_livraison'];
    }
    $status = 'ATN';


    if (!empty($_POST['ModifierDevis'])) {
        $devis = $Devis->Modify(
        intval($_POST['ModifierDevis']),
        $date,
        $_SESSION['user']->id_utilisateur,
        $_POST['clientSelect'],
        $livraisonId,
        $_POST['port'],
        $contactId,
        $_POST['globalComClient'],
        $_POST['globalComInt'],
        $status,
        NULL,
        $devisData , 
        $livraisonContact 
      );
    } else {
        $devis = $Devis->insertOne(
            $date,
            $_SESSION['user']->id_utilisateur,
            $_POST['clientSelect'],
            $livraisonId,
            $_POST['port'],
            $contactId,
            $_POST['globalComClient'],
            $_POST['globalComInt'],
            $status,
            NULL,
            $devisData,
            $livraisonContact );
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
    <page backtop="15mm" backleft="15mm" backright="15mm">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;  width: 50%"><img  style=" width:65mm" src="public/img/recodeDevis.png"/></td>
                <td style="text-align: left; width:50%"><h3>Reparation-Location-Vente</h3>imprimantes- lecteurs codes-barres<br><a>www.recode.fr</a><br><br><br>REF CLIENT :<?php echo $client->client__id ?></td>
            </tr>
            <tr>
                <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h2>Devis-<?php echo $devis ?></h2><br><?php echo date("d-m-Y") ?><br><?php echo $_SESSION['user']->email ?><p><small>Notre offre est valable une semaine à dater du : <?php  echo date("d-m-Y") ?></small></p></td>
                <td style="text-align: left; width:50%"><small>livraison & facturation</small><strong><br><?php echo $client->client__societe ?><br><?php echo $client->client__adr1 ?><br><?php echo $client->client__adr2 ?><br><?php echo $client->client__cp ." ". $client->client__ville ?></strong></td>
            </tr>
        </table>
        <table CELLSPACING=0 style="width: 100%;  margin-top: 30px; ">
                <tr style=" margin-top : 50px; background-color: #dedede; " >
                   <td style="width: 18%; text-align: left;">Prestation</td><td style="width: 37%; text-align: left">Designation</td><td style="text-align: center">Type matériel</td><td  style="width: 12%; text-align: center">Garantie</td><td style="text-align: center; ">Qté</td><td style="text-align: center; width: 17%">P.u € HT</td>
                </tr> 

                <?php 
                    $arrayPrice =[];
                    $arrayGarantie = [];
                    $array12 = [];
                    $array24 = [];
                    $array36 = [];
                    $array48 = [];
                    foreach($devisData as $value=>$obj){
                            echo "<tr style='font-size: 85%;'>
                            <td valign='top' style='width: 18%; text-align: left; border-bottom: 1px #ccc solid'>" .Pdfunctions::showPrestation($obj)."</td>
                            <td valign='top' style='width: 37%; text-align: left; border-bottom: 1px #ccc solid ; padding-bottom:15px'>" .Pdfunctions::showdesignation($obj). "</td>
                            <td valign='top' style='text-align: left; border-bottom: 1px #ccc solid'>" .$obj->etat ."</td>
                            <td valign='top' style='width: 12%; text-align: center; border-bottom: 1px #ccc solid'>" .Pdfunctions::showGarantie($obj) ."</td>
                            <td valign='top' style='text-align: center; border-bottom: 1px #ccc solid '>" .Pdfunctions::showQuantite($obj) ."</td>
                            <td valign='top' style='text-align: center; width: 20%; border-bottom: 1px #ccc solid; padding-bottom:15px'>" . Pdfunctions::showPrice($obj) ."</td>
                            <br></tr> "; 
                            $xtendTotal = Pdfunctions::xTendTotal($obj->xtend);
                            $price12 = array_sum($xtendTotal[0]);
                            $price24 = array_sum($xtendTotal[1]);
                            $price36 = array_sum($xtendTotal[2]);
                            $price48 = array_sum($xtendTotal[3]);
                           
                            if ($price12 > 0 ) {
                                array_push($array12 , floatval(floatval($price12)*intval($obj->quantite)));
                             }
                             if ($price24 > 0 ){
                                array_push($array24 , floatval(floatval($price24)*intval($obj->quantite)));
                             }
                             if ($price36 > 0) {
                                array_push($array36 , floatval(floatval($price36)*intval($obj->quantite)));
                             }
                             if( $price48 > 0){
                                array_push($array48 , floatval(floatval($price48)*intval($obj->quantite)));
                             }
                             array_push( $arrayPrice, floatval(floatval($obj->prix)*intval($obj->quantite)));
                    };
                    
                            echo "<tr style='font-size: 85%;'>
                            <td valign='top' style='width: 18%; text-align: left; border-bottom: 1px #ccc solid'>port</td>
                            <td valign='top' style='width: 37%; text-align: left; border-bottom: 1px #ccc solid'></td>
                            <td valign='top' style='text-align: left; border-bottom: 1px #ccc solid'></td>
                            <td valign='top' style='width: 12%; text-align: center; border-bottom: 1px #ccc solid'></td>
                            <td valign='top' style='text-align: center; border-bottom: 1px #ccc solid '></td>
                            <td valign='top' style='text-align: center; width: 20%; padding-bottom:15px; border-bottom: 1px #ccc solid'>" .Pdfunctions::showPort($_POST['port']) ."</td>
                            </tr>";
                            array_push( $arrayPrice, floatval($_POST['port']));
                ?>
        </table>
        <table style=" margin-top: 15px">
            <tr>
            <td style="width: 290px"></td>
            <td>
                <table CELLSPACING=0  style=" border: 1px black solid;">
                    <tr style="background-color: #dedede;"><td style="width: 210px; text-align: left">Type de Garantie </td><td style="text-align: center"><strong>total € HT </strong></td><td style="text-align: center">Total € TTC</td></tr>
                    <?php
                        $totalPrice = number_format(array_sum($arrayPrice),2);
                       
                          echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>Total hors extensions</td><td style='text-align: center'><strong>  ".$totalPrice. "  </strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc($totalPrice),2)." </td></tr>";
                          if (sizeOf($array12) == sizeof($devisData)) {
                            array_push($array12 , floatval($totalPrice));
                            $total12Mois = number_format(array_sum($array12),2);
                          echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>Total extensions 12 mois</td><td style='text-align: center'><strong>  ".$total12Mois. "  </strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc($total12Mois),2)." </td></tr>";
                          }
                          if (sizeOf($array24) == sizeof($devisData)) {
                           array_push($array24 , floatval($totalPrice));
                            $total24Mois = number_format(array_sum($array24),2);
                          echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>Total extensions 24 mois</td><td style='text-align: center'><strong>  ".$total24Mois. "  </strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc($total24Mois),2)." </td></tr>";
                          }
                          if (sizeOf($array36) == sizeof($devisData)) {
                           array_push($array36 , floatval($totalPrice));
                            $total36Mois = number_format(array_sum($array36),2);
                          echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>Total extensions 36 mois</td><td style='text-align: center'><strong>  ".$total36Mois. "  </strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc($total36Mois),2)." </td></tr>";
                          }
                          if (sizeOf($array48) == sizeof($devisData)) {
                           array_push($array48 , floatval($totalPrice));
                            $total48Mois = number_format(array_sum($array48),2);
                          echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>Total extensions 48 mois</td><td style='text-align: center'><strong>  ".$total48Mois. "  </strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc($total48Mois),2)." </td></tr>";
                          }
                       
                    ?>
                </table>
            </td>
            </tr>
        </table>

        <div style=" width: 100%; position: absolute; top:73%">
       
        <table style=" margin-top: 15px">
            <tr><td><strong>Conditions de paiement</strong> : Virement à la réception</td></tr>
            <?php
            if (!empty($_POST['globalComClient'])) {
               echo '<tr><td>' . $_POST['globalComClient'] .'</td></tr>';
            }
            ?>
        </table>
        <table CELLSPACING=0 style=" width: 100%; margin-top: 5px; margin-bottom: 15px;">
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
        </div>  
    </page>
    <?php
    $content = ob_get_contents();
    
    
    try {
        $doc = new Html2Pdf('P','A4','fr');
        $doc->pdf->SetDisplayMode('fullpage');
        $doc->writeHTML($content);
        ob_clean();
        $doc->output('devisN:' . $devis.'.pdf');
        unset( $_SESSION['livraison']);
       
    } catch (Html2PdfException $e) {
      die($e); 
    }

   

}
    

   