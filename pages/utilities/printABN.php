<?php
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
$Abonnement = new \App\Tables\Abonnement($Database);
$General = new \App\Tables\General($Database);

if (empty($_SESSION['user']))
 {
    header('location: login');
 }

 // si une commande à été postée: 
 if (!empty($_POST['hiddenABN'])) 
 {
    //decode  le json
    $arrayABN = json_decode($_POST['hiddenABN']);
    //texte de l'export: 
    $export = "";
    
    //boucle sur les abonnements de la liste: 
    foreach($arrayABN as $ABN)
    {
       if (!empty($ABN->array)) 
       {

        $export .= $ABN->prestaLib .';';

        //date du jour:
        $date = date("Y-m-d H:i:s");

        //creation de l'objet retour
        $objectInsert = new stdClass;
        $objectInsert->devis__id = '';
        $objectInsert->cmd__date_cmd =  $date;
        $objectInsert->devis__id_client_livraison = $ABN->ab__client__id_fact ;
        $objectInsert->devis__contact_livraison = null;
        $objectInsert->devis__note_client = null;
        $objectInsert->devis__note_interne = null;
        $objectInsert->devis__user__id = $_SESSION['user']->id_utilisateur;

        //crée le retour et met a jour la commande : 
        $date = date("Y-m-d H:i:s");
        $temp = $Cmd->makeRetour($objectInsert , '' , $ABN->ab__client__id_fact , $_SESSION['user']->id_utilisateur );
        $update = $General->updateAll('cmd','ABF','cmd__etat','cmd__id',$temp);
        $update = $General->updateAll('cmd',' ','cmd__code_cmd_client','cmd__id',$temp);
        $General->updateAll('cmd', $date , 'cmd__date_fact' , 'cmd__id', $temp );
        $General->updateAll('cmd', $_SESSION['user']->id_utilisateur , 'cmd__user__id_fact' , 'cmd__id', $temp );

        //calcule le total la prestation et les lignes : 
        $total = 00 ; 
        foreach($ABN->array as $key )
        {
            $total += $key->abl__prix_mois * 3  ;
        }
       
        $objectInsert = new stdClass;
        $objectInsert->idDevis = $temp;
        $objectInsert->prestation = $ABN->ab__presta;
        $objectInsert->designation =  ' Période du ' . $_POST['dateDebut'] . ' au ' . $_POST['dateFin'] ;
        $objectInsert->etat = 'NC';
        $objectInsert->garantie = '';
        $objectInsert->comClient = '';
        $objectInsert->quantite = 1;
        $objectInsert->prix = floatval($total);
        $objectInsert->idfmm = '409';
        $objectInsert->extension = '';
        $objectInsert->prixGarantie = '';

        //insere la ligne
        $insert = $Cmd->insertLine($objectInsert);
        $Cmd->commande2facture($temp);

        //recupere les variable: 
        $temp = $Cmd->GetById($temp);
        $clientView = $Client->getOne($temp->client__id);
        $societeLivraison = false ;
        //si une societe de livraison existe : 

        if ($temp->devis__id_client_livraison) 
        {
            $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
        }
        //recupère les lignes liées: 

        $arrayOfDevisLigne = $Cmd->devisLigne($temp->devis__id);
        //met a jour la quantite facturée 

        foreach ($arrayOfDevisLigne as $ligne) 
        {
           $update = $General->updateAll('cmd_ligne', $ligne->devl_quantite,'cmdl__qte_fact','cmdl__cmd__id',$temp->devis__id);
        } 

        //recupere les dates et les formattes pour l'affichage
        $dateFact = new DateTime( $temp->cmd__date_fact);
        $formate = $dateFact->format('d/m/Y'); 
        $date_time = new DateTime( $temp->cmd__date_cmd);
        $formated_date = $date_time->format('d/m/Y'); 
        $Keyword = new \App\Tables\Keyword($Database);
        $garanties = $Keyword->getGaranties();

        //update en vla la facture: 
        $General->updateAll('cmd', 'VLA', 'cmd__etat' , 'cmd__id', $temp->devis__id );

        //commence l'enregistrement pour le rendu pdf :  
        ob_start();
     ?>





















     <style type="text/css">
         .page_header{
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
                 <td style="text-align: left;  width: 50%"><img  style=" width:65mm" src="public/img/recodeDevis.png"/></td>
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
                Numero de contrat : <?php echo $ABN->ab__cmd__id ?><br>
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
    
                    $totaux = Pdfunctions::totalABN($temp, $arrayOfDevisLigne);
    
    
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
                    Siret 397 937 068 00016 - APE 9511Z<br>
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
                    <strong>RECODE by eurocomputer - 112 allée François Coli - 06210 Mandelieu - +33 4 93 47 25 00 - contact@recode.fr<br>
                    Ateliers en France - 25 ans d'expertise - Matériels neufs & reconditionnés </strong>
                </td>
                </tr>
             </table>
    </page_footer>
    
    <div style="margin-top: 35px;">
        <table CELLSPACING=0 style="margin-top: 35px; width:100%">
                
                <?php 
                    $arrayPrice =[];
                   
                    $export .= $temp->devis__id . ';' . $temp->client__id . ';' ;
                    foreach($arrayOfDevisLigne as $value=>$obj)
                    {
                        array_push( $arrayPrice, floatval($obj->devl_puht)*intval($obj->devl_quantite));
                    $export .= $obj->devl_puht .';
';
                        
                    };
                    echo Pdfunctions::magicLineABN($arrayOfDevisLigne , $temp , $ABN->prestaLib);
                ?>
        </table>
    </div>
    
    
    
    <div>
    <?php
    if ($temp->devis__note_client) {
       echo $temp->devis__note_client;
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
            $doc->output('F:\F'.$numFact.'-D'.$temp->devis__id.'-C'.$temp->client__id.'.pdf' , 'F');
        }
        $doc->output('O:\intranet\Auto_Print\FC\F'.$numFact.'-D'.$temp->devis__id.'-C'.$temp->client__id.'.pdf' , 'F');
       
     
        
     
        
        
     } catch (Html2PdfException $e) 
     {
       die($e); 
     }


       }
       
      
      
       
    }
    $file = fopen("auto.csv", "w");
    fwrite($file , $export);
    fclose($file);
    header('location: abonnement');
   
}
