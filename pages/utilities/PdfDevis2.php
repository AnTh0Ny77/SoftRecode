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
    if (!empty($_POST['check_total'])) 
    {
        $General->updateAll('cmd' , 'STX' , 'cmd__modele_devis' , 'cmd__id' , $_POST['idDevis']);
    }
    
    header('location: mesDevis');
}
