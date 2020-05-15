<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Client = new \App\Tables\Client($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['AjaxDevis'])){
    $temp =   $Cmd->GetById($_POST['AjaxDevis']);
    $clientView = $Client->getOne($temp->client__id);
    $societeLivraison = false ;
    if ($temp->devis__id_client_livraison) {
        $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
    }
    $arrayOfDevisLigne = $Cmd->devisLigne($_POST['AjaxDevis']);
    foreach ($arrayOfDevisLigne as $ligne) {
      $xtendArray = $Cmd->xtenGarantie($ligne->devl__id);
      $ligne->ordre = $xtendArray;
    } 
$date_time = new DateTime( $temp->devis__date_crea);
$formated_date = $date_time->format('d/m/Y'); 
 ob_start();

 ?>
 
 <style type="text/css">
      table{   font-size: 15px; font-style: normal; font-variant: normal;}
     
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
             <td style="text-align: left; width:50%"><h3>REPARATION-LOCATION-VENTE</h3>imprimantes lecteurs codes barres<br><a style="color: green;">www.recode.fr</a><br><br></td>
         </tr>
         <tr>
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h2>Devis <?php echo $temp->devis__id ?></h2><br><?php echo date("d-m-Y") ?><br><?php echo $_SESSION['user']->email ?><p><small>Notre offre est valable une semaine à dater du : <?php echo $formated_date ?></small></p></td>
             <td style="text-align: left; width:50%"><?php 
             if ($societeLivraison) {
                echo "<small>facturation :</small><strong><br>";
                echo Pdfunctions::showSociete($clientView) ." </strong> 
                <br> <small>Livraison :</small><strong><br>";
                echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 



                
                
             } 
             else{
                echo "<small>livraison & facturation</small><strong><br>";
                echo Pdfunctions::showSociete($clientView)  ."</strong></td>";

             } ?>
         </tr>
     </table>
     <table CELLSPACING=0 style="width: 100%;  margin-top: 50px;  ">
             <tr style=" margin-top : 50px; background-color: #dedede; " >
                <td style="width: 18%; text-align: left;  padding-top: 4px; padding-bottom: 4px;">Prestation</td><td style="width: 37%; text-align: left; padding-top: 4px; padding-bottom: 4px;">Designation</td><td style="text-align: center; padding-top: 4px; padding-bottom: 4px;"></td><td  style="width: 12%; text-align: center; padding-top: 4px; padding-bottom: 4px;"></td><td style="text-align: center; padding-top: 4px; padding-bottom: 4px;">Qté</td><td style="text-align: center; width: 17%; padding-top: 4px; padding-bottom: 4px;">P.u € HT</td>
             </tr> 
             <?php 
                 $arrayPrice =[];
                 $arrayGarantie = [];
                 $array12 = [];
                 $array24 = [];
                 $array36 = [];
                 $array48 = [];
                 foreach($arrayOfDevisLigne as $value=>$obj){
                        

                        echo Pdfunctions::magicLine($obj);
                         $xtendTotal = Pdfunctions::xTendTotalView($obj->ordre);
                         $price12 = array_sum($xtendTotal[0]);
                         $price24 = array_sum($xtendTotal[1]);
                         $price36 = array_sum($xtendTotal[2]);
                         $price48 = array_sum($xtendTotal[3]);

                         if ($price12 > 0 ) {
                            array_push($array12 , floatval(floatval($price12)*intval($obj->devl_quantite)));
                         }
                         if ($price24 > 0 ){
                            array_push($array24 , floatval(floatval($price24)*intval($obj->devl_quantite)));
                         }
                         if ($price36 > 0) {
                            array_push($array36 , floatval(floatval($price36)*intval($obj->devl_quantite)));
                         }
                         if( $price48 > 0){
                            array_push($array48 , floatval(floatval($price48)*intval($obj->devl_quantite)));
                         }
                         array_push( $arrayPrice, floatval(floatval($obj->devl_puht)*intval($obj->devl_quantite)));
                 };
                 
                         echo "<tr style='font-size: 85%;  font-style: italic;'>
                         <td valign='top' style='width: 18%; text-align: left; border-bottom: 1px #ccc solid'>port</td>
                         <td valign='top' style='width: 37%; text-align: left; border-bottom: 1px #ccc solid'></td>
                         <td valign='top' style='text-align: left; border-bottom: 1px #ccc solid'></td>
                         <td valign='top' style='width: 12%; text-align: center; border-bottom: 1px #ccc solid'></td>
                         <td valign='top' style='text-align: center; border-bottom: 1px #ccc solid '></td>
                         <td valign='top' style='text-align: center; width: 20%; padding-bottom:15px; border-bottom: 1px #ccc solid'>" . number_format(Pdfunctions::showPort($temp->devis__port),2) ." €</td>
                         </tr>";
                         array_push( $arrayPrice, floatval($temp->devis__port));
             ?>
     </table>
     <table style=" margin-top: 25px">
         <tr>
         <td style="width: 270px"></td>
         <td>
             <table CELLSPACING=0  style=" border: 1px black solid;">
                 <tr style="background-color: #dedede;"><td style="width: 210px; text-align: left">Type de Garantie </td><td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td><td style="text-align: center">Total € TTC</td></tr>
                 <?php
                     $totalPrice = number_format(array_sum($arrayPrice),2);
                       
                       echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>hors garanties</td><td style='text-align: center'><strong>  ".$totalPrice. " €</strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc(floatval($totalPrice)),2)." €</td></tr>";
                       
                       if (sizeOf($array12) == sizeof($arrayOfDevisLigne)) {
                         array_push($array12 , floatval($totalPrice));
                         $total12Mois = number_format(array_sum($array12),2);
                       echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>garantie 12 mois</td><td style='text-align: center'><strong>  ".$total12Mois. " €</strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc( floatval($total12Mois)),2)." €</td></tr>";
                       }
                       if (sizeOf($array24) == sizeof($arrayOfDevisLigne)) {
                        array_push($array24 , floatval($totalPrice));
                         $total24Mois = number_format(array_sum($array24),2);
                       echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>garentie 24 mois</td><td style='text-align: center'><strong>  ".$total24Mois. " €</strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc(floatval($total24Mois)),2)." €</td></tr>";
                       }
                       if (sizeOf($array36) == sizeof($arrayOfDevisLigne)) {
                        array_push($array36 , floatval($totalPrice));
                         $total36Mois = number_format(array_sum($array36),2);
                       echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>garantie 36 mois</td><td style='text-align: center'><strong>  ".$total36Mois. " €</strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc(floatval($total36Mois)),2)." €</td></tr>";
                       }
                       if (sizeOf($array48) == sizeof($arrayOfDevisLigne)) {
                        array_push($array48 , floatval($totalPrice));
                         $total48Mois = number_format(array_sum($array48),2);
                       echo  "<tr><td style='width: 210px; text-align: left'><input type='checkbox'>Total extensions 48 mois</td><td style='text-align: center'><strong>  ".$total48Mois. " €</strong></td><td style='text-align: center'> " .number_format(Pdfunctions::ttc(floatval($total48Mois)),2)." €</td></tr>";
                       }
                    
                 ?>
             </table>
         </td>
         </tr>
     </table>

     <div style=" width: 100%; position: absolute; top:70%">
    
     <table style=" margin-top: 15px">
         <tr><td><strong>Conditions de paiement</strong> : Virement à la réception</td></tr>
         <?php
         if (!empty($temp->devis__note_client)) {
            echo '<tr><td>' . $temp->devis__note_client .'</td></tr>';
         }
         ?>
     </table>
     <table CELLSPACING=0 style=" width: 100%; margin-top: 15px; margin-bottom: 15px;">
         <tr style="background-color: #dedede;  "><td style="text-align: left;  width: 50%; padding-top: 7px; padding-bottom: 7px;"><strong>BON POUR COMMANDE</strong><BR>NOM DU SIGNATAIRE: <br>VOTRE N° DE CDE :<br>DATE:</td><td style="text-align: right;  width: 50%; vertical-align:top; padding-top: 7px;">CACHET & SIGNATURE</td></tr>
     </table>

     <table style=" margin-top: 10px; color: #8c8c8c; width: 100%;">
         <tr><td style="font-size: 80%;"><small>Le client accepte la présente proposition ainsi que les condition générales de vente Recode.<br>Recode conserve la propriété du matériel jusqu'a 
         paiement intégral du prix et des frais annexes.</small></td></tr>
         <tr>
             <td><small><strong>Coordonnées bancaires(Banque Populaire Méditéranée)</strong><br>
              IBAN : FR76 1460 7003 6569 0218 9841 804- BIC: CCBPFRPPMAR</small></td>
         </tr>
         <tr >
             <td  style="text-align: center; font-size: 80%; width: 100%;"><br><br><small>New Eurocomputer-TVA FR33b 397 934 068 Siret 397 934 068 00016 - APE9511Z - SAS au capital 38112.25 €<br>
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
     $doc->output(__DIR__ .'/'.$_SESSION['user']->log_nec.'devis.pdf', 'F');
     unset( $_SESSION['Contact']);
     unset( $_SESSION['Client']);
     unset( $_SESSION['livraison']);
     
 } catch (Html2PdfException $e) {
   die($e); 
 }
 echo  json_encode($temp);

 }
 else {
    echo 'request failed';
 }

}