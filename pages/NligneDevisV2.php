
<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
use App\Methods\Pdfunctions;
session_start();

//URL bloqué si pas de connexion :
 if (empty($_SESSION['user']->id_utilisateur)) {
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
 $Stats = new App\Tables\Stats($Database);
 $Stocks = new App\Tables\Stock($Database);
 
 $Database->DbConnect();
 $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
$_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
 
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
            //creation de Devis:
            $date = date("Y-m-d H:i:s");
            //livraison
            if (!empty($_POST['clientLivraison']) )
            { $livraison = $_POST['clientLivraison'];}
            else { $livraison =  $_POST['clientSelect'];}
            //contacts
            if ($_POST['contactSelect'] != 'Aucun' )
            { $_POST['contactSelect'] = $_POST['contactSelect'];}
            else { $_POST['contactSelect'] =  null;}
            //contacts livraison
            if ($_POST['contactLivraison'] != 'Aucun' )
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
if (!empty($_POST['modifReturn']) ) 
{

    switch ($_POST['clientSelect']) 
    {
        case 'Aucun':
            $_SESSION['alertV2'] = 'Modif';
            header('location: DevisV2'); 
            break;
        
        default:
        //creation de Devis:
        $date = date("Y-m-d H:i:s");
        //livraison
        if (!empty($_POST['clientLivraison']))
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

    
} 

//si un retour pour tva intracom a été signalé : 
$alert_intracom = false;
if (!empty($_SESSION['alert_intracom'])) 
{
    $idDevis = $_SESSION['alert_intracom'];
    $_SESSION['alert_intracom'] = '';
    $alert_intracom = true;
}

if (!empty($_POST['return_from_validation'])) 
{
    $idDevis = $_POST['return_from_validation'];
}



//descend une ligne : 
if (!empty($_POST['down'])) 
{
    $updateOrdre = $Devis->upanDonwn('down',$_POST['downId'], $_POST['downLigne'] , $_POST['down'][0]);
    $idDevis = $_POST['downId'];
    $ligne_temp = $Cmd->devisLigne($_POST['downId']);
    $Cmd->update_ordre_sous_ref($ligne_temp);
}

//monte une ligne : 
if (!empty($_POST['up'])) 
{
    $updateOrdre = $Devis->upanDonwn('up',$_POST['upId'], $_POST['upLigne'] , $_POST['up'][0]);
    $idDevis = $_POST['upId'];
    $ligne_temp = $Cmd->devisLigne($_POST['upId']);
    $Cmd->update_ordre_sous_ref($ligne_temp);
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

//ajout de port automatique : 
if (!empty($_POST['cmd__id_port'])) 
{
    if (!empty($_POST['value_port'])) 
    {
       $port = $Article->get_fmm_by_id($_POST['value_port']); // recupere les * infos sur FMM par son ID

        $newLines = $Devis->insertLine(
            $_POST['cmd__id_port'],
            'PRT',
            $_POST['value_port'],
            $port->afmm__design_com,
            'NC.',
            null,
            '1',
            null,
            $port->afmm__prix_conseil,
            null,
            null
        );
    }
    $idDevis = $_POST['cmd__id_port'];
}
//active/deesatcive une ligne :
if (!empty($_POST['cmd__id_activate'])) 
{
    if (!empty($_POST['value_activate'])) 
    {
        $ligne =$Cmd->devisLigneUnit($_POST['value_activate']);
        if ($ligne->cmdl__actif == 0 ) 
        {
            $General->updateAll('cmd_ligne', 1, 'cmdl__actif', 'cmdl__id', $_POST['value_activate']);
        }
        else
        {
            $General->updateAll('cmd_ligne', 0 , 'cmdl__actif', 'cmdl__id', $_POST['value_activate']);
        }
    }
    $idDevis = $_POST['cmd__id_activate'];
}


//creation de sous référence :
if (!empty($_POST['input_id_ref']) && !empty($_POST['select_sous_ref']) && !empty($_POST['designation_sous_ref'])) 
{
   $mother_line = $Devis->get_line_by_id($_POST['input_id_ref']); 
  
   $daugther_line = $Devis->create_daugther_line($mother_line,$_POST['select_sous_ref'],$_POST['designation_sous_ref'],$_POST['quantite_sous_ref'],$_POST['com_sous_ref']);
   if (!empty($_POST['sous_ref_garantie'])) 
   {
        $General->updateAll('cmd_ligne' , 1 , 'cmdl__sous_garantie' , 'cmdl__id' , $daugther_line);
   }

   if ($_POST['pn_select-sr']!= '0') 
   {
        $General->updateAll('cmd_ligne', $_POST['pn_select-sr'], 'cmdl__pn', 'cmdl__id', $daugther_line);
        //change l'idd fmm a piece detachée si la famille du pn est piece detachée ou accésoire :
        $verify = $Stocks->check_famille_pn($_POST['pn_select-sr']);
        if ($verify != false)
            $General->updateAll('cmd_ligne', 100, 'cmdl__id__fmm', 'cmdl__id', $daugther_line);
   }
   else 
   {
        $General->updateAll('cmd_ligne', null, 'cmdl__pn', 'cmdl__id', $daugther_line);
   }
  
   $idDevis = $mother_line->cmdl__cmd__id;
  
}

//modification de sous référence :
if (!empty($_POST['input_modif_sous_ref']) && !empty($_POST['input_modif_sous_ref_cmd']) && !empty($_POST['select_modif_sous_ref'])) 
{
    $General->updateAll('cmd_ligne', $_POST['select_modif_sous_ref'] , 'cmdl__id__fmm', 'cmdl__id', $_POST['input_modif_sous_ref']);
    $General->updateAll('cmd_ligne', $_POST['quantite_modif_sous_ref'], 'cmdl__qte_cmd', 'cmdl__id', $_POST['input_modif_sous_ref']);
    $General->updateAll('cmd_ligne', $_POST['designation_modif_sous_ref'], 'cmdl__designation', 'cmdl__id', $_POST['input_modif_sous_ref']);
    $General->updateAll('cmd_ligne', $_POST['com_modif_sous_ref'], 'cmdl__note_interne', 'cmdl__id', $_POST['input_modif_sous_ref']);
    if (!empty($_POST['modif_sous_ref_garantie'])) 
    {
        $General->updateAll('cmd_ligne', 1, 'cmdl__sous_garantie', 'cmdl__id', $_POST['input_modif_sous_ref']);
    }
    else 
    {
        $General->updateAll('cmd_ligne', 0 , 'cmdl__sous_garantie', 'cmdl__id', $_POST['input_modif_sous_ref']);
    }

    if ($_POST['pn_select-sr-m']!= '0') 
    {
        $General->updateAll('cmd_ligne', $_POST['pn_select-sr-m'], 'cmdl__pn', 'cmdl__id', $_POST['input_modif_sous_ref']);
        //change l'idd fmm a piece detachée si la famille du pn est piece detachée ou accésoire :
        $verify = $Stocks->check_famille_pn($_POST['pn_select-sr-m']);
        if ($verify != false)
            $General->updateAll('cmd_ligne', 100, 'cmdl__id__fmm', 'cmdl__id', $_POST['input_modif_sous_ref']);
    }
    else 
    {
        $General->updateAll('cmd_ligne', null, 'cmdl__pn', 'cmdl__id', $_POST['input_modif_sous_ref']);
    }
    $idDevis = $_POST['input_modif_sous_ref_cmd'];
    
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
    
    //met a jour la designation automatique :  (Frank R ou autre admin)
    if (!empty($_POST['checkDesignation'])) 
    {
        $General->updateAll('art_fmm' , $_POST['designation'] , 'afmm__design_com' , 'afmm__id' , $_POST['fmm']);
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
    //gerer l' activité de la ligne : 
    if (!empty($_POST['checkactif'])) 
    {
        $General->updateAll('cmd_ligne' , 0  , 'cmdl__actif' , 'cmdl__id' , $newLines);
    }
    else 
    {
        $General->updateAll('cmd_ligne' ,1  , 'cmdl__actif' , 'cmdl__id' , $newLines);
    }
    //gere le pn : 
    if ($_POST['pn_select'] != '0') 
    {
        $General->updateAll('cmd_ligne', $_POST['pn_select'], 'cmdl__pn', 'cmdl__id', $newLines);

        //change l'idd fmm a piece detachée si la famille du pn est piece detachée ou accésoire :
        $verify = $Stocks->check_famille_pn($_POST['pn_select']);
        if ($verify != false ) 
            $General->updateAll('cmd_ligne',100, 'cmdl__id__fmm', 'cmdl__id', $newLines);
        
    } 
    else 
    {
        $General->updateAll('cmd_ligne', null , 'cmdl__pn', 'cmdl__id', $newLines);
    }

    //gere limage du pn  : 
    if (!empty($_POST['checkImagePn'])) 
    {
        $General->updateAll('cmd_ligne' , 2   , 'cmdl__image' , 'cmdl__id' , $newLines);
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
         //met a jour la designation automatique :  (Frank R ou autre admin)
        if (!empty($_POST['checkDesignation'])) 
        {
            $General->updateAll('art_fmm' , $_POST['designation'] , 'afmm__design_com' , 'afmm__id' , $_POST['fmm']);
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


        //gere le pn : 
        if ($_POST['pn_select'] != '0' ) 
        {
            $General->updateAll('cmd_ligne', $_POST['pn_select'] , 'cmdl__pn', 'cmdl__id', $_POST['boolModif']);
            //change l'idd fmm a piece detachée si la famille du pn est piece detachée ou accésoire :
            $verify = $Stocks->check_famille_pn($_POST['pn_select']);
            if ($verify != false)
                $General->updateAll('cmd_ligne', 100, 'cmdl__id__fmm', 'cmdl__id', $_POST['boolModif']);
        }
        else 
        {
            $General->updateAll('cmd_ligne', null , 'cmdl__pn', 'cmdl__id', $_POST['boolModif']);
        }

        //gere limage du pn  : 
        if (!empty($_POST['checkImagePn'])) 
        {
            $General->updateAll('cmd_ligne' , 2   , 'cmdl__image' , 'cmdl__id' , $_POST['boolModif']);
        }

        //affichage de l'état : 
        if(!empty($_POST['checkEtat'])) 
        {
            $General->updateAll('cmd_ligne' , 1 , 'cmdl__etat_masque' , 'cmdl__id' , $_POST['boolModif']);
        }
        else 
        {
            $General->updateAll('cmd_ligne' , 0 , 'cmdl__etat_masque' , 'cmdl__id' ,$_POST['boolModif']);
        }
         //gerer l' activité de la ligne : 
        if (!empty($_POST['checkactif'])) 
        {
            $General->updateAll('cmd_ligne' , 0  , 'cmdl__actif' , 'cmdl__id' ,$_POST['boolModif']);
        }
        else 
        {
            $General->updateAll('cmd_ligne' ,1  , 'cmdl__actif' , 'cmdl__id' , $_POST['boolModif']);
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
$lignes_totaux = $Cmd->devisLigne_actif($idDevis);
$devisLigne = $Cmd->devisLigne_sous_ref($idDevis);
$totaux  = Pdfunctions::totalFacturePRO($devis, $lignes_totaux);
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
    'remise_total'=> $remiseTotal,
    'alert_intracom'=> $alert_intracom
    
 ]);


?>