<?php
require "./vendor/autoload.php";


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
session_start();

//declaration de objets nécéssaires : 
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Contact = new \App\Tables\Contact($Database);
$Client = new \App\Tables\Client($Database);
$General = new App\Tables\General($Database);
$Pistage = new App\Tables\Pistage($Database);

if(empty($_SESSION['user']))
{
    header('location: login');
}

//si une facture a été faite = variable pour l'alerte: 
if(!empty($_SESSION['factureEtoile'])) {
    $_POST['hiddenCommentaire'] = $_SESSION['factureEtoile'];
    $_SESSION['factureEtoile'] = "";
}

// si une commande à été postée: 
if (!empty($_POST['hiddenCommentaire'])){

    //gestion de la date de facturation
    if (!empty($_POST['date_fact']) ) {
        $date = date($_POST['date_fact']);
        setcookie("date_facture_cookies", $date, time()+3600);
        $date = new DateTime($_POST['date_fact']);
        $date = date($_POST['date_fact'] . " H:i:s");
        // $date = $date->format('Y-m-d H:i:s'); 
    }else {
    if(empty($_COOKIE['date_facture_cookies'])) 
    {
        $date = date("Y-m-d H:i:s");
        setcookie("date_facture_cookies", $date, time()+3600);
        $date = new DateTime($date);
        $date = $date->format('Y-m-d H:i:s'); 
    }
    else{
        
        $date = date($_COOKIE['date_facture_cookies']);
        setcookie("date_facture_cookies", $date, time()+3600);
        $date = date($_COOKIE['date_facture_cookies'] . " H:i:s");
    }
}

$commande_temporaire = $Cmd->GetById($_POST['hiddenCommentaire']);
$ligne_temporaire = $Cmd->devisLigne($_POST['hiddenCommentaire']);

//controle si la facture  n'est pas deja une facture :
if ($commande_temporaire->devis__etat == 'VLD'){
    $_SESSION['facture'] = $_POST['hiddenCommentaire'];
    header('location: facture');
    die();
}

//controle si le total n'est pas a zero : 
$totaux = Pdfunctions::totalFacturePDF($commande_temporaire, $ligne_temporaire);        
       
    if (empty(floatval($totaux[3]))){
       $_SESSION['facture'] = $_POST['hiddenCommentaire'];
       $_SESSION['facture_zero'] = 'TTZ';
       header('location: facture');
    }
    else {
            // 2 changer le status de la commande et attribuer un numero de facture:
            $Cmd->commande2facture($_POST['hiddenCommentaire']);
            $General->updateAll('cmd', $date , 'cmd__date_fact' , 'cmd__id', $_POST['hiddenCommentaire'] );
            $General->updateAll('cmd', $_SESSION['user']->id_utilisateur , 'cmd__user__id_fact' , 'cmd__id', $_POST['hiddenCommentaire'] );

            //  4 activer une alert pour indiquer le bon fonctionnement du logiciel 
            $relique = $Cmd->classicReliquat($_POST['hiddenCommentaire']);

            // alerte si un reliquat à été facturé : 
            $alertReliquat = $Cmd->alertReliquat($_POST['hiddenCommentaire']);

            // gère l'arlerte en fonction du reliquat : 
            if (!empty($alertReliquat)) 
            {
                $_SESSION['alertRelique'] = $relique;
            }

            // 5 reliquat si article deja facturé mais pas livré : 
            $Cmd->FactureReliquat($_POST['hiddenCommentaire']);

            // 3 enregistrer la facture au format pdf dans un folder 
            $temp = $Cmd->GetById($_POST['hiddenCommentaire']);

            //client livré et facturé : 
            $clientView = $Client->getOne($temp->client__id);
            $societeLivraison = false ;

            if($temp->devis__id_client_livraison) 
            {
                $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
            }

            //ligne de devis : 
            $arrayOfDevisLigne = $Cmd->devisLigne_actif($_POST['hiddenCommentaire']);
            foreach($arrayOfDevisLigne as $ligne) {
                $xtendArray = $Cmd->xtenGarantie($ligne->devl__id);
                $ligne->ordre2 = $xtendArray;
            } 

            //gestion des dates : 
            $dateFact = new DateTime( $temp->cmd__date_fact);
            $formate = $dateFact->format('d/m/Y'); 
            $date_time = new DateTime( $temp->cmd__date_cmd);
            $formated_date = $date_time->format('d/m/Y'); 
            $Keyword = new \App\Tables\Keyword($Database);
            $garanties = $Keyword->getGaranties();

            $facturation_auto = $Contact->get_facturation_auto($temp->client__id);

            //Debut de l'enregistrement: 
            ob_start();
            ?>

            <style type="text/css">
                .page_header
                {
                    margin-left: 30px;
                    margin-top: 30px;
                }
                
                table{   font-size:13; font-style: normal; font-variant: normal;  border-collapse:separate; }
                strong{ color:#000;}
                h3{ color:#666666;}
                h2{ color:#3b3b3b;}
                
            </style>

            <page backtop="80mm" backleft="10mm" backright="10mm" backbottom= "30mm"> 
            <page_header>
                <table class="page_header" style="width: 100%;">
                    <tr>
                        <td style="text-align: left;  width: 50%"><img  style=" width:65mm" src="public/img/recodeDevis.png"/>
                        <img style=" width:13mm; margin-top: 50px;" src="public/img/Ecovadis.png" />
                    </td>
                        <td style="text-align: left; width:50%"><h3>REPARATION-LOCATION-VENTE</h3>imprimantes-lecteurs codes-barres<br><a style="color: green;">www.recode.fr</a><br><br></td>
                    </tr>
                    <tr>
                        <td  style="text-align: left;  width: 50% ; margin-left: 25%; padding-top: 20px;">

                        
                        <?php 
                        if (!empty($clientView->client__tva_intracom)) 
                        {
                            echo 'TVA intracom : '. $clientView->client__tva_intracom . ' ';
                        } else  echo 'TVA intracom : Non renseigné';
                        
                        ?><br>
                        Votre cde n° :<?php echo $temp->cmd__code_cmd_client ; ?> <br>
                        Date commande :  <?php echo $formated_date ; ?><br>
                        Notre B.L n° : <?php echo $temp->devis__id ; ?>
                        <br><br>
                        <small>Livraison:<br>
                        <b><?php  echo Pdfunctions::showSociete($societeLivraison) ?></b></small>
                        </td>
                        <td style="text-align: left; width:50%">
                        <h3>Facture : <?php echo $temp->cmd__id_facture .' le '. $formate ; ?></h3><br>
                        
                        

                        <?php 
                        // si une societe de livraion est présente 
                            if ($temp->devis__contact__id) 
                            {
                            $contact = $Contact->getOne($temp->devis__contact__id);
                            echo "<div style='background-color: #dedede;  padding: 15px 15px 15px 15px; border: 1px solid black;  width: 280px; '><strong>";
                            echo Pdfunctions::showSocieteFacture($clientView,$contact) ." </strong></div></td>";
                            }
                            else
                            {
                            echo "<div style='background-color: #dedede; padding: 15px 15px 15px 15px; border: 1px solid black;  width: 280px;'><strong>";
                            echo Pdfunctions::showSociete($clientView)  ." </strong></div></td>";
                            }

                        ?>
                        
                        <br>
                        
                    </tr>
                </table>
            </page_header>
            <page_footer>
            <div >
            <table style=" margin-bottom: 30px;  width:100%;" >
                <tr>
                <td style="width: 55%;">  </td>
                <td align="right">

                <table style="border: 1px solid black; background-color: #dedede; padding: 10px 10px 10px 10px; font-size: 120%;">
                        <tbody> 
                            <tr>  
                        <?php

                            $totaux = Pdfunctions::totalFacturePDF($temp, $arrayOfDevisLigne);


                            echo "<td style='text-align: left; width: 200px;'>
                                    Total Hors Taxes :<br>
                                    Total Tva ".number_format($totaux[1] , 2,',', ' ')." %:<br>
                                    Total Toutes Taxes:<br>
                                </td>
                                <td style='text-align: right; '>
                                <b>".number_format($totaux[0] , 2,',', ' ')." €</b><br>
                                <b>".number_format($totaux[2] , 2,',', ' ')." €</b><br>
                                <b>".number_format($totaux[3] , 2,',', ' ')." €</b><br>
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
                <td style="text-align: left;  width: 100%; padding-top: 7px; padding-bottom: 7px; padding-left:6px;">Conditions de paiement à réception/Conditions générales de Vente.
                Des pénalités de retard au taux légal seront appliquées en cas de paiement après la date d'échéance. Conformément à la loi du 12.05.80, EUROCOMPUTER conserve la propriété du matériel jusqu'au paiement intégral du prix et des frais annexes.
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
                            <strong>RECODE by eurocomputer - 112 allée François Coli - 06210 Mandelieu - +33 4 93 47 25 00 - compta@recode.fr<br>
                            Ateliers en France - 25 ans d'expertise - Matériels neufs & reconditionnés </strong>
                        </td>
                        </tr>
                    </table>
            </page_footer>

            <div style="margin-top: 35px;">
                <table CELLSPACING=0 style="margin-top: 35px; width:100%">
                        
                        <?php 
                            $arrayPrice =[];
                            foreach($arrayOfDevisLigne as $value=>$obj){
                                    array_push( $arrayPrice, floatval(floatval($obj->devl_puht)*intval($obj->devl_quantite)));
                            };      
                            echo Pdfunctions::magicLineFTC($arrayOfDevisLigne , $temp);     
                        ?>
                </table>
            </div>



            <div>
            <?php
            if ($temp->devis__note_client) {
            echo $temp->devis__note_client;
            }

            if(!empty($facturation_auto))
            {
                echo '<br>Facture envoyée par mail à : '. $facturation_auto->contact__email . ' le ' . $formate; 
            }

            ?>
            </div>


            </page>
            <?php
            $content = ob_get_contents();

            if ($temp->cmd__nom_devis) {
            $name  = $temp->cmd__nom_devis;
            }
            else {
                $name = $temp->devis__id;
            }
            
            try 
            {
                $doc = new Html2Pdf('P','A4','fr');
                $doc->setDefaultFont('gothic');
                $doc->pdf->SetDisplayMode('fullpage');
                $doc->writeHTML($content);
                ob_clean();
                $numFact = '0000000' . $temp->cmd__id_facture ;
                $numFact = substr($numFact , -7 );
                if ($_SERVER['HTTP_HOST'] != "localhost:8080") 
                {
                    $doc->output('F:/'.$numFact.'F-'.$temp->devis__id.'D-'.$temp->client__id.'C.pdf' , 'F');

                }

                if (!empty($facturation_auto)) 
                {
                    $date = date("Y-m-d H:i:s");
                    $Pistage->addPiste($_SESSION['user']->id_utilisateur , $date, $temp->devis__id , 'facture envoyé automatiquement à ' . $facturation_auto->contact__email);
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
                        $mail->addAddress($facturation_auto->contact__email , '');
                        $mail->addBCC('crm@recode.fr');
                        //Attachments
                        $mail->addAttachment(__DIR__ . '/facture_mail/' . $numFact . '.pdf');
                        //Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Votre facture RECODE N:' . $numFact . '';
                        $mail->Body    = 'Vous trouverez ci-joint votre facture N:' . $numFact . ' de votre commande N: ' . $temp->devis__id. '';
                        $mail->send();
                        $deleted = unlink(__DIR__ . '/facture_mail/' . $numFact . '.pdf');
                        $doc->output('O:\intranet\Auto_Print\FC/' . $numFact . 'F-' . $temp->devis__id . 'D-' . $temp->client__id . 'C.pdf', 'F');
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        die();
                    }
                }
                else
                {
                    $doc->output('O:\intranet\Auto_Print\FC/'.$numFact.'F-'.$temp->devis__id.'D-'.$temp->client__id.'C.pdf' , 'F');
                }

                $_SESSION["facture"] =  ' BL n°: '. $temp->devis__id . ' Facturé n°: '. $numFact ;
                header('location: facture');
                
            } catch (Html2PdfException $e) 
            {
            die($e); 
            }
    }
               

}
