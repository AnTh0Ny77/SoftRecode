<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Command = new \App\Tables\Cmd($Database);
$Client = new \App\Tables\Client($Database);
$User = new App\Tables\User($Database);
$Global = new App\Tables\General($Database);
 

if (empty($_SESSION['user'])) 
{
    header('location: login');
 }


  
//date du jour:
$date = date("Y-m-d H:i:s");

if(!empty($_POST['poids']) && !empty($_POST['transporteur']))
{
   $paquet = null ;
   if (intval($_POST['paquets']) > 1 ) 
   {
      $paquet = intval($_POST['paquets']);
   }
   $Command->updateTransport($_POST['transporteur'] , floatval($_POST['poids']), $paquet, $_POST['id_trans'] , 'IMP' , $date);

    $arrayTrans = $Command->devisLigne($_POST['id_trans']);

    foreach ($arrayTrans as  $ligne) 
    {
       
       $Command->updateLigne($ligne->devl_quantite, 'cmdl__qte_livr', $ligne->devl__id );
    }
   $command = $Command->GetById($_POST['id_trans']);
   $date_time = new DateTime( $command->cmd__date_envoi);
    //formate la date pour l'utilisateur:
    $formated_date = $date_time->format('d/m/Y');
    $command->cmd__date_envoi = $formated_date;
   $export = $Global->exportTNT($command , $_POST['poids'],$_POST['paquets']);
   $file = fopen("tnt.txt", "w");
    fwrite($file , $export);
    fclose($file);
  



$command = $Command->getById(intval($_POST['id_trans']));
$commandLignes = $Command->devisLigne($_POST['id_trans']);
$dateTemp = new DateTime($command->cmd__date_envoi);
 //cree une variable pour la date de commande du devis
 
ob_start();
?>
<style type="text/css">
      strong{ color:#000;}
      h3{ color:#666666;}
      h2{ color:#3b3b3b;}
      table{
        font-size:13; font-style: normal; font-variant: normal; 
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
             <br><strong>REF CLIENT :<?php echo $command->devis__id_client_livraison ?></strong></td>
             </tr>
             <tr>
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h4>Bon de Livraison -  <?php echo $command->devis__id ?></h4>
             <barcode dimension="1D" type="C128" label="none" value="<?php echo $command->devis__id ?>" style="width:40mm; height:8mm; color: #3b3b3b; font-size: 4mm"></barcode><br>

             <small>Envoyé  le : <?php echo $formated_date ?></small><br>
             Vendeur :<?php echo  $_SESSION['user']->log_nec ?> </td>
             <td style="text-align: left; width:50%"><strong>
             <?php echo $command->client__livraison_societe ?><br><?php echo $command->client__livraison__adr1 ?><br><?php if (!empty($command->client__livraison__adr2)) {
                 echo $command->client__livraison__adr2; } ?>
             <br>
             <?php echo $command->client__livraison_cp ." ". $command->client__livraison_ville ?></strong><br>
             <?php echo $command->contact__nom . " ";?> 
            </td>
         </tr>
     </table>


     <table CELLSPACING=0 style="width: 700px;  margin-top: 80px; ">
             <tr style=" margin-top : 50px; background-color: #dedede;">
                <td style="width: 22%; text-align: left;">Presta<br>Type</td>
                <td style="width: 57%; text-align: left">Ref Tech<br>Désignation Client</td>
                <td style="text-align: right; width: 12%"><br>Quantité</td>
             </tr> 
             <?php
             foreach ($commandLignes as $item) {
                if($item->cmdl__garantie_option > $item->devl__mois_garantie) 
                {
                  $temp = $item->cmdl__garantie_option ;
                } else {  $temp = $item->devl__mois_garantie;}

               

                echo "<tr style='font-size: 85%;>
                        <td style='border-bottom: 1px #ccc solid'> ". $item->prestaLib." <br> " .$item->kw__lib ."</td>
                        <td style='border-bottom: 1px #ccc solid; width: 55%;'> "
                            .$item->famille__lib. " " . $item->marque . " " .$item->modele. " ". $item->devl__modele . 
                            "<br> <small>désignation sur le devis:</small> " . $item->devl__designation ."</td>
                         <td style='border-bottom: 1px #ccc solid; text-align: right'><strong> "  . $item->cmdl__qte_livr. " </strong></td>
                      </tr>";
             }
             ?>
     </table> 
     <div style=" width: 100%; position: absolute; bottom:5%">
    
   
    <table CELLSPACING=0 style=" width: 100%; margin-top: 5px; margin-bottom: 15px;">
      
    </table>

    <table style=" margin-top: 10px; color: #8c8c8c; width: 100%;">
        <tr >
            <td  style="text-align: center; font-size: 80%; width: 100%;"><br><small>New Eurocomputer-TVA FR33b 397 934 068 Siret 397 934 068 00016 - APE9511Z - SAS au capital 38112.25 €<br>
            <strong>RECODE by eurocomputeur - 112 allée François Coli -06210 Mandelieu</strong></small></td>
        </tr>
    </table>  
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
    $doc->output('C:\laragon\www\BonLivraison\BL_'.$command->devis__id.'.pdf' , 'F'); 
    header('location: transport');
} 
catch (Html2PdfException $e) 
{
  die($e); 
}
    
}

 