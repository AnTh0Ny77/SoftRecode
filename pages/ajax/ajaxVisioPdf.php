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
$Contact = new \App\Tables\Contact($Database);
$Keyword = new \App\Tables\Keyword($Database);


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
      $ligne->ordre2 = $xtendArray;
    } 

$date_time = new DateTime( $temp->devis__date_crea);
$formated_date = $date_time->format('d/m/Y'); 
$garanties = $Keyword->getGaranties();
 ob_start();

 ?>
 
 <style type="text/css">
      table{   font-size: 15px; font-style: normal; font-variant: normal;}
     
      strong{ color:#000;}
      h3{ color:#666666;}
      h2{ color:#3b3b3b;}
      table{ 
       border-collapse:separate; 
      
         }  
 </style>
 <page backtop="5mm" backleft="10mm" backright="10mm">
     <table style="width: 100%;">
         <tr>
             <td style="text-align: left;  width: 50%"><img  style=" width:65mm" src="public/img/recodeDevis.png"/></td>
             <td style="text-align: left; width:50%"><h3>REPARATION-LOCATION-VENTE</h3>imprimantes lecteurs codes barres<br><a style="color: green;">www.recode.fr</a><br><br></td>
         </tr>
         <tr>
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h2>Devis <?php echo $temp->devis__id ?></h2><br><?php echo date("d-m-Y") ?><br><?php echo $_SESSION['user']->email ?><p><small>Notre offre est valable une semaine à dater du : <?php echo $formated_date ?></small></p></td>
             <td style="text-align: left; width:50%"><?php 
             // si une societe de livraion est présente 
             if ($societeLivraison) {

                    if ($temp->devis__contact__id) {
                        // si un contact est présent dans l'adresse de facturation :
                        $contact = $Contact->getOne($temp->devis__contact__id);
                        echo "<small>facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom. "</small><strong><br>";
                        echo Pdfunctions::showSociete($clientView) ." </strong> ";
                    
                        if ($temp->devis__contact_livraison) {
                            //si un contact est présent dans l'adresse de livraison : 
                            $contact2 = $Contact->getOne($temp->devis__contact_livraison);
                            echo "<br> <small>Livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        }
                        else {
                            // si pas de contact de livraison : 
                            echo "<br> <small>Livraison :</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        } 
                    }

                    else {
                        echo "<small>facturation :</small><strong><br>";
                        echo Pdfunctions::showSociete($clientView) ." </strong>" ;
                        if ($temp->devis__contact_livraison) {
                            $contact2 = $Contact->getOne($temp->devis__contact_livraison);
                            echo "<br> <small>Livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        } else {
                            echo "<br> <small>Livraison :</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        }  
                    }  
             } 



             else{
                if ($temp->devis__contact__id) {
                $contact = $Contact->getOne($temp->devis__contact__id);
                echo "<small>livraison & facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom."</small><strong><br>";
                echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }
                else{
                    echo "<small>livraison & facturation : </small><strong><br>";
                    echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }

             } 
             ?>
         </tr>
     </table>
     <table CELLSPACING=0 style="width: 760px;  margin-top: 65px;   ">
             <tr style=" margin-top : 50px; background-color: #dedede;  " >
                <td style=" text-align: left;   padding-top: 4px; padding-bottom: 4px;">Prestation</td>
                <td style=" text-align: left; padding-top: 4px; padding-bottom: 4px;">Designation</td>
                <td style="text-align: center; padding-top: 4px; padding-bottom: 4px;"></td>
                <td  style=" text-align: center; padding-top: 4px; padding-bottom: 4px;"></td>
                <td style="text-align: center; padding-top: 4px; padding-bottom: 4px;">Qté</td>
                <td style="text-align: right; ; padding-top: 4px; padding-bottom: 4px;">P.u € HT</td>
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
                         
                         array_push( $arrayPrice, floatval(floatval($obj->devl_puht)*intval($obj->devl_quantite)));
                 };
                 
                       
                        
             ?>
     </table>
     <table style=" margin-top: 25px">
         <tr>
         <td style="width: 250px"></td>
         <td>

         <?php
         // on affiche le tableau de totaux en cas de modèle adéquate :
            if ($temp->cmd__modele_devis == 'STT') {

            $typeG = 'Type de garantie ';
            $libCheck = '<input type="checkbox"> hors garanties';

            $totable =  '<table CELLSPACING=0  style=" border: 1px black solid;">
            <tr style="background-color: #dedede;">
            <td style="width: 210px; text-align: left">'. $typeG.' </td>
            <td style="text-align: center; width: 85px;"><strong>Total € HT </strong></td>
            <td style="text-align: center">Total € TTC</td>
            </tr>';

            $totalPrice = array_sum($arrayPrice);
            echo $totable;
            echo  "<tr><td style='width: 210px; text-align: left'>". $libCheck."</td>
            <td style='text-align: center'><strong>  ". number_format($totalPrice,2  ,',', ' ') . " €</strong></td>
            <td style='text-align: right'> " .number_format(Pdfunctions::ttc(floatval($totalPrice)),2 ,',', ' ')." €</td>
            </tr>";
            $totaux = Pdfunctions::magicXtend($arrayOfDevisLigne , $garanties , array_sum($arrayPrice));
            echo '</table>';
            }
            
                
                 
                    
                    
                 ?>
             
         </td>
         </tr>
     </table>

     <div style=" width: 100%; position: absolute; top:78%">
    
   
     <table CELLSPACING=0 style=" width: 100%;  margin-bottom: 5px;">
         <tr style="background-color: #dedede;  "><td style="text-align: left;  width: 50%; padding-top: 7px; padding-bottom: 7px; padding-left:6px;"><strong>BON POUR COMMANDE</strong><BR>NOM DU SIGNATAIRE: <br>VOTRE N° DE CDE :<br>DATE:</td><td style="text-align: right;  width: 50%; vertical-align:top; padding-top: 7px; padding-right: 6px;">CACHET & SIGNATURE</td></tr>
     </table>

     <table style=" margin-top: 5px; color: #8c8c8c; width: 100%;">
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