<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
use App\Methods\Pdfunctions;
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']->id_utilisateur)) 
 {
    header('location: login');
 }
 if ($_SESSION['user']->user__facture_acces < 10 ) 
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
 $Stats = new App\Tables\Stats($Database);
 $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
 $_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
 $articleTypeList = $Article->getModels();
 $prestaList = $Keyword->getPresta();
 $keywordList = $Keyword->get2_icon();
 $tvaList = $Keyword->getAllFromParam('tva');
 $marqueur = $Keyword->getExport();
 $marqueur = intval($marqueur->kw__lib);
 $maxFact = $Cmd->getMaxFacture();
 $minFact = $Cmd->getMinFacture();

 $alert = false;
 
 $devisList = $Cmd->getFromStatusAll('VLD');
 //formatte la date pour l'utilisateur:
    foreach ($devisList as $devis) 
    {
      $devisDate = date_create($devis->cmd__date_fact);
      $date = date_format($devisDate, 'd/m/Y');
      $devis->cmd__date_fact = $date;
    }

//si un export à été envoyé :
if (!empty($_POST['exportStart']) && !empty($_POST['exportEnd'])) 
{
    $exportArray = $Cmd->ligneXport($_POST['exportStart'],$_POST['exportEnd']);
    $getAllLines = $Cmd->exportFinal($exportArray);
    $Keyword->majMarqueur($_POST['exportEnd']);
    
    $txt = '';
    
    foreach ($getAllLines as $key => $value) 
    {
        if (!empty($value)) 
        {
             $commande = $Cmd->GetById($value[0]->cmdl__cmd__id);
        $devisDate = date_create($commande->cmd__date_fact);
        $interval = new DateInterval('P30D');
        $date = date_format($devisDate, 'd/m/Y');
        $commande->cmd__date_fact = $date;
        $echeance = $devisDate->add($interval);
        $echeance = date_format($echeance, 'd/m/Y');
        
        $lignes = $Cmd->devisLigne($value[0]->cmdl__cmd__id);
        $total = Pdfunctions::totalFacture($commande,$value);
        $libelle = 'F';
        
        if ($commande->cmd__modele_facture == 'AVR') 
        {
            $libelle = 'A';
        }
        $libelle = $commande->devis__id.' '.$commande->client__societe;
        $libelle = substr($libelle, 0 , 28);
        //determine les première ligne par rapport au taux de tva: 
        if (intval($commande->tva_value)  == 10 ) 
        {
            $txt.=  'VE;' . $commande->cmd__id_facture .';'.$commande->cmd__date_fact.';'.$echeance.';'.$libelle.';411'.$commande->client__id.';'. number_format($total[3] , 2 , ',', ' ').'; ;'. $libelle.'
VE;'.$commande->cmd__id_facture.';'.$commande->cmd__date_fact.'; ;T.V.A;44571200; ;'.number_format($total[2] , 2 ,',', ' ').'
' ;
        }
        elseif (intval($commande->tva_value)  == 20) 
        {
            $txt.=  'VE;' . $commande->cmd__id_facture .';'.$commande->cmd__date_fact.';'.$echeance.';'.$libelle.';411'.$commande->client__id.';'.number_format($total[3] , 2 ,  ',' ,  ' ').'; ;'. $libelle.'
VE;'.$commande->cmd__id_facture.';'.$commande->cmd__date_fact.'; ;T.V.A;44571101; ;'.number_format($total[2] , 2 ,',', ' ').'
' ;
        }
        else 
        {
            $txt.=  'VE;' . $commande->cmd__id_facture .';'.$commande->cmd__date_fact.';'.$echeance.';'.$libelle.';411'.$commande->client__id.';'.number_format($total[3] , 2 , ',' , ' ').'; ;'. $libelle.'
';
        }
       
        foreach ($value as $test) 
        {    
            if (floatval($test->devl_puht) != 00.00 ) 
            {
                $compta = $Cmd->getCompta($test , $commande);
               
             if (!empty($compta[0])) 
             {
                $txt.= 'VE;' . $commande->cmd__id_facture .';'.$commande->cmd__date_fact.'; ;'.$test->devl__type .' '.$test->cmdl__qte_fact.' '.$test->famille.';'.$compta[0]->cpt__compte_quadra.'; ;'.number_format($test->devl_puht * $test->cmdl__qte_fact , 2 , ',' ,' ').'
';
             }  
         
            if (!empty($compta[1])) 
            {
               
                $txt.= 'VE;' . $commande->cmd__id_facture .';'.$commande->cmd__date_fact.'; ;EXT'.$test->cmdl__qte_fact.' '.$test->famille.';'.$compta[1]->cpt__compte_quadra.'; ;'.number_format($test->cmdl__garantie_puht * $test->cmdl__qte_fact, 2, ',' , ' ').'
';
            }
            }    
        }
    }
    $file = fopen("O:\intranet\Compta/export_".$_POST['exportStart']."_".$_POST['exportEnd'].".csv", "w");
    fwrite($file , $txt);
    fclose($file);
        }
}
// Donnée transmise au template : 
echo $twig->render('export.twig',
[
'user'=>$user,
'devisList'=>$devisList,
'marqueur'=>$marqueur,
'tvaList' => $tvaList,
'maxFact'=> $maxFact,
'minFact' => $minFact,
'alert' => $alert
]);