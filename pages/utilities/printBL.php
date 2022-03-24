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
$Contact = new App\Tables\Contact($Database);
$Pisteur = new App\Tables\Pistage($Database);
//$Pisteur = new App\Tables\Pistage($Database);
if (empty($_SESSION['user'])) header('location: login');
//date du jour:
$date = date("Y-m-d H:i:s");
if(!empty($_POST['poids']) && !empty($_POST['transporteur']) && $_POST['transporteur']!= 'NONE'){
   $paquet = null ;
   if (intval($_POST['paquets']) > 1 ) {
      $paquet = intval($_POST['paquets']);
   }
   $Command->updateTransport($_POST['transporteur'] , floatval($_POST['poids']), $paquet, $_POST['id_trans'] , 'IMP' , $date);
   $Pisteur->addPiste($_SESSION['user']->id_utilisateur, $date , $_POST['id_trans'] , 'Saisie Transport effectuée' );
   $arrayTrans = $Command->devisLigne($_POST['id_trans']); 
//met a jour les différentes lignes en fonction de la quantité validé par le client : 
    foreach ($arrayTrans as  $ligne){
        if ($ligne->prestaLib == 'Port'){
            $Command->updateLigne($ligne->devl_quantite, 'cmdl__qte_livr', $ligne->devl__id );
            $Command->updateLigne($ligne->devl_quantite, 'cmdl__qte_fact', $ligne->devl__id );
        }
        foreach ($_POST['linesTransport'] as $key => $value) {
           if ($key == $ligne->devl__id) {
            $Command->updateLigne($value, 'cmdl__qte_livr', $ligne->devl__id );
            $Command->updateLigne($value, 'cmdl__qte_fact', $ligne->devl__id );
           }
        }  
    }
$command = $Command->GetById($_POST['id_trans']);
$date_time = new DateTime( $command->cmd__date_envoi);
//formate la date pour l'utilisateur:
$formated_date = $date_time->format('d/m/Y');
$command->cmd__date_envoi = $formated_date;
if ($command->cmd__trans == 'TNT') {
    $export = $Global->exportTNT($command , $_POST['poids'],$_POST['paquets']);
    if ($_SERVER['HTTP_HOST'] != "localhost:8080") $file = fopen("O:\intranet\Port\TNT\TNT.txt", "a");
    //ecrit 
    fwrite($file , $export);
    fclose($file);
}
$command = $Command->getById(intval($_POST['id_trans']));
$societe = $Client->getOne($command->devis__id_client_livraison);
//si il s'agit d'une fiche de garantie ou d'un reliquat facturé à l'avance  elle ne passera pas par la facturation:
if (intval($command->client__id) < 10) 
   $Command->updateGarantieToArchive($command->devis__id);

if ($command->cmd__trans == 'NBL') {
    header('location: transport2');  
    die();
}
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
        $doc = new Html2Pdf('P','A4','fr');
        $doc->setDefaultFont('gothic');
        $doc->pdf->SetDisplayMode('fullpage');
        $doc->writeHTML($content);
        ob_clean();
        if ($_SERVER['HTTP_HOST'] != "localhost:8080"){
            $doc->output('O:\intranet\Auto_Print\BL\BL_'.$command->devis__id.'.pdf' , 'F'); 
        }
    
        header('location: transport2');
    } 
    catch (Html2PdfException $e){
        die($e); 
    }
}else{
        $_SESSION['alertSaisie'] = 'PB';
        header('location: transport2');
}
 