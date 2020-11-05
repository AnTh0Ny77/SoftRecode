
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
 $General = new App\Tables\General($Database);
 $Database->DbConnect();


 

//creation devis :
if (!empty($_POST['clientSelect']) && empty($_POST['modifReturn'])) 
{
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
}

//modification de devis:
if (!empty($_POST['modifReturn'])) 
{
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

    $General->updateAll('cmd' , $_POST['clientSelect'] , 'cmd__client__id_fact' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $livraison , 'cmd__client__id_livr' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $_POST['contactSelect'] , 'cmd__contact__id_fact' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $_POST['contactLivraison'] , 'cmd__contact__id_livr' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $_POST['globalComClient'] , 'cmd__note_client' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $_POST['globalComInt'] , 'cmd__note_interne' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $_POST['modele'] , 'cmd__modele_devis' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $tva , 'cmd__tva' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $titre , 'cmd__nom_devis' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $_POST['code'] , 'cmd__code_cmd_client' , 'cmd__id' , $_POST['modifReturn']);
    $idDevis = $_POST['modifReturn'];
} 




// creation lignes : 
if (!empty($_POST['devis'])) 
{
    
    $newLines = $Devis->insertLine(
    $_POST['ordre'] , $_POST['devis'] , $_POST['presta'], $_POST['fmm'],
    $_POST['designation'] , $_POST['etat'] , $_POST['garantie'],
    intval($_POST['quantite']) , floatval($_POST['promo']), floatval($_POST['prix']), $_POST['commentaire'] , $_POST['interne']);
    $idDevis = $_POST['devis'];
    $_POST['devis'] = "";


    //extension de garanties : 
    foreach ($_POST['xtendP'] as $key => $value) 
    {
       if (!empty($value[0])) 
       {
           foreach ($_POST['xtendG'] as $mois => $promo) 
           {
               if (intval($mois) == intval($key)) 
               {
                  
               }
           }
       }
    }
}


$devis = $Cmd->GetById($idDevis);
$devisLigne = $Cmd->devisLigne($idDevis);
$articleTypeList = $Article->getModels();
$prestaList = $Keywords->getPresta();
$garanties = $Keywords->getGaranties();
$etatList = $Keywords->getEtat();


// Donnée transmise au template : 
echo $twig->render('NligneDevisV2.twig',[
    'user'=>$_SESSION['user'],
    'devis'=> $devis,
    'prestaList' => $prestaList , 
    'articleList' => $articleTypeList,
    'garanties' => $garanties,
    'etatList'=> $etatList,
    'devisLignes' => $devisLigne
    
 ]);


?>