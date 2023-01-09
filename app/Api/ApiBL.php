<?php

namespace App\Api;

require_once  '././vendor/autoload.php';

use DateTime;
use App\Database;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
use App\Methods\Devis_functions;
use App\Tables\Cmd;
use App\Tables\Contact;
use App\Api\ResponseHandler;

class ApiBL
{

    public static  function index($method)
    {
        $responseHandler = new ResponseHandler;
        switch ($method) {
            case 'GET':
                return self::get();
                break;
            default:
                return $responseHandler->handleJsonResponse([
                    'msg' =>  'Aucune opération n est prévue avec cette méthode'
                ], 404, 'Unknow');
                break;
        }
    }

    public static function get()
    {
        $responseHandler = new ResponseHandler;
        //controle du client 
        if (empty($_GET['cmd__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' lID de la commande ne peut pas etre vide '
            ], 404, 'bad request');
        }

        $Database = new Database('devis');
        $Database->DbConnect();
        $Cmd = new Cmd($Database);
        $devis = $Cmd->GetById($_GET['cmd__id']);

        if (empty($devis)) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Aucune commande n a été trouvée !'
            ], 404, 'bad request');
        }

        switch ($devis->devis__etat) {
            case 'VLD':
            case 'IMP':
                return self::renderBL($_GET['cmd__id'], $Database);
                break;
            case 'ABN':
            case 'CMD':
            case 'NFT':
            case 'ABO':
            case 'ATN':
            case 'PBL':
            case 'PLL':
            case 'RFS':
            case 'VLA':
                return $responseHandler->handleJsonResponse([
                    'msg' =>  ' L etat ne permet aps  ledition du document '
                ], 404, 'bad request');
                break;
        }
    }

    public static function renderBL($id, $database){
        $Client = new \App\Tables\Client($database);
        $Contact = new \App\Tables\Contact($database);
        $Keyword = new \App\Tables\Keyword($database);
        $Contact = new Contact($database);
        $Cmd = new Cmd($database);
        $command = $Cmd->getById($id);
        $societe = $Client->getOne($command->devis__id_client_livraison);
        //si il s'agit d'une fiche de garantie ou d'un reliquat facturé à l'avance  elle ne passera pas par la facturation:
        if (intval($command->client__id) < 10)
            $Cmd->updateGarantieToArchive($command->devis__id);

        
        $date_time = new DateTime($command->cmd__date_envoi);
        //formate la date pour l'utilisateur:
        $formated_date = $date_time->format('d/m/Y');
        $command->cmd__date_envoi = $formated_date;
        $commandLignes = $Cmd->devisLigne($id);
        // $dateTemp = new DateTime($command->cmd__date_envoi);
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
             <td style="text-align: left; width:50%"><h3>Reparation - Location - Vente</h3>imprimantes - lecteurs codes-barres<br>
             <a>www.recode.fr</a><br>
              04 93 47 25 00<br>
             <br></td>
             </tr>
             <tr>
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h4>Bon de Livraison -  <?php echo $command->devis__id ?></h4>
             <barcode dimension="1D" type="C128" label="none" value="<?php echo $command->devis__id ?>" style="width:40mm; height:8mm; color: #3b3b3b; font-size: 4mm"></barcode><br>

             <small>Envoyé  le : <?php echo $formated_date ?></small><br>
             Vendeur :<?php echo  $command->nomDevis . " " .$command->prenomDevis  ?> <br>
             Code cmd :<?php echo $command->cmd__code_cmd_client ?>
            </td>
             <td style="text-align: left; width:50%"><strong><?php 
             if ($command->devis__contact_livraison) {
                            //si un contact est présent dans l'adresse de livraison : 
                            $contact2 = $Contact->getOne($command->devis__contact_livraison);
                            echo "<br><small>".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                            echo Pdfunctions::showSociete($societe) . "</strong>"; 
                        }
                        else {
                            // si pas de contact de livraison : 
                            echo "<br><small></small><strong><br>";
                            echo Pdfunctions::showSociete($societe) . "</strong>"; 
                        } ?></strong>
            </td>
         </tr>
     </table>
     <table CELLSPACING=0 style="width: 700px;  margin-top: 80px; ">
             <tr style=" margin-top : 50px; background-color: #dedede;">
                <td style="width: 22%; text-align: left;">Presta<br>Type</td>
                <td style="width: 57%; text-align: left">Désignation</td>
                <td style="text-align: right; width: 12%">Quantité</td>
             </tr> 
             <?php
                foreach ($commandLignes as $item){
                    if ($item->prestaLib != 'Port') {
                        if ($item->kw__lib == 'Non Concerné'){
                            $item->kw__lib = '';
                        }
                        echo "<tr style='font-size: 85%;>
                                <td style='border-bottom: 1px #ccc solid'> ". $item->prestaLib." <br> " .$item->kw__lib ."</td>
                                <td style='border-bottom: 1px #ccc solid; width: 55%;'> "
                                    . $item->devl__designation ."</td>
                                <td style='border-bottom: 1px #ccc solid; text-align: right'><strong> "  . $item->cmdl__qte_livr. " </strong></td>
                            </tr>";
                    }
                }
             ?>
     </table> 
     <div style=" width: 100%; position: absolute; bottom:5%">
    <table CELLSPACING=0 style=" width: 100%; margin-top: 5px; margin-bottom: 15px;">
    </table>
    <table style=" margin-top: 10px; color: #8c8c8c; width: 100%;">
        <tr >
            <td  style="text-align: center; font-size: 80%; width: 100%;"><br><small>New Eurocomputer-TVA FR33b 397 934 068 Siret 397 934 068 00016 - APE9511Z - SAS au capital 38112.25 €<br>
            <strong>RECODE by eurocomputer - 112 allée François Coli - 06210 Mandelieu</strong></small></td>
        </tr>
    </table>  
    </div>  
</page>
<?php
$content = ob_get_contents();
        try {
            $doc = new Html2Pdf('P', 'A4', 'fr');
            $doc->setDefaultFont('gothic');
            $doc->pdf->SetDisplayMode('fullpage');
            $doc->writeHTML($content);
            ob_clean();
            $doc->output('' . $command->devis__id . '.pdf');
        } catch (Html2PdfException $e) {
            die($e);
        }
    }
}
