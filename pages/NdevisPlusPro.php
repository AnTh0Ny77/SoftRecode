<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

    //URL bloqué si pas de connexion :
 if (empty($_SESSION['user'])) {
    header('location: login');
 }
 if ($_SESSION['user']->user__devis_acces < 10 ) {
  header('location: noAccess');
}

    //Connexion et Entités : 
 $Database = new App\Database('devis');
 $Client = new App\Tables\Client($Database);
 $Keywords = new App\Tables\Keyword($Database);
 $Contact = new App\Tables\Contact($Database);
 $Article = new App\Tables\Article($Database);
 
 $Cmd = new App\Tables\Cmd($Database);
 $Database->DbConnect();

    //listes  : 
 $clientList = $Client->getAll();
 $modeleList = $Keywords->getModele();

    //alertes et variables : 
 $alertClient = false;
 $contactList = null;
 $contactLivraison = null;

    //traitement de l'alerte : 
if (isset($_SESSION['alertV2'])) 
{
    switch ($_SESSION['alertV2']) 
    {
        case 'Aucun':
            $_SESSION['alertV2'] = '';
            $alertClient = 'Aucun Client selectionné';
            break;
        
        default:
            $_SESSION['alertV2'] = '';
            break;
    }
}

    //traitement de la modification 
if (!empty($_POST['modif'])) 
{
    $modif = $Cmd->GetById($_POST['modif']);   
        //contact : 
    if (!empty($modif->client__id)) 
    {   
        $contactList = $Contact->getFromLiaison($modif->client__id);
    } 
        //livraison :
    if (!empty($modif->devis__id_client_livraison)) 
    {
        $contactLivraison = $Contact->getFromLiaison($modif->devis__id_client_livraison);
    } 
}else $modif =  false;


       
       



// Donnée transmise au template : 
echo $twig->render('NdevisPlusPro.twig',[
   'user'=>$_SESSION['user'],
   'clientList' => $clientList,
   'modeleList' => $modeleList,
   'alertClient' => $alertClient,
   'modif'=> $modif,
   'contactList' => $contactList,
   'contactLivraison' => $contactLivraison
]);;
