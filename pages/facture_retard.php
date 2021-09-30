<?php
//mailer : 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './vendor/phpmailer/phpmailer/src/Exception.php';
require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/phpmailer/src/SMTP.php';

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
//declaration de objets nécéssaires : 
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Contact = new \App\Tables\Contact($Database);
$Client = new \App\Tables\Client($Database);
$General = new App\Tables\General($Database);
//requete qui recupère les factures en retard: 
$sql = $this->Db->Pdo->query("SELECT cmd__id  
FROM `cmd` 
LEFT JOIN client as c ON cmd__client__id_fact = c.client__id 
LEFT JOIN contact as d ON d.contact__fonction = \'FAC\' 
LEFT JOIN liaison_client_contact as l ON ( c.client__id = l.liaison__client__id AND d.contact__id = l.liaison__contact__id ) 
WHERE l.liaison__contact__id = ( SELECT contact__id FROM contact WHERE l.liaison__contact__id = contact__id AND contact__fonction = \'FAC\' ) 
AND ( cmd__date_fact > 000 )");
//execution:
$data = $request->fetchAll(PDO::FETCH_OBJ);

foreach ($data as $cmd) {

    $temp = $Cmd->GetById($cmd->cmd__id);

    $clientView = $Client->getOne($temp->client__id);
    $societeLivraison = false;

    if ($temp->devis__id_client_livraison)
        $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);

    $arrayOfDevisLigne = $Cmd->devisLigne_actif($cmd->cmd__id);
    foreach ($arrayOfDevisLigne as $ligne) {
        $xtendArray = $Cmd->xtenGarantie($ligne->devl__id);
        $ligne->ordre2 = $xtendArray;
    }

    $dateFact = new DateTime($temp->cmd__date_fact);
    $formate = $dateFact->format('d/m/Y');
    $date_time = new DateTime($temp->cmd__date_cmd);
    $formated_date = $date_time->format('d/m/Y');
    $Keyword = new \App\Tables\Keyword($Database);
    $garanties = $Keyword->getGaranties();
    $facturation_auto = $Contact->get_facturation_auto($temp->client__id);

    //Debut de l'enregistrement: 
    ob_start();
?>

    <style type="text/css">
        .page_header {
            margin-left: 30px;
            margin-top: 30px;
        }

        table {
            font-size: 13;
            font-style: normal;
            font-variant: normal;
            border-collapse: separate;
        }

        strong {
            color: #000;
        }

        h3 {
            color: #666666;
        }

        h2 {
            color: #3b3b3b;
        }
    </style>


    <page backtop="80mm" backleft="10mm" backright="10mm" backbottom="30mm">
        <page_header>
            <table class="page_header" style="width: 100%;">
                <tr>
                    <td style="text-align: left;  width: 50%"><img style=" width:65mm" src="public/img/recodeDevis.png" /></td>
                    <td style="text-align: left; width:50%">
                        <h3>REPARATION-LOCATION-VENTE</h3>imprimantes-lecteurs codes-barres<br><a style="color: green;">www.recode.fr</a><br><br>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;  width: 50% ; margin-left: 25%; padding-top: 20px;">



                        <?php
                        if (!empty($clientView->client__tva_intracom)) {
                            echo 'TVA intracom : ' . $clientView->client__tva_intracom . ' ';
                        } else  echo 'TVA intracom : Non renseigné';

                        ?><br>
                        Votre cde n° :<?php echo $temp->cmd__code_cmd_client; ?> <br>
                        Date commande : <?php echo $formated_date; ?><br>
                        Notre B.L n° : <?php echo $temp->devis__id; ?>
                        <br><br>
                        <small>Livraison:<br>
                            <b><?php echo Pdfunctions::showSociete($societeLivraison) ?></b></small>
                    </td>
                    <td style="text-align: left; width:50%">
                        <h3>Facture : <?php echo $temp->cmd__id_facture . ' le ' . $formate; ?></h3><br>



                        <?php
                        // si une societe de livraion est présente 
                        if ($temp->devis__contact__id) {
                            $contact = $Contact->getOne($temp->devis__contact__id);
                            echo "<div style='background-color: #dedede;  padding: 15px 15px 15px 15px; border: 1px solid black;  width: 280px; '><strong>";
                            echo Pdfunctions::showSocieteFacture($clientView, $contact) . " </strong></div></td>";
                        } else {
                            echo "<div style='background-color: #dedede; padding: 15px 15px 15px 15px; border: 1px solid black;  width: 280px;'><strong>";
                            echo Pdfunctions::showSociete($clientView)  . " </strong></div></td>";
                        }

                        ?>

                        <br>

                </tr>
            </table>
        </page_header>
        <page_footer>
            <div>
                <table style=" margin-bottom: 30px;  width:100%;">
                    <tr>
                        <td style="width: 55%;"> </td>
                        <td align="right">

                            <table style="border: 1px solid black; background-color: #dedede; padding: 10px 10px 10px 10px; font-size: 120%;">
                                <tbody>
                                    <tr>
                                        <?php

                                        $totaux = Pdfunctions::totalFacturePDF($temp, $arrayOfDevisLigne);


                                        echo "<td style='text-align: left; width: 200px;'>
                                    Total Hors Taxes :<br>
                                    Total Tva " . number_format($totaux[1], 2, ',', ' ') . " %:<br>
                                    Total Toutes Taxes:<br>
                                </td>
                                <td style='text-align: right; '>
                                <b>" . number_format($totaux[0], 2, ',', ' ') . " €</b><br>
                                <b>" . number_format($totaux[2], 2, ',', ' ') . " €</b><br>
                                <b>" . number_format($totaux[3], 2, ',', ' ') . " €</b><br>
                                </td>";
                                        ?>
                                    </tr>
                                </tbody>
                            </table>

                        </td>
                    </tr>
                </table>
            </div>
            <table style="width:100%;  margin-bottom: 5px; margin-top: 35 px;">
                <tr>
                    <td style="text-align: left;  width: 100%; padding-top: 7px; padding-bottom: 7px; padding-left:6px;">Conditions de paiement à réception/Conditions générales de Vente.
                        Des pénalités de retard au taux légal seront appliquées en cas de paiement après la date d'échéance. Conformément à la loi du 12.05.80, EUROCOMPUTER conserve la propriété du matériel jusqu'au paiement intégral du prix et des frais annexes.
                        <hr>
                    </td>
                </tr>

            </table>
            <table class="page_footer" style="text-align: center; margin: auto; ">
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

                    <td style=" font-size: 100%; width: 100%; text-align: center; " colspan=2><br><br>
                        <strong>RECODE by eurocomputer - 112 allée François Coli - 06210 Mandelieu - +33 4 93 47 25 00 - contact@recode.fr<br>
                            Ateliers en France - 25 ans d'expertise - Matériels neufs & reconditionnés </strong>
                    </td>
                </tr>
            </table>
        </page_footer>

        <div style="margin-top: 35px;">
            <table CELLSPACING=0 style="margin-top: 35px; width:100%">

                <?php
                $arrayPrice = [];
                foreach ($arrayOfDevisLigne as $value => $obj) {
                    array_push($arrayPrice, floatval(floatval($obj->devl_puht) * intval($obj->devl_quantite)));
                };
                echo Pdfunctions::magicLineFTC($arrayOfDevisLigne, $temp);
                ?>
            </table>
        </div>



        <div>
            <?php
            if ($temp->devis__note_client) {
                echo $temp->devis__note_client;
            }

            if (!empty($facturation_auto)) {
                echo '<br>Facture envoyée par mail à : ' . $facturation_auto->contact__email . ' le ' . $formate;
            }

            ?>
        </div>


    </page>
<?php
    $content = ob_get_contents();

    if ($temp->cmd__nom_devis) {
        $name  = $temp->cmd__nom_devis;
    } else {
        $name = $temp->devis__id;
    }

    try {
        $doc = new Html2Pdf('P', 'A4', 'fr');
        $doc->setDefaultFont('gothic');
        $doc->pdf->SetDisplayMode('fullpage');
        $doc->writeHTML($content);
        ob_clean();
        $numFact = '0000000' . $temp->cmd__id_facture;
        $numFact = substr($numFact, -7);
        if (!empty($facturation_auto)) {
            $config_json = file_get_contents("vendor/config/security.json");
            $config_json = json_decode($config_json);




            //Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $doc->output(__DIR__ . '/facture_mail/' . $numFact . '.pdf', 'F');
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host       = 'mail01.one2net.net';
                $mail->SMTPAuth   = true;
                $mail->Username   = $config_json->mail_adress->compta;
                $mail->Password   = $config_json->mail_pass->compta;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                //Recipients
                $mail->setFrom('compta@recode.fr', 'Facture');
                $mail->addAddress($facturation_auto->contact__email, '');
                $mail->addBCC('crm@recode.fr');
                //Attachments
                $mail->addAttachment(__DIR__ . '/facture_mail/' . $numFact . '.pdf');
                //Content
                $mail->isHTML(true);
                $mail->Subject = 'Votre facture RECODE N:' . $numFact . '';
                $mail->Body    = 'Vous trouverez ci-joint votre facture N:' . $numFact . ' de votre commande N: ' . $temp->devis__id . '';
                $mail->send();
                $deleted = unlink(__DIR__ . '/facture_mail/' . $numFact . '.pdf');
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                die();
            }
        } 
    } catch (Html2PdfException $e) {
        die($e);
    }
}
echo 'facture renvoyées';
die();