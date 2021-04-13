<?php
//injection des objets et repertoire nécéssaires : 
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Devis =new App\Tables\Devis($Database);
$General = new App\Tables\General($Database);
$tableau_garantie = json_decode($_POST['tableau_garantie']);
$tableau_commantaire = json_decode($_POST['tableau_commentaires']);

switch ($_POST['nature_demande']) 
{
        case 'VLDDEVIS':

                //met à jour les garanties : 
                foreach ($tableau_garantie as $value) 
                {
                       $liste_garantie = $Devis->selectGaranties($value->id);
                       foreach ($liste_garantie as $garantie) 
                       {
                             if (intval($garantie->cmdg__type) == intval($value->valeur)) 
                             {
                                        
                                        $General->updateAll('cmd_ligne', $value->valeur , 'cmdl__garantie_option' , 'cmdl__id' , $value->id);
                                      
                                        $General->updateAll('cmd_ligne', $garantie->cmdg__prix , 'cmdl__garantie_puht' , 'cmdl__id' ,$value->id);
                             }  
                       }
                }

                //met à jour les commentaires / quantites  : 
                foreach($tableau_commantaire as $value) 
                {
                        //différence commentaire global et commentaire de ligne : 
                        if ($value->id == 'commentaireInterneValid') 
                        {
                                $General->updateAll('cmd', $value->valeur , 'cmd__note_interne' , 'cmd__id' ,$_POST['id_devis']);
                        }
                        else 
                        {
                                $General->updateAll('cmd_ligne', $value->valeur , 'cmdl__note_interne' , 'cmdl__id' ,$value->id); 
                                $ligne = $Devis->selecOneLine($value->id);   
                                $General->updateAll('cmd_ligne', $ligne->cmdl__qte_cmd , 'cmdl__qte_cmd' , 'cmdl__id' ,$value->id);
                        }         
                       
                }
                $General->updateAll('cmd', $_POST['code_commande_client'] , 'cmd__code_cmd_client' , 'cmd__id' ,$_POST['id_devis']);
                $General->updateAll('cmd', 'ATN' , 'cmd__etat' , 'cmd__id' ,$_POST['id_devis']);
                $_SESSION['creaFiche'] = $_POST['id_devis'];
                header('location: printFt');
                break;
        
        case 'FTCADMIN':
                break;
        
        case 'AVRADMIN':
                break;
}


