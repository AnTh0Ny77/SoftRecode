<?php
//injection des objets et repertoire nécéssaires : 
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
$General = new App\Tables\General($Database);


if (empty($_SESSION['user'])) 
{
    header('location: login');
}

if (!empty($_POST['idDevis'])) 
{
    //mise à jour du status :
    $General->updateAll('cmd' , 'ATN' , 'cmd__etat' , 'cmd__id' , $_POST['idDevis']);

    //mise a jour pour mode classique ou remise : 
    if (!empty($_POST['checkremise'])) 
    {
        $General->updateAll('cmd' , 1 , 'cmd__mode_remise' , 'cmd__id' , $_POST['idDevis']);
    }
    else
    {
        $General->updateAll('cmd' , 0 , 'cmd__mode_remise' , 'cmd__id' , $_POST['idDevis']);
    }
    //mise a jour pour le report des extensions
    if (!empty($_POST['checkExtend'])) 
    {
        $General->updateAll('cmd' , 1 , 'cmd__report_xtend' , 'cmd__id' , $_POST['idDevis']);
    }
    else
    {
        $General->updateAll('cmd' , 0 , 'cmd__report_xtend' , 'cmd__id' , $_POST['idDevis']);
    }

    $devis = $Cmd->GetById($_POST['idDevis']);
    $devis_ligne = $Cmd->devisLigne($_POST['idDevis']);
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

    //enregistrement du contenu html: 
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

    


    </page>
 <?php
 $content = ob_get_contents();

 if ($devis->cmd__nom_devis) 
 {
    $name  = $devis->cmd__nom_devis;
 }
 else 
 {
    $name = $devis->devis__id;
 }
 
 try 
 {
     $doc = new Html2Pdf('P','A4','fr');
     $doc->setDefaultFont('gothic');
     $doc->pdf->SetDisplayMode('fullpage');
     $doc->writeHTML($content);
     ob_clean();
     $doc->output(''.$devis->devis__id.'-'.$name.'.pdf');
     unset( $_SESSION['Contact']);
     unset( $_SESSION['Client']);
     unset( $_SESSION['livraison']);
    
 } 
 catch (Html2PdfException $e) 
 {
   die($e); 
 }
}
