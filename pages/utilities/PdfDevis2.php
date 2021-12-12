<?php
//injection des objets et repertoire nécéssaires : 
require "./vendor/autoload.php";
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
use App\Methods\Devis_functions;
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Contact = new \App\Tables\Contact($Database);
$Client = new \App\Tables\Client($Database);
$General = new App\Tables\General($Database);
$Keyword = new \App\Tables\Keyword($Database);
$garanties = $Keyword->getGaranties();


if (empty($_SESSION['user'])) 
{
    header('location: login');
}

if (!empty($_POST['idDevis'])) 
{
    //control de la tva intracom : 
    $devis_controle = $Cmd->GetById($_POST['idDevis']);

    //control de la pretation du port :
    $lignes = $Cmd->devisLigne($_POST['idDevis']);

    // foreach ($lignes as $ligne) 
    // {
    //     if ( $ligne->devl__type != 'PRT' ) 
    //     {
    //         $General->updateAll('cmd_ligne', 'PRT' , 'cmdl__prestation ', 'cmdl__id', $ligne->devl__id );
    //     }
    // }

    $client_controle = $Client->getOne($devis_controle->client__id);

    if ($client_controle->client__tva == 1 &&  empty($client_controle->client__tva_intracom)) 
    {
        $_SESSION['alert_intracom'] = $_POST['idDevis'];
        header('location: ligneDevisV2');
    }
    else
    {
        //mise a jour de la date du devis : 
        $date = date("Y-m-d H:i:s");
        $General->updateAll('cmd' , $date , 'cmd__date_devis' , 'cmd__id' , $_POST['idDevis']);

        //mise a jour de la date de client : 
        $General->updateAll('client' , $date , 'client__dt_last_modif' , 'client__id' , $devis_controle->client__id);
        //mise a jour de la date de client livraison : 
        $General->updateAll('client' , $date , 'client__dt_last_modif' , 'client__id' , $devis_controle->devis__id_client_livraison);

        //mide a jour de l'id utilisateur : 
        $user = $_SESSION['user']->id_utilisateur;
        $General->updateAll('cmd' , $user , 'cmd__user__id_devis' , 'cmd__id' , $_POST['idDevis']);

        //mise à jour du status :
        $General->updateAll('cmd' , 'ATN' , 'cmd__etat' , 'cmd__id' , $_POST['idDevis']);

        //mise a jour pour mode classique ou remise : 
        if (!empty($_POST['checkremise'])) 
        {
            $General->updateAll('cmd' , 1 , 'cmd__mode_remise' , 'cmd__id' , $_POST['idDevis']);
        }
        else
        {
            $General->updateAll('cmd' , 0 , 'cmd__mode_remise' , 'cmd__id' , $_POST['idDevis']);
        }
        //mise a jour pour le report des extensions
        if (!empty($_POST['checkExtend'])) 
        {
            $General->updateAll('cmd' , 1 , 'cmd__report_xtend' , 'cmd__id' , $_POST['idDevis']);
        }
        else
        {
            $General->updateAll('cmd' , 0 , 'cmd__report_xtend' , 'cmd__id' , $_POST['idDevis']);
        }
        // si absence de total :
        if(!empty($_POST['check_tva'])) 
        { 
            $General->updateAll('cmd' , 'TVT' , 'cmd__modele_devis' , 'cmd__id' , $_POST['idDevis']);
        }
        else
        {
            $General->updateAll('cmd' , 'STT' , 'cmd__modele_devis' , 'cmd__id' , $_POST['idDevis']);
        }

        //si absence de total :
        if (!empty($_POST['check_standard'])) {
            $General->updateAll('cmd', 'STS', 'cmd__modele_devis', 'cmd__id', $_POST['idDevis']);
        }

        //si absence de total :
        if (!empty($_POST['check_total'])) 
        {
            $General->updateAll('cmd' , 'STX' , 'cmd__modele_devis' , 'cmd__id' , $_POST['idDevis']);
        }

        //remet dans l'ordre des sous ref et les refs : 
        $tableau_ligne = $Cmd->devisLigne($_POST['idDevis']);
        $Cmd->update_ordre_sous_ref($tableau_ligne);
        header('location: mesDevis');
    }

    
}
