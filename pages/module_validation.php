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
//$tableau_commantaire = json_decode($_POST['tableau_commentaires']);

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
                // foreach($tableau_commantaire as $value) 
                // {
                //         //différence commentaire global et commentaire de ligne : 
                //         if ($value->id == 'commentaireInterneValid') 
                //         {
                //                 $General->updateAll('cmd', $value->valeur , 'cmd__note_interne' , 'cmd__id' ,$_POST['id_devis']);
                //         }
                //         else 
                //         {
                //                 $General->updateAll('cmd_ligne', $value->valeur , 'cmdl__note_interne' , 'cmdl__id' ,$value->id); 
                //                 $ligne = $Devis->selecOneLine($value->id);   
                //                 $General->updateAll('cmd_ligne', $ligne->cmdl__qte_cmd , 'cmdl__qte_cmd' , 'cmdl__id' ,$value->id);
                //         }         
                       
                // }
                $General->updateAll('cmd', $_POST['commentaire_interne_post'] , 'cmd__note_interne' , 'cmd__id' ,$_POST['id_devis']);
                $General->updateAll('cmd', $_POST['code_commande_client'] , 'cmd__code_cmd_client' , 'cmd__id' ,$_POST['id_devis']);
                $General->updateAll('cmd', 'ATN' , 'cmd__etat' , 'cmd__id' ,$_POST['id_devis']);
                $_SESSION['creaFiche'] = $_POST['id_devis'];
                header('location: printFt');
                break;
        
        case 'FTCADMIN':
                //met à jour les garanties : 
                foreach ($tableau_garantie as $value) {
                        $liste_garantie = $Devis->selectGaranties($value->id);
                        foreach ($liste_garantie as $garantie) {
                                if (intval($garantie->cmdg__type) == intval($value->valeur)) {

                                        $General->updateAll('cmd_ligne', $value->valeur, 'cmdl__garantie_option', 'cmdl__id', $value->id);

                                        $General->updateAll('cmd_ligne', $garantie->cmdg__prix, 'cmdl__garantie_puht', 'cmdl__id', $value->id);
                                }
                        }
                }

                //met à jour les commentaires / quantites  : 
                // foreach ($tableau_commantaire as $value) {
                //         //différence commentaire global et commentaire de ligne : 
                //         if ($value->id == 'commentaireInterneValid') {
                //                 $General->updateAll('cmd', $value->valeur, 'cmd__note_interne', 'cmd__id', $_POST['id_devis']);
                //         } else {
                //                 $General->updateAll('cmd_ligne', $value->valeur, 'cmdl__note_interne', 'cmdl__id', $value->id);
                //                 $ligne = $Devis->selecOneLine($value->id);
                //                 $General->updateAll('cmd_ligne', $ligne->cmdl__qte_cmd, 'cmdl__qte_cmd', 'cmdl__id', $value->id);
                //                 $General->updateAll('cmd_ligne', $ligne->cmdl__qte_cmd, 'cmdl__qte_livr', 'cmdl__id', $value->id);
                //                 $General->updateAll('cmd_ligne', $ligne->cmdl__qte_cmd, 'cmdl__qte_fact', 'cmdl__id', $value->id);
                //         }
                // }
                //recupère le tableau de ligne à jour : 
                $commandLignes = $Cmd->devisLigne($_POST['id_devis']);

                //met a jour les extensions de garanties des lignes fillles : 
                foreach ($commandLignes as $ligne) {
                        //je met a jour les ligne filles qui ont le droit de bénéficier de l'extension de garantie de la mère : 
                        if (!empty($ligne->cmdl__garantie_option)) {
                                $update = $Cmd->update_filles_extensions($ligne);
                        }
                }
                //met a jour les ordres : 
                $Cmd->update_ordre_sous_ref($commandLignes);
                //met à jour les dates : 
                $date = date("Y-m-d H:i:s");
                $General->updateAll('cmd', $date, 'cmd__date_devis', 'cmd__id',$_POST['id_devis']);
                $General->updateAll('cmd', $date, 'cmd__date_cmd', 'cmd__id',$_POST['id_devis']);
                $General->updateAll('cmd', $date, 'cmd__date_envoi', 'cmd__id',$_POST['id_devis']);
                //code commande 
                $General->updateAll('cmd', $_POST['commentaire_interne_post'] , 'cmd__note_interne' , 'cmd__id' ,$_POST['id_devis']);
                $General->updateAll('cmd', $_POST['code_commande_client'], 'cmd__code_cmd_client', 'cmd__id', $_POST['id_devis']);
                $_SESSION['factureEtoile'] = $_POST['id_devis'];
                header('location: printFTC');
                break;
        
        case 'AVRADMIN':
                //met à jour les garanties : 
                foreach ($tableau_garantie as $value) {
                        $liste_garantie = $Devis->selectGaranties($value->id);
                        foreach ($liste_garantie as $garantie) {
                                if (intval($garantie->cmdg__type) == intval($value->valeur)) {

                                        $General->updateAll('cmd_ligne', $value->valeur, 'cmdl__garantie_option', 'cmdl__id', $value->id);

                                        $General->updateAll('cmd_ligne', $garantie->cmdg__prix, 'cmdl__garantie_puht', 'cmdl__id', $value->id);
                                }
                        }
                }

                //met à jour les commentaires / quantites  : 
                // foreach ($tableau_commantaire as $value) {
                //         //différence commentaire global et commentaire de ligne : 
                //         if ($value->id == 'commentaireInterneValid') {
                //                 $General->updateAll('cmd', $value->valeur, 'cmd__note_interne', 'cmd__id', $_POST['id_devis']);
                //         } else {
                               
                //                 $General->updateAll('cmd_ligne', $value->valeur, 'cmdl__note_interne', 'cmdl__id', $value->id);
                //                 $ligne = $Devis->selecOneLine($value->id);
                               
                //                 $General->updateAll('cmd_ligne', $ligne->cmdl__qte_cmd, 'cmdl__qte_cmd', 'cmdl__id', $value->id);
                //         }
                // }
                //recupère le tableau de ligne à jour : 
                $commandLignes = $Cmd->devisLigne($_POST['id_devis']);

                //met a jour les extensions de garanties des lignes fillles : 
                foreach ($commandLignes as $ligne) {
                        //je met a jour les ligne filles qui ont le droit de bénéficier de l'extension de garantie de la mère : 
                        if (!empty($ligne->cmdl__garantie_option)) {
                                $update = $Cmd->update_filles_extensions($ligne);
                        }
                }
                //met a jour les ordres : 
                $Cmd->update_ordre_sous_ref($commandLignes);
                //met à jour les dates : 
                $date = date("Y-m-d H:i:s");
                $General->updateAll('cmd', $date, 'cmd__date_devis', 'cmd__id',$_POST['id_devis']);
                $General->updateAll('cmd', $date, 'cmd__date_cmd', 'cmd__id',$_POST['id_devis']);
                $General->updateAll('cmd', $date, 'cmd__date_envoi', 'cmd__id',$_POST['id_devis']);
                //code commande 
                $General->updateAll('cmd', $_POST['commentaire_interne_post'] , 'cmd__note_interne' , 'cmd__id' ,$_POST['id_devis']);
                $General->updateAll('cmd', $_POST['code_commande_client'], 'cmd__code_cmd_client', 'cmd__id', $_POST['id_devis']);
                $_SESSION['AvoirValide'] = $_POST['id_devis'];
                header('location: PRINTADMINAVOIR');
                break;
}


