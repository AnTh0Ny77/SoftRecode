<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();
$Database = new App\Database('devisrecode');
$Database->DbConnect();
$Command = new \App\Tables\Command($Database);
$Client = new \App\Tables\Client($Database);

if (empty($_SESSION['user'])) {
    header('location: login');
 }

if(!empty($_POST['travailFiche'])) {
$command = $Command->getById(intval($_POST['travailFiche']));
$commandLignes = $Command->commandLigne($_POST['travailFiche']);

$date_time = new DateTime( $command->cmd__date_crea);
$formated_date = $date_time->format('d/m/Y'); 
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

<page backtop="10mm" backleft="15mm" backright="15mm">
     <table style="width: 100%;">
         <tr>
             <td style="text-align: left;  width: 50%"><img  style=" width:60mm" src="public/img/recodeDevis.png"/></td>
             <td style="text-align: left; width:50%"><h3>Reparation-Location-Vente</h3>imprimantes- lecteurs codes-barres<br>
             <a>www.recode.fr</a><br><br>
             <br><strong>REF CLIENT :<?php echo $command->cmd__client__id ?></strong></td>
             </tr>
             <tr>
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h4>Fiche De travail -  <?php echo $command->cmd__id ?></h4>
             <barcode dimension="1D" type="C128" label="none" value="<?php echo $command->cmd__id ?>" style="width:40mm; height:8mm; color: #3b3b3b; font-size: 4mm"></barcode><br>

             <small>Edité le : <?php echo $formated_date ?></small></td>
             <td style="text-align: left; width:50%"><strong>
             <?php echo $command->client__societe ?><br><?php echo $command->client__adr1 ?><br><?php if (!empty($command->client__adr2)) {
                 echo $command->client__adr2; } ?>
             <br>
             <?php echo $command->client__cp ." ". $command->client__ville ?></strong><br>
             <?php echo $command->contact__nom . " " . $command->contact__prenom   ?> 
            </td>
         </tr>
     </table>


     <table CELLSPACING=0 style="width: 100%;  margin-top: 30px; ">
             <tr style=" margin-top : 50px; background-color: #dedede; " >
                <td style="width: 22%; text-align: left;">Presta<br>Type<br>Gar.</td>
                <td style="width: 57%; text-align: left">Ref Tech<br>Désignation Client<br>Complement techniques</td>
                <td style="text-align: right; width: 12%"><strong>CMD</strong></td>
             </tr> 
             <?php


             foreach ($commandLignes as $item) {
                if($item->cmdligne__mois_garantie > $item->cmdligne__mois_extension) {
                  $temp = $item->cmdligne__mois_garantie ;
                } else {  $temp = $item->cmdligne__mois_extension;}
                echo '<tr>
                      <td> '. $item->cmdligne__type.' <br>' .$item->cmdligne__type .' <br> ' . $temp .' </td>
                      <td> ' . $item->cmdligne__model . '<br>'. $item->cmdligne__designation .'<br>'.$item->cmdligne__note_interne .'</td>
                      <td><strong> ' . $item->cmdligne__quantite. '</strong> </td>
                     </tr>';
             }

             ?>
        
     </table> 
</page>


















<?php
$content = ob_get_contents();

try {
    $doc = new Html2Pdf('P','A4','fr');
    $doc->pdf->SetDisplayMode('fullpage');
    $doc->writeHTML($content);
    ob_clean();
    $doc->output('devisN.pdf');
    unset( $_SESSION['Contact']);
    unset( $_SESSION['Client']);
    unset( $_SESSION['livraison']);
   
} catch (Html2PdfException $e) {
  die($e); 
}
    
}

 