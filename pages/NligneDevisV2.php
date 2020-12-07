
<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
use App\Methods\Pdfunctions;
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
            //verification de la tva intracom à 0 : 
            $verifClient  = $Client->getOne($_POST['clientSelect']);
            // if (empty($verifClient->client__tva_intracom) && $verifClient->client__tva == ) 
            // {
               
            // }
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
            'STT', $tva, $titre, $_POST['code']);
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
    $General->updateAll('cmd' , 'STT' , 'cmd__modele_devis' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $tva , 'cmd__tva' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $titre , 'cmd__nom_devis' , 'cmd__id' , $_POST['modifReturn']);
    $General->updateAll('cmd' , $_POST['code'] , 'cmd__code_cmd_client' , 'cmd__id' , $_POST['modifReturn']);
    $idDevis = $_POST['modifReturn'];
} 



//descend une ligne : 
if (!empty($_POST['down'])) 
{
    $updateOrdre = $Devis->upanDonwn('down',$_POST['downId'], $_POST['downLigne'] , $_POST['down'][0]);
    $idDevis = $_POST['downId'];
}

//monte une ligne : 
if (!empty($_POST['up'])) 
{
    $updateOrdre = $Devis->upanDonwn('up',$_POST['upId'], $_POST['upLigne'] , $_POST['up'][0]);
    $idDevis = $_POST['upId'];
}

//efface une ligne : 
if (!empty($_POST['deleteId'])) 
{
    $Devis->deleteLine($_POST['deleteId']);
    $idDevis = $_POST['deleteIdCmd'];
}

//modification de ligne demandée :
$modif = null; 
$duplicate = null;
if (!empty($_POST['modifyId']) && !empty($_POST['modifyIdCmd']) && empty($_POST['duplicate'])) 
{
    $modif = $Devis->selecOneLine($_POST['modifyId']);
    $xtend = $Devis->selectGaranties($_POST['modifyId']);
    $modif->xtend = $xtend;
    $modif->modif = true;
    $idDevis = $_POST['modifyIdCmd'];
}
//duplicata de ligne :
if (!empty($_POST['modifyId']) && !empty($_POST['duplicate'])) 
{
    $modif = $Devis->selecOneLine($_POST['modifyId']);
    $xtend = $Devis->selectGaranties($_POST['modifyId']);
    $modif->xtend = $xtend;
    $idDevis = $_POST['modifyIdCmd'];
    $duplicate = $_POST['duplicate'];
}



// creation lignes ou duplicata : 
if (!empty($_POST['devis']) && empty($_POST['boolModif'])) 
{
    if (empty($_POST['etat'])) 
    {
        $_POST['etat'] = 'NC.';
    }
    if (empty($_POST['garantie'])) 
    {
        $_POST['garantie'] = '00';
    }
    if (empty($_POST['xtendP'])) 
    {
        $_POST['xtendP'] = [];
    }
    


    $newLines = $Devis->insertLine(
    $_POST['devis'] , $_POST['presta'], $_POST['fmm'],
    $_POST['designation'] , $_POST['etat'] , $_POST['garantie'],
    intval($_POST['quantite']) , floatval($_POST['promo']), floatval($_POST['prix']), $_POST['commentaire'] , $_POST['interne']);
    $idDevis = $_POST['devis'];
    $_POST['devis'] = "";

    //affichage de l'état : 
    if (!empty($_POST['checkEtat'])) 
    {
        $General->updateAll('cmd_ligne' , 1 , 'cmdl__etat_masque' , 'cmdl__id' , $newLines);
    }
    else 
    {
        $General->updateAll('cmd_ligne' , 0 , 'cmdl__etat_masque' , 'cmdl__id' , $newLines);
    }
    //affichage de l'etat: 
    if (!empty($_POST['checkImage'])) 
    {
        $General->updateAll('cmd_ligne' ,1  , 'cmdl__image' , 'cmdl__id' , $newLines);
    }
    else 
    {
        $General->updateAll('cmd_ligne' , 0 , 'cmdl__image' , 'cmdl__id' , $newLines);
    }

    //extension de garanties : 
    foreach ($_POST['xtendP'] as $key => $value) 
    {
       if (!empty($value[0])) 
       {
           foreach ($_POST['xtendB'] as $mois => $promo) 
           {
               if (intval($mois) == intval($key)) 
               {
                  $Devis->insertGaranties( $newLines , $mois , floatval($value[0]) , floatval($promo[0]) );
                   
               }
           }
       }
    }
   
}

//modification de ligne : 
if (!empty($_POST['boolModif']) ) 
    {
        if (empty($_POST['etat'])) 
        {
            $_POST['etat'] = 'NC.';
        }
        if (empty($_POST['garantie'])) 
        {
            $_POST['garantie'] = '00';
        }
        if (empty($_POST['xtendP'])) 
        {
            $_POST['xtendP'] = [];
        }
        $General->updateAll('cmd_ligne' , $_POST['presta'] , 'cmdl__prestation' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , $_POST['fmm'] , 'cmdl__id__fmm' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , $_POST['designation'] , 'cmdl__designation' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , $_POST['etat'] , 'cmdl__etat' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , $_POST['garantie'] , 'cmdl__garantie_base' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , intval($_POST['quantite']) , 'cmdl__qte_cmd' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , floatval($_POST['promo']) , 'cmdl__prix_barre' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , floatval($_POST['prix']) , 'cmdl__puht' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , $_POST['commentaire'] , 'cmdl__note_client' , 'cmdl__id' , $_POST['boolModif']);
        $General->updateAll('cmd_ligne' , $_POST['interne'] , 'cmdl__note_interne' , 'cmdl__id' , $_POST['boolModif']);
        //affichage de l'état : 
        if(!empty($_POST['checkEtat'])) 
        {
            $General->updateAll('cmd_ligne' , 1 , 'cmdl__etat_masque' , 'cmdl__id' , $_POST['boolModif']);
        }
        else 
        {
            $General->updateAll('cmd_ligne' , 0 , 'cmdl__etat_masque' , 'cmdl__id' ,$_POST['boolModif']);
        }
        //affichage de l'etat: 
        if(!empty($_POST['checkImage'])) 
        {
            $General->updateAll('cmd_ligne' ,1  , 'cmdl__image' , 'cmdl__id' ,$_POST['boolModif']);
        }
        else 
        {
            $General->updateAll('cmd_ligne' , 0 , 'cmdl__image' , 'cmdl__id' , $_POST['boolModif']);
        }
            
        $Devis->deleteGarantie($_POST['boolModif']);

        $idDevis = $_POST['boolIdCmd'];
       

    
        //extension de garanties : 
        foreach ($_POST['xtendP'] as $key => $value) 
        {
           if (!empty($value[0])) 
           {
               foreach ($_POST['xtendB'] as $mois => $promo) 
               {
                   if (intval($mois) == intval($key)) 
                   {
                      $Devis->insertGaranties( $_POST['boolModif'] , $mois , floatval($value[0]) , floatval($promo[0]) );
                       
                   }
               }
           }
        }
       
    }




$devis = $Cmd->GetById($idDevis);
$devisLigne = $Cmd->devisLigne($idDevis);
$totaux  = Pdfunctions::totalFacturePRO($devis, $devisLigne);
$remiseRequest = $Devis->getRemise($idDevis);
$remiseTotal = 0.00 ;
foreach ($remiseRequest as $remise) 
{
    $remiseTotal += floatval($remise->cmdl__prix_barre);
    
}
$remiseTotal = $remiseTotal - $totaux[0];
    if ($remiseTotal <= 0) 
    {
        $remiseTotal = 0.00 ;
    }
//formatte les totaux pour affichage : 
$remiseTotal =  number_format($remiseTotal , 2,',', ' ');
$totaux[0] = number_format($totaux[0] , 2,',', ' ');
$totaux[1] = number_format($totaux[1] , 2,',', ' ');
$totaux[2] = number_format($totaux[2] , 2,',', ' ');
$totaux[3] = number_format($totaux[3] , 2,',', ' ');



foreach ($devisLigne as $ligne) 
{
   $garanties = $Devis->selectGaranties($ligne->devl__id);
  
   $ligne->xtend = $garanties;
}

$articleTypeList = $Article->getModels();
$prestaList = $Keywords->getPresta();
$garanties = $Keywords->getGaranties();
if (!empty($modif)) 
{
    foreach ($garanties as $garantie) 
    {
        
        foreach ($modif->xtend as $xtend) 
        {
            if ($garantie->kw__value== $xtend->cmdg__type) 
            {
               $garantie->xtend = $xtend;
               
            }
        }
    }
}

$etatList = $Keywords->getEtat();


// Donnée transmise au template : 
echo $twig->render('NligneDevisV2.twig',[
    'user'=>$_SESSION['user'],
    'devis'=> $devis,
    'prestaList' => $prestaList , 
    'articleList' => $articleTypeList,
    'garanties' => $garanties,
    'etatList'=> $etatList,
    'devisLignes' => $devisLigne,
    'modif' => $modif,
    'duplicate' => $duplicate,
    'totaux' => $totaux ,
    'remise_total'=> $remiseTotal
    
 ]);


?>