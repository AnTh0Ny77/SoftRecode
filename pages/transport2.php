<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

 //URL bloqué si pas de connexion :
 if (empty($_SESSION['user']->id_utilisateur)) 
 {
    header('location: login');
 }
 if ($_SESSION['user']->user__saisie_acces < 10 ) 
 {
   header('location: noAccess');
 }


 //déclaration des instances nécéssaires :
 $user= $_SESSION['user'];
 $Database = new App\Database('devis');
 $Database->DbConnect();
 $Keyword = new App\Tables\Keyword($Database);
 $General = new App\Tables\General($Database);
 $Client = new App\Tables\Client($Database);
 $Contact = new \App\Tables\Contact($Database);
 $Cmd = new App\Tables\Cmd($Database);
 
 
 
//par défault le champ de recherche est égal a null:
 $champRecherche = null;
 $devisSeul = null;
 $alert = null;
 $devisLigne = null;
// variable qui determine la liste des devis à afficher:
if (!empty($_GET['cmd'])) {
    $_POST['rechercheF'] = $_GET['cmd'] ;
}
if (!empty($_POST['rechercheF'])) 
{          
           $devisSeul = $Cmd->GetById(intval($_POST['rechercheF']));
           $champRecherche = $_POST['rechercheF'];                        
} 

switch ($devisSeul) 
{
    case false:
        $alert = 'aucuns résultats';
        break;
    case null:
            $alert = 'saisir un numéro de commande';
            break;
    default:
            switch ($devisSeul->devis__etat) 
            {
                case 'ATN':
                    $alert = 'ATN';
                    break;
                case 'VLD':
                    $alert = 'VLD';
                    break;
                case 'CMD':
                    $alert = 'CMD';
                    break;
                case 'IMP':
                    $alert = 'IMP';
                    break;
                default:
                    $alert = 'DEF';
                    break;
            }
            $devisDate = date_create($devisSeul->cmd__date_cmd);
            $date = date_format($devisDate, 'd/m/Y');
            $devisSeul->devis__date_crea = $date;
            $devisSeul->DataLigne = json_encode($Cmd->devisLigne($devisSeul->devis__id));
            $devisLigne = $Cmd->devisLigne($devisSeul->devis__id);
        break;
}


//liste des transporteur :
$TransportListe = $Keyword->getTransporteur();

// Donnée transmise au template : 
echo $twig->render('transport2.twig',
[
'user'=>$user,
'devis'=>$devisSeul,
'champRecherche'=>$champRecherche,
'transporteurs'=>$TransportListe,
'alert' => $alert , 
'devisLigne' => $devisLigne,

]);