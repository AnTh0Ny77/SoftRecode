<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();


 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) 
 {
    header('location: login');
 }
 if ($_SESSION['user']->user__cmd_acces < 10 ) 
 {
   header('location: noAccess');
 }

 //déclaration des instances nécéssaires :
 $user= $_SESSION['user'];
 $Database = new App\Database('devis');
 $Database->DbConnect();
 $Keyword = new App\Tables\Keyword($Database);
 $Client = new App\Tables\Client($Database);
 $Contact = new \App\Tables\Contact($Database);
 $Cmd = new App\Tables\Cmd($Database);
 $General = new App\Tables\General($Database);
 $Article = new App\Tables\Article($Database);
 

if (!empty($_POST['POSTGarantie'])) 
{
    $cmd = $Cmd->GetById($_POST['POSTGarantie']);

    if ($cmd->devis__id_client_livraison) 
    {
      $livraisonRetour = $cmd->devis__id_client_livraison;
    }
    else 
    {
      $livraisonRetour = $cmd->client__id;
    }
    
   
    $retour = $Cmd->makeRetour( $cmd , 'Garantie',  000002 , $_SESSION['user']->id_utilisateur);
    $retour = $Cmd->GetById($retour);
    $update = $General->updateAll('cmd' , $livraisonRetour , 'cmd__client__id_livr' , 'cmd__id' , $retour->devis__id );
    $lignes = $Cmd->devisLigne($retour->devis__id); 
}



//si une mise a jour de societe de livraison a été effectuée: 
if ( !empty($_POST['idRetourLivraison'])) 
{
  $cmd = $Cmd->GetById($_POST['idCmd']);
  $update = $General->updateAll('cmd' , $_POST['changeLivraisonRetour'] , 'cmd__client__id_livr' , 'cmd__id' , $_POST['idRetourLivraison'] );
  $retour = $Cmd->GetById($_POST['idRetourLivraison']);
  $lignes = $Cmd->devisLigne($retour->devis__id);
}


$alert = false;


//si une fiche de garantie a été crée : 
if (!empty($_POST['qteArray'])) 
{
  if($_POST['typeRetour'] == 'Garantie')
  {
    $idClient = 000002;
  } else  $idClient = 000003;

  $count = 0 ;
  foreach ($_POST['qteArray'] as $key => $value) 
  {
    if (intval($value) > 0) 
    { 
      $count += 1 ;
    } 
  }

 if ($count > 0 ) 
 {
  $sujet = $Cmd->GetById($_POST['idRetour']);
  $retour = $Cmd->makeRetour($sujet , $_POST['typeRetour'] , $idClient , $_SESSION['user']->id_utilisateur );
  foreach ($_POST['qteArray'] as $key => $value) 
  {
    if (intval($value) > 0) 
    { 
      $transfert = $Cmd->makeAvoirLigne($_POST['idArray'][$key],$retour ,$value);
    } 
  }

$command = $Cmd->getById(intval($retour));
$commandLignes = $Cmd->devisLigne($retour);

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
             <?php echo $command->contact__nom . " " . $command->contact__prenom   ?> <br>
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
    header('location: ficheTravail');
} 
catch (Html2PdfException $e) 
{
  die($e); 
}
  
 }
 
 
 
}
 


 // Donnée transmise au template : 
 echo $twig->render('garantiesFiches.twig',
 [
 'user'=>$user,
 'cmd'=> $cmd,
 'lignes'=> $lignes,
 'alert'=> $alert, 
 'retour'=> $retour
 ]);
 
 
  
