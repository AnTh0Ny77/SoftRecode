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
 

if (empty($_SESSION['user'])) {
    header('location: login');
 }


 // si une validation de devis a été effectuée : 
if(!empty($_POST['devisCommande']))
{
  $date = date("Y-m-d H:i:s");
  $Command->updateStatus('CMD',$_POST['devisCommande']);
  $Command->updateDate('cmd__date_cmd' , $date , $_POST['devisCommande'] );
  $Command->updateAuthor('cmd__user__id_cmd' , $_SESSION['user']->id_utilisateur , $_POST['devisCommande']);
  if (!empty($_POST['arrayLigneDeCommande'])) 
  {
    $validLignes = json_decode($_POST['arrayLigneDeCommande']);
    foreach ($validLignes as $lignes) 
    {
      $Command->updateGarantie(
        $lignes->devl__prix_barre[0],
        $lignes->devl__prix_barre[1],
        $lignes->devl__note_interne,
        $lignes->devl_quantite,
        $lignes->cmdl__cmd__id,
        $lignes->devl__ordre );
    } 
  }
  if (!empty($_POST['code_cmd'])) 
  {
    $Global->updateAll('cmd', $_POST['code_cmd'],'cmd__code_cmd_client', 'cmd__id', $_POST['devisCommande']);
  }
  //contient l'id du devis pour l'imprssion de la fiche de travail : client2.js
  $print_request = $_POST['devisCommande'];
}
  

$command = $Command->getById(intval($_POST['devisCommande']));
$commandLignes = $Command->devisLigne($_POST['devisCommande']);
$dateTemp = new DateTime($command->cmd__date_cmd);
 //cree une variable pour la date de commande du devis
 $date_time = new DateTime( $command->cmd__date_cmd);
 //formate la date pour l'utilisateur:
 $formated_date = $date_time->format('d/m/Y');
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
             <br><strong>REF CLIENT :<?php echo $command->client__id ?></strong></td>
             </tr>
             <tr>
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h4>Fiche De travail -  <?php echo $command->devis__id ?></h4>
             <barcode dimension="1D" type="C128" label="none" value="<?php echo $command->devis__id ?>" style="width:40mm; height:8mm; color: #3b3b3b; font-size: 4mm"></barcode><br>

             <small>Commandé le : <?php echo $formated_date ?></small><br>
             Vendeur :<?php echo  $_SESSION['user']->log_nec ?> </td>
             <td style="text-align: left; width:50%"><strong>
             <?php echo $command->client__societe ?><br><?php echo $command->client__adr1 ?><br><?php if (!empty($command->client__adr2)) {
                 echo $command->client__adr2; } ?>
             <br>
             <?php echo $command->client__cp ." ". $command->client__ville ?></strong><br>
             <?php echo $command->contact__nom . " " . $command->contact__prenom   ?> 
             <strong>
             <?php
             if (!empty($command->cmd__code_cmd_client)) 
             {
              echo $command->cmd__code_cmd_client;
             } 
             ?>
             </strong>
            </td>
         </tr>
     </table>


     <table CELLSPACING=0 style="width: 700px;  margin-top: 80px; ">
             <tr style=" margin-top : 50px; background-color: #dedede;">
                <td style="width: 22%; text-align: left;">Presta<br>Type<br>Gar.</td>
                <td style="width: 57%; text-align: left">Ref Tech<br>Désignation Client<br>Complement techniques</td>
                <td style="text-align: right; width: 12%"><strong>CMD</strong><br>Livr</td>
             </tr> 
             <?php
             foreach ($commandLignes as $item) {
                if($item->cmdl__garantie_option > $item->devl__mois_garantie) 
                {
                  $temp = $item->cmdl__garantie_option ;
                } else {  $temp = $item->devl__mois_garantie;}

               

                echo "<tr style='font-size: 85%;>
                        <td style='border-bottom: 1px #ccc solid'> ". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois</td>
                        <td style='border-bottom: 1px #ccc solid; width: 55%;'> "
                            .$item->famille__lib. " " . $item->marque . " " .$item->modele. " ". $item->devl__modele . 
                            "<br> <small>désignation sur le devis:</small> " . $item->devl__designation ." <br>" .$item->devl__note_interne .
                        "</td>
                         <td style='border-bottom: 1px #ccc solid; text-align: right'><strong> "  . $item->devl_quantite. " </strong></td>
                      </tr>";
             }
             ?>
     </table> 
     <table style=" margin-top: 200px; width: 100%">
             <tr style=" margin-top: 200px; width: 100%"><td><small>Commentaire:</small></td></tr>
             <tr >
             <td style='border-bottom: 1px black solid; border-top: 1px black solid; width: 100%' > <?php echo  $command->devis__note_interne ?> </td>
            </tr>
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
    $doc->output('C:\laragon\www\fichesTravail\Ft_'.$command->devis__id.'.pdf' , 'F'); 
    header('location: mesDevis');
} 
catch (Html2PdfException $e) 
{
  die($e); 
}
    


 