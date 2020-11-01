
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
 $Devis = new App\Tables\Devis($Database);
 $Cmd = new App\Tables\Cmd($Database);
 $Database->DbConnect();



//traitement de la creation d'un nouveau devis : 
switch ($_POST['clientSelect']) 
{
    case 'Aucun':
        $_SESSION['alertV2'] = 'Aucun';
        header('location: DevisV2'); 
        break;
    
    default:
        //creation de Devis:
        $date = date("Y-m-d H:i:s");
        //livraison
        if ($_POST['clientLivraison'] != 'Aucun')
        { $livraison = $_POST['clientLivraison'];}
        else { $livraison =  $_POST['clientSelect'];}
        //contacts
        if ($_POST['contactSelect'] != 'Aucun')
        { $_POST['contactSelect'] = $_POST['contactSelect'];}
        else { $_POST['contactSelect'] =  null;}
        //contacts livraison
        if ($_POST['contactLivraison'] != 'Aucun')
        { $_POST['contactLivraison'] = $_POST['contactLivraison'];}
        else { $_POST['contactLivraison'] =  null;}
        //tva 
        $tva = $Client->getOne($livraison);
        $tva = $tva->client__tva;
        //user:
        $user = $_SESSION['user']->id_utilisateur;
        //titre : 
        if (!empty($_POST['titreDevis'])) 
        {$accents = array('/[áàâãªäÁÀÂÃÄ]/u'=>'a','/[ÍÌÎÏíìîï]/u'=>'i','/[éèêëÉÈÊË]/u'=>'e','/[óòôõºöÓÒÔÕÖ]/u'=>'o','/[úùûüÚÙÛÜ]/u'=>'u','/[çÇ]/u' =>'c');
            $titre = preg_replace(array_keys($accents), array_values($accents), $_POST['titreDevis']); $titre  = strtoupper($titre);$titre = preg_replace('/([^.a-z0-9]+)/i', '-', $titre);
        }else {$titre = '' ;}
        //creation du devis : 
        $idDevis = $Devis->createDevis(
        $date, $user, $_POST['clientSelect'], $livraison, $_POST['contactSelect'],
        $_POST['contactLivraison'], $_POST['globalComClient'], $_POST['globalComInt'],
        $_POST['modele'], $tva, $titre, $_POST['code']);
        break;
}


$devis = $Cmd->GetById($idDevis);
$articleTypeList = $Article->getModels();
$prestaList = $Keywords->getPresta();
$garanties = $Keywords->getGaranties();





// Donnée transmise au template : 
echo $twig->render('NligneDevisV2.twig',[
    'user'=>$_SESSION['user'],
    'devis'=> $devis,
    'prestaList' => $prestaList , 
    'articleList' => $articleTypeList,
    'garanties' => $garanties
    
 ]);
















?>