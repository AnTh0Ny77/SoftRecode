<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Contact = new \App\Tables\Contact($Database);
$Client = new \App\Tables\Client($Database);

if (empty($_SESSION['user'])) {
    header('location: login');
 }
 if (!empty($_POST['VoirDevis'])) {
     
    $temp =   $Cmd->GetById($_POST['VoirDevis']);
 
    $clientView = $Client->getOne($temp->client__id);
    $societeLivraison = false ;

    if ($temp->devis__id_client_livraison) 
    {
        $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
    }

    $arrayOfDevisLigne = $Cmd->devisLigne($_POST['VoirDevis']);

    foreach ($arrayOfDevisLigne as $ligne) 
    {
        $xtendArray = $Cmd->xtenGarantie($ligne->devl__id);
        $ligne->ordre2 = $xtendArray;
    } 

$date_time = new DateTime($temp->devis__date_crea);
$formated_date = $date_time->format('d/m/Y'); 
$Keyword = new \App\Tables\Keyword($Database);
$garanties = $Keyword->getGaranties();
 ob_start();

 ?>
 
 <style type="text/css">
 
     .page_header{
        margin-left: 30px;
        margin-top: 30px;
     }

    
     
      table{   font-size:13; font-style: normal; font-variant: normal;  border-collapse:separate; }
     
      strong{ color:#000;}
      h3{ color:#666666;}
      h2{ color:#3b3b3b;}
     
 </style>


<page backtop="80mm" backleft="10mm" backright="10mm" backbottom= "30mm"> 
<page_header>
     <table class="page_header" style="width: 100%;">
         <tr>
             <td style="text-align: left;  width: 50%"><img  style=" width:65mm" src="public/img/recodeDevis.png"/></td>
             <td style="text-align: left; width:50%"><h3>REPARATION-LOCATION-VENTE</h3>imprimantes-lecteurs codes-barres<br><a style="color: green;">www.recode.fr</a><br><br></td>
         </tr>
         <tr>
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h2>Devis <?php echo $temp->devis__id ?></h2><br><?php echo date("d-m-Y") ?><br><?php echo $temp->email ?><p><small>Notre offre est valable une semaine à dater du : <?php echo $formated_date ?></small></p></td>
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
                            echo "<br> <small>livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        }
                        else {
                            // si pas de contact de livraison : 
                            echo "<br> <small>livraison :</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        } 
                    }

                    else {
                        echo "<small>facturation :</small><strong><br>";
                        echo Pdfunctions::showSociete($clientView) ." </strong>" ;
                        if ($temp->devis__contact_livraison) {
                            $contact2 = $Contact->getOne($temp->devis__contact_livraison);
                            echo "<br> <small>livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        } else {
                            echo "<br> <small>livraison :</small><strong><br>";
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
</page_header>
<page_footer>
        <table  class="page_footer" style="text-align: center; margin: auto; ">
            <tr >
                <td  style=" font-size: 80%; width: 100%;  "><br><br><small>New Eurocomputer-TVA FR33 397 934 068 Siret 397 934 068 00016 - APE9511Z - SAS au capital 38112.25 €<br>
                <strong>RECODE by Eurocomputer - 112 allée François Coli -06210 Mandelieu</strong></small></td>
            </tr>
         </table>
</page_footer>

<table CELLSPACING=0 style="margin-top: 15px; width:100%">
        
        <?php 
            $arrayPrice =[];
            foreach($arrayOfDevisLigne as $value=>$obj){
                    array_push( $arrayPrice, floatval(floatval($obj->devl_puht)*intval($obj->devl_quantite)));
            };      
            echo Pdfunctions::magicLine($arrayOfDevisLigne);     
        ?>
</table>

<div>
<table style=" margin-top: 25px;  width:100%;" >
    <tr>
    <td style="width: 45%;">  </td>
    <td align="right">

    <?php
    // on affiche le tableau de totaux en cas de modèle adéquate :

    if ($temp->cmd__modele_devis != 'STX') 
    {
        switch ($temp->cmd__modele_devis) 
        {
            // devis standart total classique:
            case 'STT':
               $totalPrice = array_sum($arrayPrice);
               $totaux = Pdfunctions::totalCon($arrayOfDevisLigne , $garanties , array_sum($arrayPrice) , true , $temp->tva_Taux);
                break;
            // devis standart total logique: 
            case 'STL':
               $totalPrice = array_sum($arrayPrice);
               $totaux = Pdfunctions::magicXtend($arrayOfDevisLigne , $garanties , array_sum($arrayPrice) , true );
               
                break;
            // devis sans TVA total classique: 
            case 'TVT':
                $totalPrice = array_sum($arrayPrice);
                $totaux = Pdfunctions::totalCon($arrayOfDevisLigne , $garanties , array_sum($arrayPrice) , false , $temp->tva_Taux );
                break;
            // devis sans TVA total logique: 
            case 'TVL':
                $totalPrice = array_sum($arrayPrice);
                $totaux = Pdfunctions::magicXtend($arrayOfDevisLigne , $garanties , array_sum($arrayPrice) , false );
                break;
            
            default:
                $totalPrice = array_sum($arrayPrice);
                $totaux = Pdfunctions::totalCon($arrayOfDevisLigne , $garanties , array_sum($arrayPrice) , true , $temp->tva_Taux);
               
                break;
        }
       echo '</table>';
    }

   
    ?>
    
    </td>
    </tr>
</table>
</div>

<div>
<?php
if ($temp->devis__note_client) {
   echo $temp->devis__note_client;
}

?>
</div>

<div>

    <table CELLSPACING=0 style=" width: 100%;  margin-bottom: 5px; margin-top: 25 px;">
    <tr style="background-color: #dedede;  "><td style="text-align: left;  width: 50%; padding-top: 7px; padding-bottom: 7px; padding-left:6px;"><strong>BON POUR COMMANDE</strong><BR>NOM DU SIGNATAIRE: <br>VOTRE N° DE CDE :<br>DATE:</td><td style="text-align: right;  width: 50%; vertical-align:top; padding-top: 7px; padding-right: 6px;">CACHET & SIGNATURE</td></tr>
</table>

<table style="width: 100%;">
           <table style="   margin-top: 5px; color: #8c8c8c; width: 100%;">
               <tr><td style="font-size: 80%;"><small>Le client accepte la présente proposition ainsi que les conditions générales de vente Recode.<br>Recode conserve la propriété du matériel jusqu'a 
               paiement intégral du prix et des frais annexes.</small></td></tr>
               <tr>
                   <td><small><strong>Coordonnées bancaires (Banque Populaire Méditerranée)</strong><br>
                   IBAN : FR76 1460 7003 6569 0218 9841 804- BIC: CCBPFRPPMAR</small></td>
               </tr>
           </table>  
</table>


</div> 
</page>
 <?php
 $content = ob_get_contents();

 if ($temp->cmd__nom_devis) {
  $name  = $temp->cmd__nom_devis;
 }
 else {
    $name = $temp->devis__id;
 }
 
 try {
     $doc = new Html2Pdf('P','A4','fr');
     $doc->setDefaultFont('gothic');
     $doc->pdf->SetDisplayMode('fullpage');
     $doc->writeHTML($content);
     ob_clean();
     $doc->output(''.$temp->devis__id.'-'.$name.'.pdf');
     unset( $_SESSION['Contact']);
     unset( $_SESSION['Client']);
     unset( $_SESSION['livraison']);
    
 } catch (Html2PdfException $e) {
   die($e); 
 }
}
