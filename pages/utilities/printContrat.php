<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;


session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new \App\Tables\Cmd($Database);
$Client = new \App\Tables\Client($Database);
$User = new App\Tables\User($Database);
$Global = new App\Tables\General($Database);
$Contact = new App\Tables\Contact($Database);
$Abonnement = new App\Tables\Abonnement($Database);

if (empty($_SESSION['user'])) 
{
    header('location: login');
}
if (!empty($_POST['printContrat'])) 
{
    $print_request = $_POST['printContrat'];
}
else
{
    header('location: abonnementAdmin');
}


$abn = $Abonnement->getById($print_request);

$abnLignes = $Abonnement->getLigneActives($print_request);
  
$temp =   $Cmd->GetById($print_request);

$clientView = $Client->getOne($temp->client__id);
$societeLivraison = false ;

if ($temp->devis__id_client_livraison) 
    {
        $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
    }


//date du jour:
$formate = date("d/m/Y"); 
$date_time = new DateTime( $temp->cmd__date_cmd);
$formated_date = $date_time->format('d/m/Y'); 
$Keyword = new \App\Tables\Keyword($Database);
$garanties = $Keyword->getGaranties();
ob_start();

?>
 
<style type="text/css">
    .page_header{margin-left: 30px;margin-top: 30px;}
    table{ font-size:13; font-style: normal; font-variant: normal;  border-collapse:separate; }
    strong{color:#000;}
    h3{color:#666666;}
    h2{color:#3b3b3b;}  
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
             // si une societe de livraion est présente 
                if ($temp->devis__contact__id) 
                {
                $contact = $Contact->getOne($temp->devis__contact__id);
                echo "<div style='background-color: #dedede;  padding: 15px 15px 15px 15px; border: 1px solid black;  width: 280px; '><strong>";
                echo Pdfunctions::showSocieteFacture($clientView,$contact) ." </strong></div>";
                }
                else
                {
                echo "<div style='background-color: #dedede; padding: 15px 15px 15px 15px; border: 1px solid black;  width: 280px;'><strong>";
                echo Pdfunctions::showSociete($clientView)  ." </strong></div>";
                }

             ?>
            </td>

             <td style="text-align: left; width:50%">
             <h3><?php echo $abn->prestaionAbn . " " .  $temp->devis__id   ; ?></h3>
             <br>
             
            
             <br>  
            
            </td>
         </tr>
     </table>
</page_header>
<page_footer>
<div>
<table style=" margin-bottom: 30px; width:100%;">
    <tr>
    <td style="width: 55%;">  </td>
    <td align="right">

     <table style="border: 1px solid black; background-color: #dedede; padding: 10px 10px 10px 10px; font-size: 120%;">
            <tbody> 
                <tr>  
            <?php
                $totaux = Pdfunctions::totalContract($abnLignes);

                echo "  <td style='text-align: left; width: 200px;'>
                            Total général mensuel : 
                        </td>
                        <td style='text-align: right; '>
                        <b>".number_format($totaux[0] , 2,',', ' ')." €</b>
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
    <td style="text-align: center;  width: 50%; padding-top: 7px; padding-bottom: 5px; padding-left:6px;">
    <b> RECODE : </b>
     <br>
    Date et signature : 
    <br>
    <br>
    <br>
    <br>
    <br>
    <hr>
    </td>
    <td style="text-align: center;  width: 50%; padding-top: 7px; padding-bottom: 5px; padding-left:6px;">
    <b>CLIENT : </b>
    <br>
    Cachet de l'entreprise avec mention "Lu et approuvé"
    <br>
    <br>
    <br>
    <br>
    <br>
    <hr>
    </td>
    </tr> 
</table>
        <table  class="page_footer" style="text-align: center; margin: auto; ">
        <tr>
            <td style="text-align: left; ">
                TVA: FR33 397 934 068<br>
                Siret 397 934 068 00016 - APE 9511Z<br>
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
    <div style="margin-top: 5px;">
            <table CELLSPACING=0 style="margin-top: 10px; width:100%">        
                    <?php         
                        echo Pdfunctions::magicLineContrat($abnLignes ,$abn->ab__mois_engagement );     
                    ?>
            </table>
    </div>
    <div  style="margin-top: 10px;">

    <?php
        if ($abn->ab__note) 
        {
          echo $abn->ab__note;
        }
    ?>

    </div>
</page>


 <?php
 $content = ob_get_contents();

 try
 {
     $doc = new Html2Pdf('P','A4','fr');
     $doc->setDefaultFont('gothic');
     $doc->pdf->SetDisplayMode('fullpage');
     $doc->writeHTML($content);
     ob_clean();

    
    $doc->output('O:\intranet\Auto_Print\CT\CT'.$temp->devis__id.'.pdf', 'F');
    // $doc->output(''.$temp->devis__id.'.pdf');
    //declarer la session pour s'en servir à l'impression:
    $_SESSION['abn_admin'] = $temp->devis__id;
    header('location: abonnementAdmin');

 } 
 catch (Html2PdfException $e) 
 {
   die($e); 
 }


 