<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
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
 
//par défault le champ de recherche est égal a null:
 $champRecherche = null;


// determine la préférence de l'utilisateur avec les 2 checkboxs :
if (empty($_SESSION['searchFT'])) 
{
   $_SESSION['searchFT'] = 'ALL';
}


if (!empty($_POST['ftCheck']) && !empty($_POST['blCheck']) ) 
 {
   $_SESSION['searchFT'] = 'ALL';
 }
 elseif (!empty($_POST['ftCheck']) && empty($_POST['blCheck'])) 
 {
   $_SESSION['searchFT'] = 'FT';
 }
 elseif (empty($_POST['ftCheck']) && !empty($_POST['blCheck'])) 
 {
   $_SESSION['searchFT'] = 'BL';
 }

$sessionUser = $_SESSION['searchFT'];



// variable qui determine la liste des devis à afficher:
if (!empty($_POST['recherche-fiche'])) 
{
   switch ($_POST['recherche-fiche']) 
   {
         case 'search':
               if ($_POST['rechercheF'] != "") 
               {
                  $devisList = $Cmd->magicRequestFunnyBunny($_POST['rechercheF'] , $sessionUser);
                  $champRecherche = $_POST['rechercheF'];
         break;
               }
               else 
               {
               $devisList = $Cmd->magicRequestFunnyBunny('' , $sessionUser);
               $champRecherche = $_POST['rechercheF'];
         break;
               }

         case 'id-fiche':
               $devisList = [];
               $devisSeul = $Cmd->GetById(intval($_POST['rechercheF']));
               $champRecherche = $_POST['rechercheF'];
               array_push($devisList, $devisSeul);
         break;
                     
         default:
            $devisList = $Cmd->magicRequestFunnyBunny('' , $sessionUser);
         break;
   }
                  
   } else $devisList = $Cmd->magicRequestFunnyBunny('' , $sessionUser);
    
 



//si une modification ou une creation de date d'envoi à ete indiquée:
if(!empty($_POST['estimDate']))
{
   $Cmd->updateDate('cmd__date_envoi', $_POST['estimDate'] , intval($_POST['dateId']));
   header('location: ficheTravail');
}

//si une modification de commentaire principal a été effectué : 
if(!empty($_POST['hiddenCommentaire']) && !empty($_POST['comInterne']))
{
   $Cmd->updateComInterne($_POST['comInterne'], intval($_POST['hiddenCommentaire']));
   header('location: ficheTravail');
}

//si une modification dans un commentaire de ligne à été effectué:
   
if (!empty($_POST['ligneID']) && !empty($_POST['ligneCom'])) 
{
  $Cmd->updateComInterneLigne($_POST['ligneCom'], intval($_POST['ligneID']));
  header('location: ficheTravail');
}
 
//nombre des fiches dans la liste 
 $NbDevis = count($devisList);

//formatte la date pour l'utilisateur:
 foreach ($devisList as $devis) 
 {
   $devisDate = date_create($devis->cmd__date_cmd);
   $date = date_format($devisDate, 'd/m/Y');
   $devis->devis__date_crea = $date;
   if ($devis->cmd__date_envoi) 
   {
      $envoiDate = date_create($devis->cmd__date_envoi);
      $envoiDate = date_format($envoiDate, 'd/m/Y');
      $devis->cmd__date_envoi = $envoiDate;
   }
  
 }
  
// Donnée transmise au template : 
echo $twig->render('ficheT.twig',
[
'user'=>$user,
'devisList'=>$devisList,
'NbDevis'=>$NbDevis,
'champRecherche'=>$champRecherche ,
'sessionUser' =>$sessionUser

]);