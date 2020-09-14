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
$Contact = new \App\Tables\Contact($Database);
$User = new App\Tables\User($Database);
 

if (empty($_SESSION['user'])) {
    header('location: login');
 }
  
if(!empty($_POST['travailFiche'])) {
$command = $Command->getById(intval($_POST['travailFiche']));
$commandLignes = $Command->devisLigne($_POST['travailFiche']);
 //cree une variable pour la date de commande du devis
 $date_time = new DateTime( $command->cmd__date_cmd);
 //formate la date pour l'utilisateur:
 $formated_date = $date_time->format('d/m/Y');
 $societeLivraison = false ;
 if ($command->devis__id_client_livraison) 
 {
     $societeLivraison = $Client->getOne($command->devis__id_client_livraison);
 }
//recupere le client: 
$clientView = $Client->getOne($command->client__id);
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
            <td>
             <?php  // si une societe de livraion est présente 
             if ($societeLivraison) 
             {
                //si un contact est présent dans l'adresse de livraison :    
                if ($command->devis__contact_livraison) 
                {            
                    $contact2 = $Contact->getOne($command->devis__contact_livraison);
                        echo "<br> <small>Societe : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                        echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                }
                // si pas de contact de livraison :
                else 
                {          
                    echo "<br> <small>Societe :</small><strong><br>";
                    echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                }
             }

            //Si pas de societe de livraison presente:
            else 
            {
                //si un contact est present:
                if (!empty($command->devis__contact__id)) 
                {
                   
                    $contact = $Contact->getOne($command->devis__contact__id);
                    echo "<small>Societe : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom."</small><strong><br>";
                    echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }
                else
                {
                    echo "<small>Societe : </small><strong><br>";
                    echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }
            }     ?> 
          
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

try {
    $doc = new Html2Pdf('P','A4','fr');
    $doc->setDefaultFont('gothic');
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

 