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

if (empty($_SESSION['user']))
 {
    header('location: login');
 }

 // si une commande à été postée: 
 if (!empty($_POST['hiddenCommentaire'])) 
 {
   $Cmd->updateQuantiteFTC($_POST['hiddenCommentaire']);
   //  2  changer le status de la commande et attribuer un numero de facture:
   $Cmd->commande2facture($_POST['hiddenCommentaire']);
   //  4 activer une alert pour indiquer le bon fonctionnement du logiciel 
   $Cmd->classicReliquat($_POST['hiddenCommentaire']);
   // 5 reliquat si article deja facturé mais pas livré : 
   $Cmd->FactureReliquat($_POST['hiddenCommentaire']);

   //  3 enregistrer la facture au format pdf dans un folder 
     
    $temp =   $Cmd->GetById($_POST['hiddenCommentaire']);
 
    $clientView = $Client->getOne($temp->client__id);
    $societeLivraison = false ;

    if ($temp->devis__id_client_livraison) 
    {
        $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
    }

    $arrayOfDevisLigne = $Cmd->devisLigne($_POST['hiddenCommentaire']);

    foreach ($arrayOfDevisLigne as $ligne) 
    {
        $xtendArray = $Cmd->xtenGarantie($ligne->devl__id);
        $ligne->ordre2 = $xtendArray;
    } 

$date_time = new DateTime( $temp->cmd__date_cmd);
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
             <td  style="text-align: left;  width: 50% ; margin-left: 25%; padding-top: 20px;">

             
          
             <?php 
             if (!empty($clientView->client__tva_intracom)) 
             {
                echo 'TVA intracom : '. $clientView->client__tva_intracom . ' ';
             } else  echo 'TVA intracom : Non renseigné';
              
             ?><br>
             votre cde n° :<small> <?php echo $temp->cmd__code_cmd_client ; ?> </small><br>
             date commande :  <?php echo $formated_date ; ?><br>
             notre B.L n° : <?php echo $temp->devis__id ; ?>
            <br><br>
            
             <small >
             Livraison:<br>
             <b>
             <?php  echo Pdfunctions::showSociete($societeLivraison) ?>
             </b>
             </small>
            </td>
             <td style="text-align: left; width:50%">
             <h3>Facture : <?php echo $temp->cmd__id_facture ; ?></h3><br>
             <?php 
             // si une societe de livraion est présente 
           
            
                if ($temp->devis__contact__id) 
                {
                $contact = $Contact->getOne($temp->devis__contact__id);
                echo "<small>facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom."</small><strong><br>";
                echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }
                else
                {
                    echo "<small>facturation : </small><strong><br>";
                    echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }

             
             ?>
              
             <br>
            
         </tr>
     </table>
</page_header>
<page_footer>
<div >
<table style=" margin-bottom: 30px;  width:100%;" >
    <tr>
    <td style="width: 60%;">  </td>
    <td align="right">

     <table style="border: 1px solid black; background-color: lightgray;">
            <tbody> 
                <tr>  
            <?php

                $totaux = Pdfunctions::totalFacturePDF($temp, $arrayOfDevisLigne);


                echo "<td style='text-align: left; width: 175px;'>
                        TOTAL HORS TAXES :<br>
                        TAUX TVA :<br>
                        TOTAL TVA :<br>
                        TOTAL TOUTES TAXES:
                     </td>
                       


                     <td style='text-align: right; '>
                     <b>".number_format($totaux[0] , 2)." €</b><br>
                     <b>".number_format($totaux[1] , 2)." %</b><br>
                     <b>".number_format($totaux[2] , 2)." €</b><br>
                     <b>".number_format($totaux[3] , 2)." €</b><br>
                     </td>";
            ?>
            </tr>
            </tbody>
    </table>
    
    </td>
    </tr>
</table>
</div>
<table CELLSPACING=0 style=" width: 100%;  margin-bottom: 5px; margin-top: 35 px;">
    <tr><td style="text-align: center;  width: 100%; padding-top: 7px; padding-bottom: 7px; padding-left:6px;">Condition et payement à réception/Condition générale de Vente.
     Des pénalités de retard au taux légal seront appliquées en cas de paiement aprés la date d'échéance. Conformément à la loi du 12.05.80, EUROCOMPUTEUR conserve la propriété du matériel jusqu'au paiement intégral du prix et des frais annexes
    </td></tr>
    </table>
        <table  class="page_footer" style="text-align: center; margin: auto; ">
            <tr >
                <td  style=" font-size: 80%; width: 100%;  "><br><br><small>New Eurocomputer-TVA FR33b 397 934 068 Siret 397 934 068 00016 - APE9511Z - SAS au capital 38112.25 €<br>
                <strong>RECODE by eurocomputeur - 112 allée François Coli -06210 Mandelieu</strong></small></td>
            </tr>
         </table>
</page_footer>

<div style="margin-top: 35px;">
    <table CELLSPACING=0 style="margin-top: 35px; width:100%">
            
            <?php 
                $arrayPrice =[];
                foreach($arrayOfDevisLigne as $value=>$obj){
                        array_push( $arrayPrice, floatval(floatval($obj->devl_puht)*intval($obj->devl_quantite)));
                };      
                echo Pdfunctions::magicLineFTC($arrayOfDevisLigne , $temp);     
            ?>
    </table>
</div>



<div>
<?php
if ($temp->devis__note_client) {
   echo $temp->devis__note_client;
}

?>
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
     $doc->output('C:\laragon\www\factures/Facture'.$temp->cmd__id_facture.'.pdf' , 'F');
     
     header('location: facture');
    
 } catch (Html2PdfException $e) {
   die($e); 
 }
}
