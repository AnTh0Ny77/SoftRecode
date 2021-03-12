<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
session_start();


 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']->id_utilisateur)) 
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
 $Pisteur = new App\Tables\Pistage($Database);
 $Stats = new App\Tables\Stats($Database);
 $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
 

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
  $Pisteur->addPiste($_SESSION['user']->id_utilisateur, $date , $_POST['creaLivraison'] , ' à entammé la creation d une fiche de garantie de type : '.$_POST['typeRetour'].' ' );
  //type de retour maintenance ou garantie 
  $type = intval($_POST['typeRetour']);
  if ($type == 02 )
  {
    $text = 'Garantie';
  }
  elseif($type == 06)
  {
    $text = 'RMA Fournisseur';
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
if (!empty($_POST['comInterne'])) 
  {
    $updateCom = $General->updateAll('cmd' , $_POST['comInterne'] , 'cmd__note_interne' , 'cmd__id' , $command->devis__id );
  }
$commandLignes = $Cmd->devisLigne($_POST['PrintFicheCreation']);
$update = $General->updateAll('cmd' , 'CMD' , 'cmd__etat' , 'cmd__id' , $command->devis__id );

$_SESSION['garanFiche'] = $command->devis__id;
//date du jour:
$date = date("Y-m-d H:i:s");

$Pisteur->addPiste($_SESSION['user']->id_utilisateur, $date , $_POST['PrintFicheCreation'] , 'a imprimé sa fiche de garantie ' );

$General->updateAll('cmd' , $date  , 'cmd__date_devis ' , 'cmd__id' , $command->devis__id );
$General->updateAll('cmd' ,$date, 'cmd__date_cmd', 'cmd__id' , $command->devis__id);
header('location: printFt');


}
 

 // Donnée transmise au template : 
 echo $twig->render('garantieCreation.twig',
 [
 'user'=>$user,
 'articleList' => $articleTypeList,
 'retour' => $retour,
 'lignes' => $lignes
 ]);
 
 
  
