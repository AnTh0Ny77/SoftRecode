<?php 
require "./vendor/autoload.php";


use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();

//declaration de objets nécéssaires : 
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Contact = new \App\Tables\Contact($Database);
$Client = new \App\Tables\Client($Database);
$General = new App\Tables\General($Database);

if(empty($_SESSION['user']))
{
    header('location: login');
}

// si une commande à été postée: 
if (!empty($_POST['PrintFormat'])) 
{
    
    
    // 3 enregistrer la facture au format pdf dans un folder 
    $temp = $Cmd->GetById($_POST['PrintFormat']);

    //client livré et facturé : 
    $clientView = $Client->getOne($temp->client__id);
    $societeLivraison = false ;

    if($temp->devis__id_client_livraison) 
    {
        $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
    }
    else 
    {
        $societeLivraison = $Client->getOne($temp->client__id);
    }

    //ligne de devis : 
    $arrayOfDevisLigne = $Cmd->devisLigne($temp->devis__id);
  
    foreach($arrayOfDevisLigne as $ligne) 
    {
        $xtendArray = $Cmd->xtenGarantie($ligne->devl__id);
        $ligne->ordre2 = $xtendArray;
    } 


    //gestion des dates : 
    $dateFact = new DateTime( $temp->cmd__date_fact);
    $formate = $dateFact->format('d/m/Y'); 
    $date_time = new DateTime( $temp->cmd__date_cmd);
    $formated_date = $date_time->format('d/m/Y'); 
    $Keyword = new \App\Tables\Keyword($Database);
    $garanties = $Keyword->getGaranties();

    //Debut de l'enregistrement: 
    ob_start();

 ?>

 <style type="text/css">
     .page_header{margin-left: 30px;margin-top: 30px;}
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
            Votre cde n° :<?php echo $temp->cmd__code_cmd_client ; ?> <br>
            Date :  <?php echo $formated_date ; ?><br>
            Notre B.L n° : <?php echo $temp->devis__id ; ?><br>
            <br>
            <small>Livraison:<br>
            <b><?php  
            echo Pdfunctions::showSociete($societeLivraison) ?></b></small>
            </td>
             <td style="text-align: left; width:50%">
             <h3>Facture PROFORMA n°<?php echo $temp->devis__id; ?></h3><br>
             
             <?php 
             // si une societe de livraion est présente 
                if ($temp->devis__contact__id) 
                {
                $contact = $Contact->getOne($temp->devis__contact__id);
                echo "<div style='background-color: #dedede;  padding: 15px 15px 15px 15px; border: 1px solid black;  width: 280px; '><strong>";
                echo Pdfunctions::showSocieteFacture($clientView,$contact) ." </strong></div></td>";
                }
                else
                {
                echo "<div style='background-color: #dedede; padding: 15px 15px 15px 15px; border: 1px solid black;  width: 280px;'><strong>";
                echo Pdfunctions::showSociete($clientView)  ." </strong></div></td>";
                }

             ?>
             
             <br>
            
         </tr>
     </table>
</page_header>
<page_footer>
<div >
<table style=" margin-bottom: 30px;  width:100%;">
    <tr>
    <td style="width: 55%;">  </td>
    <td align="right">

     <table style="border: 1px solid black; background-color: #dedede; padding: 10px 10px 10px 10px; font-size: 120%;">
            <tbody> 
                <tr>  
            <?php

                $totaux = Pdfunctions::totalFacturePRO($temp, $arrayOfDevisLigne);


                echo "<td style='text-align: left; width: 200px;'>
                        Total Hors Taxes :<br>
                        Total Tva ".number_format($totaux[1] , 2,',', ' ')." %:<br>
                        Total Toutes Taxes:<br>
                     </td>
                     <td style='text-align: right; '>
                     <b>".number_format($totaux[0] , 2,',', ' ')." €</b><br>
                     <b>".number_format($totaux[2] , 2,',', ' ')." €</b><br>
                     <b>".number_format($totaux[3] , 2,',', ' ')." €</b><br>
                     </td>";
            ?>
            </tr>
            </tbody>
    </table>
    
    </td>
    </tr>
</table>
</div>
<table  style="width:100%;  margin-bottom: 5px; margin-top: 35 px;">
    <tr>
    <td style="text-align: left;  width: 100%; padding-top: 7px; padding-bottom: 7px; padding-left:6px;">Conditions de paiement à réception/Conditions générales de Vente.
     Des pénalités de retard au taux légal seront appliquées en cas de paiement après la date d'échéance. Conformément à la loi du 12.05.80, EUROCOMPUTER conserve la propriété du matériel jusqu'au paiement intégral du prix et des frais annexes.
    <hr>
    </td>
    </tr>
    
</table>
        <table  class="page_footer" style="text-align: center; margin: auto; ">
        <tr>
            <td style="text-align: left; ">
                TVA: FR33 397 934 068<br>
                Siret 397 937 068 00016 - APE 9511Z<br>
                SAS au capital 38112.25 € 
            </td>


                <td style="text-align: right; ">
                BPMED NICE ENTREPRISE<br>
                <strong>IBAN : </strong>FR76 1460 7003 6569 0218 9841 804<br>
                <strong>BIC : </strong>CCBPFRPPMAR
                </td>
            </tr>
            
            <tr>

                <td  style=" font-size: 100%; width: 100%; text-align: center; " colspan=2><br><br>
                <strong>RECODE by eurocomputer - 112 allée François Coli - 06210 Mandelieu - +33 4 93 47 25 00 - contact@recode.fr<br>
                Ateliers en France - 25 ans d'expertise - Matériels neufs & reconditionnés </strong>
            </td>
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
                echo Pdfunctions::magicLinePRO($arrayOfDevisLigne , $temp);     
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
 $name = $temp->devis__id;
 $content = ob_get_contents();
 try 
 {
     $doc = new Html2Pdf('P','A4','fr');
     $doc->setDefaultFont('gothic');
     $doc->pdf->SetDisplayMode('fullpage');
     $doc->writeHTML($content);
     ob_clean();
     $numFact = '0000000' . $temp->cmd__id_facture ;
     $numFact = substr($numFact , -7 );

     if ($_SERVER['HTTP_HOST'] != "localhost:8080") 
     {
        $doc->output('O:\intranet\Auto_Print\PF/'.$temp->devis__id.'PF-'.$temp->devis__id.'C.pdf' , 'F'); 
     }
    
     $doc->output('O:\intranet\Auto_Print\PF/'.$temp->devis__id.'PF-'.$temp->devis__id.'C.pdf' , 'F'); 
    header('location: ficheTravail');
    
 } catch (Html2PdfException $e) 
 {
   die($e); 
 }
 
}