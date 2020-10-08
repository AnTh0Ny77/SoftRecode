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


 //si ouverture venant de la liste des fiche de travail : creation d' un retour + recup des info de la commande d'origine 
 // attention devis doit passer en status spécial pour passer en poubelle si rien n'est validé : 
if (!empty($_POST['POSTGarantie'])) 
{
    $cmd = $Cmd->GetById($_POST['POSTGarantie']);

    if (!empty($cmd->devis__id_client_livraison)) 
    {
      $livraisonRetour = $cmd->devis__id_client_livraison;
    }
    else 
    {
     
      $livraisonRetour = intval($cmd->client__id);
    }

   
    $retour = $Cmd->makeRetour( $cmd , 'Garantie',  000002 , $_SESSION['user']->id_utilisateur);
    $retour = $Cmd->GetById($retour);
    $update = $General->updateAll('cmd' , $livraisonRetour , 'cmd__client__id_livr' , 'cmd__id' , $retour->devis__id );
    
    $update = $General->updateAll('cmd' , 'PBL' , 'cmd__etat' , 'cmd__id' , $retour->devis__id );
    $retour = $Cmd->GetById($retour->devis__id);
    $lignes = $Cmd->devisLigne($retour->devis__id); 
}



//si une mise a jour de societe de livraison a été effectuée: 
// Attention on doit recup les bonne info cmd d'origine et compagnie :
if ( !empty($_POST['idRetourLivraison'])) 
{
  $cmd = $Cmd->GetById($_POST['idCmd']);
  $update = $General->updateAll('cmd' , $_POST['changeLivraisonRetour'] , 'cmd__client__id_livr' , 'cmd__id' , $_POST['idRetourLivraison'] );
  $update = $General->updateAll('cmd' , null , 'cmd__contact__id_livr' , 'cmd__id' , $_POST['idRetourLivraison'] );
  $retour = $Cmd->GetById($_POST['idRetourLivraison']);
  $lignes = $Cmd->devisLigne($retour->devis__id);
}

//si une mise à jour de contact de societe livraison a ete effectuée :
if (!empty($_POST['ChangeContact']) && !empty($_POST['contactIdCmd']) && !empty($_POST['contactIdRetour'])) 
{
  $cmd = $Cmd->GetById($_POST['contactIdCmd']);
  $update = $General->updateAll('cmd' , $_POST['ChangeContact'] , 'cmd__contact__id_livr' , 'cmd__id' , $_POST['contactIdRetour'] );
  $retour = $Cmd->GetById($_POST['contactIdRetour']);
  $lignes = $Cmd->devisLigne($retour->devis__id);
}

//si une machine à été ajouté sur  le retour fraichement créer : 
//Attention !! recup les info d'origine et mettre en MAJ le devis : 
if (!empty($_POST['hiddenAddLines']) && !empty($_POST['hiddenAddLinesCmd']) && !empty($_POST['designationArticle'])) 
{
  
  $cmd = $Cmd->GetById($_POST['hiddenAddLinesCmd']);
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

//si une suppression de ligne a été envoyée
if (!empty($_POST['deleteLine']) && !empty($_POST['deleteLineRetour']) && !empty($_POST['deleteLineCmd'])) 
{
  $delete = $Cmd->deleteLine($_POST['deleteLine'] , $_POST['deleteLineRetour'] );
  $cmd = $Cmd->GetById($_POST['deleteLineCmd']);
  $retour = $Cmd->GetById($_POST['deleteLineRetour']);
  $lignes = $Cmd->devisLigne($retour->devis__id);
  if (empty($lignes)) 
  {
    $update = $General->updateAll('cmd' , 'PBL' , 'cmd__etat' , 'cmd__id' , $retour->devis__id );
    $retour = $Cmd->GetById($_POST['deleteLineRetour']);
  }
 
}

//si un mise à jour du status de la fiche : Maintenance / Retour : 
if (!empty($_POST['typeRetour']) && !empty($_POST['StatusRetour']) && !empty($_POST['StatusCmd'])) 
{
  $cmd = $Cmd->GetById($_POST['StatusCmd']);
  $update = $General->updateAll('cmd' , $_POST['typeRetour'] , 'cmd__client__id_fact' , 'cmd__id' , $_POST['StatusRetour']);

  
  $retour = $Cmd->GetById($_POST['StatusRetour']);
  $lignes = $Cmd->devisLigne($retour->devis__id);
  if (!empty($_POST['codeComdInput'])) 
  {
     $sujet = $retour->cmd__code_cmd_client . ' Ticket n° : '. $_POST['codeComdInput'];
    $update = $General->updateAll('cmd' , $sujet , 'cmd__code_cmd_client' , 'cmd__id' , $_POST['StatusRetour']);
  }
  
}


$alert = false;


//si une fiche de garantie a été crée : 
if (!empty($_POST['rechercheF'])) 
{
 
$command = $Cmd->getById($_POST['rechercheF']);
$commandLignes = $Cmd->devisLigne($_POST['rechercheF']);
$update = $General->updateAll('cmd' , 'CMD' , 'cmd__etat' , 'cmd__id' , $command->devis__id );
$_SESSION['garanFiche'] = $command->devis__id;

header('location: printFt');

 

 
}
 


 // Donnée transmise au template : 
 echo $twig->render('garantiesFiches.twig',
 [
 'user'=>$user,
 'cmd'=> $cmd,
 'lignes'=> $lignes,
 'alert'=> $alert, 
 'retour'=> $retour,
 'articleList' => $articleTypeList
 ]);
 
 
  
