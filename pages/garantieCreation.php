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
 

 $articleTypeList = $Article->getModels();

 $retour = false ;
 $lignes = false ;
 //si une societe, un code commande et une commande reférence on été choisis:
 if (!empty($_POST['creaLivraison']) && !empty($_POST['typeRetour']) && !empty($_POST['creaNumCommande'])) 
 {
   
  //date du jour:
  $date = date("Y-m-d H:i:s");
  //creation de l'objet retour
  $objectInsert = new stdClass;
  $objectInsert->devis__id = $_POST['creaNumCommande'];
  $objectInsert->cmd__date_cmd =  $date;
  $objectInsert->devis__id_client_livraison = $_POST['creaLivraison'];
  $objectInsert->devis__contact_livraison = null;
  $objectInsert->devis__note_client = null;
  $objectInsert->devis__note_interne = null;
  $objectInsert->devis__user__id = $_SESSION['user']->id_utilisateur;

  //type de retour maintenance ou garantie 
  $type = intval($_POST['typeRetour']);
  if ($type == 02 )
  {
    $text = 'Garantie';
  }
  else
  {
    $text = 'Maintenance';
  }
  
  $retour = $Cmd->makeRetour( $objectInsert , $text,  $type , $_SESSION['user']->id_utilisateur);
  $retour = $Cmd->GetById($retour);
  $update = $General->updateAll('cmd' , 'PBL' , 'cmd__etat' , 'cmd__id' , $retour->devis__id );
 }



 //si une mise à jour de contact de societe livraison a ete effectuée :
if (!empty($_POST['ChangeContact'])  && !empty($_POST['idRetourChangeContact'])) 
{
  $update = $General->updateAll('cmd' , $_POST['ChangeContact'] , 'cmd__contact__id_livr' , 'cmd__id' , $_POST['idRetourChangeContact'] );
  $retour = $Cmd->GetById($_POST['idRetourChangeContact']);
  $lignes = $Cmd->devisLigne($retour->devis__id);
}


//si une machine à été ajouté sur  le retour fraichement créer : 
//Attention !! recup les info d'origine et mettre en MAJ le devis : 
if (!empty($_POST['hiddenAddLines']) &&   !empty($_POST['designationArticle'])) 
{
  
  $retour = $Cmd->GetById($_POST['hiddenAddLines']);
  $objectInsert = new stdClass;
  $objectInsert->idDevis = $retour->devis__id;
  $objectInsert->prestation = $_POST['typeLigne'];
  $objectInsert->designation = $_POST['designationArticle'];
  $objectInsert->etat = 'NC';
  $objectInsert->garantie = '';
  $objectInsert->comClient = '';
  $objectInsert->quantite = $_POST['quantiteLigne'];
  $objectInsert->prix = '';
  $objectInsert->idfmm = $_POST['choixDesignation'];
  $objectInsert->extension = '';
  $objectInsert->prixGarantie = '';

  $insert = $Cmd->insertLine($objectInsert);
  $lignes = $Cmd->devisLigne($retour->devis__id);
  $update = $General->updateAll('cmd' , 'PLL' , 'cmd__etat' , 'cmd__id' , $retour->devis__id );
  $retour = $Cmd->GetById($_POST['hiddenAddLines']);
}


//si une supression de ligne à été envoyée: 
//si une suppression de ligne a été envoyée
if (!empty($_POST['deleteLine']) && !empty($_POST['deleteLineRetour'])) 
{
  $delete = $Cmd->deleteLine($_POST['deleteLine'] , $_POST['deleteLineRetour'] );
  $retour = $Cmd->GetById($_POST['deleteLineRetour']);
  $lignes = $Cmd->devisLigne($retour->devis__id);
  if (empty($lignes)) 
  {
    $update = $General->updateAll('cmd' , 'PBL' , 'cmd__etat' , 'cmd__id' , $retour->devis__id );
    $retour = $Cmd->GetById($_POST['deleteLineRetour']);
  }
 
}

//si une demande d'impression a été faite : 
if (!empty($_POST['PrintFicheCreation'])) 
{
$command = $Cmd->getById($_POST['PrintFicheCreation']);
$commandLignes = $Cmd->devisLigne($_POST['PrintFicheCreation']);
$update = $General->updateAll('cmd' , 'CMD' , 'cmd__etat' , 'cmd__id' , $command->devis__id );

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
             <td style="text-align: left; width:50%">
             <?php
              $livraison = $Client->getOne($command->devis__id_client_livraison);
              echo Pdfunctions::showSociete($livraison);
             ?>
             <br>
             
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
    if ($_SERVER['HTTP_HOST'] != "localhost:8080") 
    {
      $doc->output('O:\intranet\Auto_Print\FT\Ft_'.$command->devis__id.'.pdf' , 'F'); 
    }
   
    header('location: ficheTravail');
} 
catch (Html2PdfException $e) 
{
  die($e); 
}
}
 

 // Donnée transmise au template : 
 echo $twig->render('garantieCreation.twig',
 [
 'user'=>$user,
 'articleList' => $articleTypeList,
 'retour' => $retour,
 'lignes' => $lignes
 ]);
 
 
  
