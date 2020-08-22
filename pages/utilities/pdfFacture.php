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
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;">
             <h3>facture : XXXXXXXX</h3><?php echo 'code client :' . $temp->client__id;  ?><br>
             <?php echo 'TVA intracom : '. floatval($temp->cmd__tva) . ' %'; ?><br><br>
             votre cde n° :<small> <?php echo $temp->cmd__code_cmd_client ; ?> </small><br>
             date commande :  <?php echo $formated_date ; ?><br><br>
             notre B.L n° : <?php echo $temp->devis__id ; ?><br>
             Mandelieu le XX/XX/XXXX
            </td>
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
<table CELLSPACING=0 style=" width: 100%;  margin-bottom: 5px; margin-top: 35 px;">
    <tr><td style="text-align: left;  width: 50%; padding-top: 7px; padding-bottom: 7px; padding-left:6px;">Condition et payement à réception/Condition générale de Vente XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX<br>
    XXXXXXXXXXXXX X XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX  X     X XXXXXX XXXXXX  XXXXXXX  XXXXXXXXXXXXXXXXXXXXX X XXXXXXXXXXXXXXX</td></tr>
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

<div style=" margin-top: 70px;">
<table style=" margin-top: 45px;  width:100%;" >
    <tr>
    <td style="width: 60%;">  </td>
    <td align="right">

     <table class="">
            <thead>
                    <tr>
                    <th scope="col">Prix € HT</th>
                    <th scope="col">Taux TVA</th>
                    <th scope="col">Montant TVA</th>
                    <th scope="col">Total TTC</th>
                    </tr>
            </thead>
            <tbody> 
                <tr>  
            <?php

                $totaux = Pdfunctions::totalFacture($temp, $arrayOfDevisLigne);

                echo "<td  style='text-align: left; margin-left= 4px;'><b>".number_format($totaux[0] , 2)." €</b></td>";
                echo "<td style='text-align: left;'>".number_format($totaux[1] , 2)." %</td>";
                echo "<td style='text-align: left;'>".number_format($totaux[2] , 2)." €</td>";
                echo "<td style='text-align: left;'><b>".number_format($totaux[3] , 2)." €</b></td>";
               
            
            ?>
            </tr>
            </tbody>
    </table>
    
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
