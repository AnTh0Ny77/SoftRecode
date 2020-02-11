<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
session_start();


// Si un devis a été validé : 
if (!empty($_POST)) {
    $devisData = json_decode ($_POST["dataDevis"], true);
    

    ob_start();
    ?>
    <style type="text/css">
         strong{ color:#000;}
         h3{ color:#666666;}
         h2{ color:#3b3b3b;}
    </style>
    <page  backleft="20mm" backright="20mm">
    <page_header>
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;  width: 50%"><img  style=" width:75mm" src="http://localhost:8080/DevisRecode/public/img/recodeDevis.png"/></td>
                <td style="text-align: left; width:50%"><h3>Reparation-Location-Vente</h3>imprimantes- lecteurs codes-barres<br><a>www.recode.fr</a><br><br><br>REF CLIENT :</td>
            </tr>
            <tr>
                <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h2>Devis- 3190808</h2><br>07/07/07<br>anthonybs.pro@gmail.com<p><small>Notre offre est valable une semaine à dater du : 07/07/07</small></p></td>
                <td style="text-align: left; width:50%"><small>livraison & facturation</small><strong><br>Nom Société<br>adr1<br>adr2<br>99999 ville</strong></td>
            </tr>
        </table>
    </page_header>
    </page>
    <?php
    $content = ob_get_contents();
    
    try {
        $doc = new Html2Pdf('P','A4','fr');
        $doc->pdf->SetDisplayMode('fullpage');
        $doc->writeHTML($content);
        ob_clean();
        $doc->output('exemple.pdf');
    } catch (Html2PdfException $e) {
      die($e); 
    }}
    else{ var_dump($_POST);}

   