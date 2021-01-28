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

if (empty($_SESSION['user'])) {
    header('location: login');
 }


 if (!empty($_SESSION['creaFiche'])) 
{
    $command = $Command->getById(intval($_SESSION['creaFiche']));
    $_POST['devisCommande'] = $command->devis__id ;
    $_SESSION['creaFiche'] = '';
}

if (!empty($_SESSION['garanFiche'])) 
{
    $command = $Command->getById(intval($_SESSION['garanFiche']));
    $_POST['devisCommande'] = $command->devis__id ;
    $_SESSION['garanFiche'] = '';
}


 // si une validation de devis a été effectuée : 
if(!empty($_POST['devisCommande']))
{
  $delete_daugther_lines_with_inactif_mum = $Command->delete_ligne_inactif_filles($_POST['devisCommande']);
  $deleteLines = $Command->delete_ligne_inactif($_POST['devisCommande']);
  $date = date("Y-m-d H:i:s");
  $Command->updateStatus('CMD',$_POST['devisCommande']);
  $Command->updateDate('cmd__date_cmd' , $date , $_POST['devisCommande'] );
  $Command->updateAuthor('cmd__user__id_cmd' , $_SESSION['user']->id_utilisateur , $_POST['devisCommande']);
  if (!empty($_POST['arrayLigneDeCommande'])) 
  {
    $validLignes = json_decode($_POST['arrayLigneDeCommande']);
    foreach ($validLignes as $lignes) 
    {
      $Command->updateGarantie(
        $lignes->devl__prix_barre[0],
        $lignes->devl__prix_barre[1],
        $lignes->devl__note_interne,
        $lignes->devl_quantite,
        $lignes->cmdl__cmd__id,
        $lignes->devl__ordre );
    } 
  }
  if (!empty($_POST['code_cmd'])) 
  {
    $Global->updateAll('cmd', $_POST['code_cmd'],'cmd__code_cmd_client', 'cmd__id', $_POST['devisCommande']);
  }
 
  if (!empty($_POST['ComInterCommande'])) 
  {
    $Global->updateAll('cmd', $_POST['ComInterCommande'],'cmd__note_interne', 'cmd__id', $_POST['devisCommande']);
  }

  //contient l'id du devis pour l'imprssion de la fiche de travail : client2.js
  $print_request = $_POST['devisCommande'];
}

  
$command = $Command->getById(intval($_POST['devisCommande']));

//si la date de divis est vide ( reliquat ou garantie)
if (empty($command->devis__date_crea)) 
{
    $date = date("Y-m-d H:i:s");
    $Global->updateAll('cmd', $date,'cmd__date_devis', 'cmd__id', $command->devis__id);
}

$commandLignes = $Command->devisLigne($_POST['devisCommande']);
$clientView = $Client->getOne($command->client__id);
$user = $User->getByID($clientView->client__id_vendeur);
$userCMD = $User->getByID($command->cmd__user__id_cmd);
$societeLivraison = false ;

//si une societe de livraison existe: 
if($command->devis__id_client_livraison) 
{
    $societeLivraison = $Client->getOne($command->devis__id_client_livraison);
}

$command = $Command->getById(intval($_POST['devisCommande']));
    
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

<page backtop="10mm" backleft="5mm" backright="5mm" backbottom='15%' footer="page">
    <page_footer >
         
     <div style=" width: 100%; position: absolute; bottom:1px">
         
     <?php
      
      if (!empty($societeLivraison->client__memo_config)) 
      {
          echo '<strong>ATTENTION CONFIG CLIENT</strong> : '. $societeLivraison->client__memo_config;
      }

     ?>
   
        <table CELLSPACING=0 style=" width: 100%;  ">
            <tr style="background-color: #dedede;">
                        <td style="text-align: center; width: 30%"><strong>Préparé par</strong></td>
                        <td style="text-align: center; width: 40%"><strong>Réceptionné par : </strong></td>
                        <td style="text-align: center; width: 30%"><strong>POIDS</strong></td>
            </tr> 
            <tr>
                <td style="border: 1px #ccc solid; height: 150px; text-align:center;">
                
                    <span style="margin-top: 65px; background-color: #dedede;"><strong>Vérifié par</strong></span>
                </td>
                <td style="border: 1px #ccc solid; ">
                    <small><i>Nom/signature/tampon</i></small>
                </td>
                <td style="border: 1px #ccc solid; ">
                    
                </td>
            </tr>
        </table>  
   
   </div>  
    </page_footer>
     <table style="width: 100%;">
         <tr>
             <td style="text-align: left;  width: 50%"><img  style=" width:60mm" src="public/img/recodeDevis.png"/></td>
             <td style="text-align: left; width:50%"><h3>Reparation-Location-Vente</h3>imprimantes- lecteurs codes-barres<br>
             <a>www.recode.fr</a><br><br>
             <br></td>
             </tr>
             <tr>
             <td  style="text-align: left;  width: 50% ; margin-left: 25%;"><h4>Fiche De travail -  <?php echo $command->devis__id ?></h4>
             <barcode dimension="1D" type="C128" label="none" value="<?php echo $command->devis__id ?>" style="width:40mm; height:8mm; color: #3b3b3b; font-size: 4mm"></barcode><br>

            Commandé le : <strong><?php echo $formated_date ?></strong><br>
            Commercial : <strong><?php
                        if (!empty($user)) 
                        {
                            echo  $user->nom . ' '. $user->prenom ;
                        } 
                        else 
                        {
                            echo 'Non renseigné';
                        }
                    ?>
                </strong> 
                    <?php
                        if (!empty($user->postefix)) 
                        {
                        echo ' (Tél: '. $user->postefix .')';
                        } 

                        
                    ?>
                     
                
                    <?php
                        if (!empty($userCMD)) 
                        {
                            echo  '<br>Commandé par : <strong>'.$userCMD->nom . ' '. $userCMD->prenom .'</strong> ';
                        } 
                    ?>
                
                    <?php
                        if (!empty($userCMD->postefix)) 
                        {
                        echo ' (Tél: '. $userCMD->postefix .')';
                        } 

                       
                    ?> 
                    </td>
             <td style="text-align: left; width:50%"><strong><?php 
              if ($societeLivraison) 
              {

                if ($command->devis__contact__id) {
                    // si un contact est présent dans l'adresse de facturation :
                    $contact = $Contact->getOne($command->devis__contact__id);
                    echo "<small>facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom. "</small><strong><br>";
                    echo Pdfunctions::showSociete($clientView) ." </strong> ";
                    if (!empty($clientView->client__tel)) 
                        {
                           echo '<br> TEL : '.$clientView->client__tel.'';
                        }  
                
                    if ($command->devis__contact_livraison) {
                        //si un contact est présent dans l'adresse de livraison : 
                        $contact2 = $Contact->getOne($command->devis__contact_livraison);
                        echo "<br> <small>livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                        echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
                        if (!empty($societeLivraison->client__tel)) 
                        {
                           echo '<br> TEL : '.$societeLivraison->client__tel.'';
                        }      
                    }
                    else {
                        // si pas de contact de livraison : 
                        echo "<br> <small>livraison :</small><strong><br>";
                        echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
                        if (!empty($societeLivraison->client__tel)) 
                        {
                           echo '<br> TEL : '.$societeLivraison->client__tel.'';
                        }  
                    } 
                }

                else {
                    echo "<small>facturation :</small><strong><br>";
                    echo Pdfunctions::showSociete($clientView) ." </strong>" ;
                    if ($command->devis__contact_livraison) {
                        $contact2 = $Contact->getOne($command->devis__contact_livraison);
                        echo "<br> <small>livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                        echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
                        if (!empty($societeLivraison->client__tel)) 
                        {
                           echo '<br> TEL : '.$societeLivraison->client__tel.'';
                        }  
                    } else {
                        echo "<br> <small>livraison :</small><strong><br>";
                        echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
                        if (!empty($societeLivraison->client__tel)) 
                        {
                           echo '<br> TEL : '.$societeLivraison->client__tel.'';
                        } 
                    }  
                }  
         } 



         else{
            if ($command->devis__contact__id) {
            $contact = $Contact->getOne($command->devis__contact__id);
            echo "<small>livraison & facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom."</small><strong><br>";
            echo Pdfunctions::showSociete($clientView)  ."</strong>";
            if (!empty($clientView->client__tel)) 
            {
               echo '<br> TEL : '.$clientView->client__tel.'';
            }  
            }
            else{
                echo "<small>livraison & facturation : </small><strong><br>";
                echo Pdfunctions::showSociete($clientView)  ."</strong>";
            if (!empty($clientView->client__tel)) 
                {
                   echo '<br>TEL : '.$clientView->client__tel.'';
                }  
            }

         } 
          
         
         
         if ($command->cmd__code_cmd_client) 
         {
            echo "<br> Code cmd: " . $command->cmd__code_cmd_client ;
         }
         ?>
         </strong>
            </td>
         </tr>
     </table>


     <table CELLSPACING=0 style="width: 100%;  margin-top: 80px; ">
             <tr style=" margin-top : 50px; background-color: #dedede;">
                <td style="width: 22%; text-align: left;">Presta<br>Type<br>Gar.</td>
                <td style="width: 60%; text-align: left">Ref Tech<br>Désignation Client<br>Complement techniques</td>
                <td style="text-align: center; width: 9%"><strong>CMD</strong></td>
                <td style="text-align: center; width: 9%"><strong>Livré</strong></td>
             </tr> 
             <?php
             foreach ($commandLignes as $item) {
                if($item->cmdl__garantie_option > $item->devl__mois_garantie) 
                {
                  $temp = $item->cmdl__garantie_option ;
                }
                 else 
                 { 
                     if (!empty($item->devl__mois_garantie)) 
                     {
                        $temp = $item->devl__mois_garantie;
                     }
                     else
                     {
                        $temp = "";
                     }
                   
                 }

               

                echo "<tr style='font-size: 100%;>
                        <td style='border-bottom: 1px #ccc solid'> ". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois</td>
                        <td style='border-bottom: 1px #ccc solid; width: 55%;'> 
                            <br> <small>désignation :</small> <b>" . $item->devl__designation ."</b><br>"
                            .$item->famille__lib. " " . $item->marque . " " .$item->modele. " ". $item->devl__modele  ." " .$item->devl__note_interne . 
                        "</td>
                         <td style='border-bottom: 1px #ccc solid; text-align: center'><strong> "  . $item->devl_quantite. " </strong></td>
                         <td style='border-bottom: 1px #ccc solid; border-left: 1px #ccc solid; text-align: right'><strong>  </strong></td>
                      </tr>";
             }
             ?>
     </table> 
     
     <table style=" margin-top: 50px; width: 100%">
             <tr style=" margin-top: 200px; width: 100%"><td><small>Commentaire:</small></td></tr>
             <tr >
             <td style='border-bottom: 1px black solid; border-top: 1px black solid; width: 100%' > <?php echo  $command->devis__note_interne ?> </td>
            </tr>
     </table>



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
    
    if ($_SESSION['user']->user__devis_acces < 10 ) 
        {
            header('location: ficheTravail');
        }
    else
        {
            header('location: ficheTravail');
        }

   
} 
catch (Html2PdfException $e) 
{
  die($e); 
}
    


 