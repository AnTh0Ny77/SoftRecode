<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
use App\Methods\Devis_functions;
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Client = new \App\Tables\Client($Database);
$Contact = new \App\Tables\Contact($Database);
$Keyword = new \App\Tables\Keyword($Database);
$garanties = $Keyword->getGaranties();

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) 
{
    echo 'no no no .... ';
}
 else 
 {

// requete table client:
 if (!empty($_POST['AjaxDevis']))
 {
     
        $devis = $Cmd->GetById($_POST['AjaxDevis']);
        $devis_ligne = $Cmd->devisLigne_actif($_POST['AjaxDevis']);
        foreach ($devis_ligne as $ligne) 
        {
            $tableau_extension = $Cmd->xtenGarantie($ligne->devl__id);
            $ligne->tableau_extension = $tableau_extension;
        } 
        //recuperation de la date du devis et formate : 
        $date_time = new DateTime($devis->devis__date_crea);
        $date_devis_formate = $date_time->format('d/m/Y');
        //recuperation de la societe facturée: 
        $societe_facture = $Client->getOne($devis->client__id);
        //recuperation de la societe de livraison si différente: 
        $societe_livraison = null;
        if ($devis->devis__id_client_livraison != $devis->client__id) 
        {
            $societe_livraison = $Client->getOne($devis->devis__id_client_livraison);
        }
    ob_start();

     ?>
    <style type="text/css">
        .page_header{margin-left: 30px; margin-top: 30px;}
        table{font-size:13; font-style: normal; font-variant: normal;  border-collapse:separate;}
        strong{ color:#000;}
        h3{color:#666666;}
        h2{color:#3b3b3b;}   
    </style>


    <page backtop="80mm" backleft="10mm" backright="10mm" backbottom= "30mm"> 
        <page_header>
            <table class="page_header" style="width: 100%;">
                <tr>
                    <td style="text-align: left;  width: 50%"><img style=" width:65mm" src="public/img/recodeDevis.png"/>
                    </td>
                    <td style="text-align: left; width:50%">
                        <h3>REPARATION-LOCATION-VENTE</h3>imprimantes-lecteurs codes-barres<br>
                        <a style="color: green;">www.recode.fr</a>
                        <br>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;  width: 50% ; margin-left: 25%;"><h2>Devis <?php echo $devis->devis__id ?></h2><br>
                        <?php echo $date_devis_formate ?><br>
                        <?php echo $devis->email ?>
                        <p><small>Notre offre est valable une semaine à dater du : <?php echo $date_devis_formate ?></small></p>
                    </td>
                    <td style="text-align: left; width:50%">
                    <?php 
                        // si une societe de livraion est présente 
                        if ($societe_livraison) 
                        {
                            if ($devis->devis__contact__id) 
                            {
                                // si un contact est présent dans l'adresse de facturation :
                                $contact = $Contact->getOne($devis->devis__contact__id);
                                echo "<small>facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom. "</small><strong><br>";
                                echo Pdfunctions::showSociete($societe_facture) ." </strong> ";
                                
                                if ($devis->devis__contact_livraison)
                                {
                                    //si un contact est présent dans l'adresse de livraison : 
                                    $contact2 = $Contact->getOne($devis->devis__contact_livraison);
                                    echo "<br> <small>livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                                    echo Pdfunctions::showSociete($societe_livraison) . "</strong>"; 
                                }
                                else 
                                {
                                    // si pas de contact de livraison : 
                                    echo "<br> <small>livraison :</small><strong><br>";
                                    echo Pdfunctions::showSociete($societe_livraison) . "</strong>"; 
                                } 
                            }
                            else 
                            {
                                echo "<small>facturation :</small><strong><br>";
                                echo Pdfunctions::showSociete($societe_facture)." </strong>";
                                if ($devis->devis__contact_livraison) 
                                {
                                    $contact2 = $Contact->getOne($devis->devis__contact_livraison);
                                    echo "<br> <small>livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                                    echo Pdfunctions::showSociete($societe_livraison) . "</strong>"; 
                                } 
                                else
                                {
                                    echo "<br> <small>livraison :</small><strong><br>";
                                    echo Pdfunctions::showSociete($societe_livraison) . "</strong>"; 
                                }  
                                }  
                        } 
                        else
                        {
                            if ($devis->devis__contact__id) 
                            {
                                $contact = $Contact->getOne($devis->devis__contact__id);
                                echo "<small>livraison & facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom."</small><strong><br>";
                                echo Pdfunctions::showSociete($societe_facture)  ."</strong>";
                            }
                            else
                            {
                                echo "<small>livraison & facturation : </small><strong><br>";
                                echo Pdfunctions::showSociete($societe_facture)  ."</strong>";
                            }
                        } 
                        ?>
                    </td>   
                </tr>
            </table>
        </page_header>
        <page_footer>
                <table class="page_footer" style="text-align: center; margin: auto; ">
                    <tr>
                        <td style=" font-size: 80%; width: 100%;">
                            <br><br>
                            <small>New Eurocomputer-TVA FR33 397 934 068 Siret 397 934 068 00016 - APE9511Z - SAS au capital 38112.25 €<br>
                            <strong>RECODE by Eurocomputer - 112 allée François Coli -06210 Mandelieu</strong></small>
                        </td>
                    </tr>
                </table>
        </page_footer>

        <table CELLSPACING=0 style="margin-top: 15px; width:100%">
        
        <?php 

            if ($devis->cmd__mode_remise > 0) 
            {
                echo Devis_functions::remise_devis_ligne_pdf($devis_ligne, 0);
            }
            else
            {
                echo Devis_functions::classic_devis_ligne_pdf_image($devis_ligne, 0);
            }
           
            
        ?>
        </table>
        <div>
            <table style=" margin-top: 25px;  width:100%;" >
                <tr>
                    <td style="width: 45%;"></td>
                    <td align="right">
                        <?php

                         $tableau_prix =[];
                         foreach ($devis_ligne as $value => $key) 
                         {
                            array_push($tableau_prix, floatval(floatval($key->devl_puht)*intval($key->devl_quantite)));
                         }
                         
                        if ($devis->cmd__modele_devis != 'STX') 
                        {
                            switch ($devis->cmd__modele_devis) 
                            {
                                case 'STT':
                                    $totalPrice = array_sum($devis_ligne);
                                    $totaux = Devis_functions::classic_total_devis($devis_ligne , $garanties , array_sum($tableau_prix) , true , $devis->tva_Taux);
                                    break;

                                case 'TVT':
                                    $totalPrice = array_sum($devis_ligne);
                                    $totaux = Devis_functions::classic_total_devis($devis_ligne , $garanties , array_sum($tableau_prix) , false , $devis->tva_Taux);
                                    break;
                                
                                default:
                                    $totalPrice = array_sum($devis_ligne);
                                    $totaux = Devis_functions::classic_total_devis($devis_ligne , $garanties , array_sum($tableau_prix) , true , $devis->tva_Taux);
                                    break;
                            }
                           
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <?php
            if($devis->devis__note_client) 
            {
                echo $devis->devis__note_client;
            }
            ?>
        </div>

        <div style="margin-top: 55 px;">
            <table CELLSPACING=0 style=" width: 100%; margin-top: 55 px;">
                <tr style="background-color: #dedede;  "><td style="text-align: left;  width: 50%; padding-top: 7px; padding-bottom: 7px; padding-left:6px;"><strong>BON POUR COMMANDE</strong><BR>NOM DU SIGNATAIRE: <br>VOTRE N° DE CDE :<br>DATE:</td><td style="text-align: right;  width: 50%; vertical-align:top; padding-top: 7px; padding-right: 6px;">CACHET & SIGNATURE</td></tr>
            </table>
            <table style="width: 100%;">
                    <table style="   margin-top: 5px; color: #8c8c8c; width: 100%;">
                        <tr><td style="font-size: 80%;"><small>Le client accepte la présente proposition ainsi que les conditions générales de vente Recode.<br>Recode conserve la propriété du matériel jusqu'au 
                        paiement intégral du prix et des frais annexes.</small></td></tr>
                        <tr>
                            <td><small><strong>Coordonnées bancaires(Banque Populaire Méditérranée)</strong><br>
                            IBAN : FR76 1460 7003 6569 0218 9841 804- BIC: CCBPFRPPMAR</small></td>
                        </tr>
                </table>  
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
     $doc->output(__DIR__ .'/visio/'.$_SESSION['user']->log_nec.'devis.pdf', 'F'); 
 } 
 catch (Html2PdfException $e) 
 {
   die($e); 
 }

 
 echo  json_encode($devis);

 }
 else {
    echo json_encode('{"erreur" : 503 }');
 }

}