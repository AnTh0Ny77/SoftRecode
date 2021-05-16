<?php
require "./vendor/autoload.php";

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Command = new \App\Tables\Cmd($Database);
$Client = new \App\Tables\Client($Database);
$User = new App\Tables\User($Database);
$Global = new App\Tables\General($Database);
$Contact = new App\Tables\Contact($Database);
//controle de la connexion : 
if (empty($_SESSION['user'])) {
    header('location: login');
}
//si une variable de session de creation de fiche est presente: (reliquat) je recupere la variable de session : 
if (!empty($_SESSION['creaFiche'])) {
    $command = $Command->getById(intval($_SESSION['creaFiche']));
    $_POST['devisCommande'] = $command->devis__id;
    $_SESSION['creaFiche'] = '';
}
//si une creation de fiche de garantie a eu lieu je recupere la variable de session : 
if (!empty($_SESSION['garanFiche'])) {
    $command = $Command->getById(intval($_SESSION['garanFiche']));
    $_POST['devisCommande'] = $command->devis__id;
    $_SESSION['garanFiche'] = '';
}
// si une validation de devis a été effectuée : 
if (!empty($_POST['devisCommande'])) {
    //efface les lignes filles des lignes inactives :
    $delete_daugther_lines_with_inactif_mum = $Command->delete_ligne_inactif_filles($_POST['devisCommande']);
    //efface les lignes inactives : 
    $deleteLines = $Command->delete_ligne_inactif($_POST['devisCommande']);
    $date = date("Y-m-d H:i:s");
    //mise a jour des dates , satus et validateur de cmd : 
    $Command->updateStatus('CMD', $_POST['devisCommande']);
    $Command->updateDate('cmd__date_cmd', $date, $_POST['devisCommande']);
    $Command->updateAuthor('cmd__user__id_cmd', $_SESSION['user']->id_utilisateur, $_POST['devisCommande']);
    //recupere le json contenant les lignes avec les extensions pré-choisies: 
    // if (!empty($_POST['arrayLigneDeCommande'])) {
    //     $validLignes = json_decode($_POST['arrayLigneDeCommande']);



    //     //je met a jour les extension de garanties choisies ainsi que les commentaires et quantités (meme si ça n'a aucun sens) :  
    //     foreach ($validLignes as $lignes) {

    //         if (!empty($lignes->devl__prix_barre[0])) {
    //             $Command->updateGarantie(
    //                 $lignes->devl__prix_barre[0],
    //                 $lignes->devl__prix_barre[1],
    //                 $lignes->devl__note_interne,
    //                 $lignes->devl_quantite,
    //                 $lignes->cmdl__cmd__id,
    //                 $lignes->devl__ordre
    //             );
    //         } else {
    //             $Global->updateAll('cmd_ligne', $lignes->devl__note_interne, 'cmdl__note_interne', 'cmdl__id', $lignes->devl__id);
    //             $Global->updateAll('cmd_ligne', $lignes->devl_quantite, 'cmdl__qte_cmd', 'cmdl__id', $lignes->devl__id);
    //         }
    //     }
    // }
    //si un code commande à été mis a jour durant la validation : 
    // if (!empty($_POST['code_cmd'])) {
    //     $Global->updateAll('cmd', $_POST['code_cmd'], 'cmd__code_cmd_client', 'cmd__id', $_POST['devisCommande']);
    // }
    //si le commentaire interne global a été mis a jour pendant la creation de commande : 
    // if (!empty($_POST['ComInterCommande'])) {
    //     $Global->updateAll('cmd', $_POST['ComInterCommande'], 'cmd__note_interne', 'cmd__id', $_POST['devisCommande']);
    // }
    //contient l'id du devis pour l'imprssion de la fiche de travail : client2.js
    $print_request = $_POST['devisCommande'];
}
//recupère la commande mise a jour :
$command = $Command->getById(intval($_POST['devisCommande']));
//si la date de devis est vide (reliquat ou garantie): je met a jour en me servant de la date commande : 
if (empty($command->devis__date_crea)) {
    $date = date("Y-m-d H:i:s");
    $Global->updateAll('cmd', $date, 'cmd__date_devis', 'cmd__id', $command->devis__id);
}

//recupère le tableau de ligne à jour : 
$commandLignes = $Command->devisLigne($_POST['devisCommande']);

//met a jour les extensions de garanties des lignes fillles : 
foreach ($commandLignes as $ligne) {
    //je met a jour les ligne filles qui ont le droit de bénéficier de l'extension de garantie de la mère : 
    if (!empty($ligne->cmdl__garantie_option)) {
        $update = $Command->update_filles_extensions($ligne);
    }
}
//met a jour les ordres : 
$Command->update_ordre_sous_ref($commandLignes);
//recupère avec le bon ordre : 
$commandLignes = $Command->devisLigne($_POST['devisCommande']);
//je recupère le client , user et validateur du  pdf : 
$clientView = $Client->getOne($command->client__id);
$user = $User->getByID($clientView->client__id_vendeur);
$userCMD = $User->getByID($command->cmd__user__id_cmd);
//si une societe de livraison existe je la recupère : 
$societeLivraison = false;
if ($command->devis__id_client_livraison) {
    $societeLivraison = $Client->getOne($command->devis__id_client_livraison);
}
//je recupère ma commande mise a jour : 
$command = $Command->getById(intval($_POST['devisCommande']));
//variable pour la gloire ? je ne l'enlève pas pour l'instant :    
$dateTemp = new DateTime($command->cmd__date_cmd);
//cree une variable pour la date de commande du devis
$date_time = new DateTime($command->cmd__date_cmd);
//formate la date pour l'utilisateur:
$formated_date = $date_time->format('d/m/Y H:i');
//debut de la génération du contenu html : 
ob_start();
?>
<style type="text/css">
    strong {
        color: #000;
    }

    h3 {
        color: #666666;
    }

    h2 {
        color: #3b3b3b;
    }

    table {
        font-size: 13;
        font-style: normal;
        font-variant: normal;
        border-collapse: separate;
        border-spacing: 0 15px;
    }
</style>
<page backtop="10mm" backleft="5mm" backright="5mm" backbottom='15%' footer="page">
    <page_footer>
        <div style=" width: 100%; position: absolute; bottom:1px">
            <?php
            //si une configuration client est présente : 
            if (!empty($societeLivraison->client__memo_config)) {
                echo '<strong>ATTENTION CONFIG CLIENT</strong> : ' . $societeLivraison->client__memo_config;
            }
            ?>
            <table CELLSPACING=0 style=" width: 100%;  ">
                <tr style="background-color: #dedede;">
                    <td style="text-align: center; width: 30%"><strong>Préparé par</strong></td>
                    <td style="text-align: center; width: 40%"><strong>Réceptionné par : </strong></td>
                    <td style="text-align: center; width: 30%"><strong>POIDS</strong></td>
                </tr>
                <tr>
                    <td style="border: 1px #ccc solid; height: 150px; text-align:center;">
                        <span style="margin-top: 65px; background-color: #dedede;"><strong>Controle qualité</strong></span>
                    </td>
                    <td style="border: 1px #ccc solid; ">
                        <small><i>Nom/signature/tampon</i></small>
                    </td>
                    <td style="border: 1px #ccc solid; ">
                    </td>
                </tr>
            </table>
        </div>
    </page_footer>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: left;  width: 50%"><img style=" width:60mm" src="public/img/recodeDevis.png" /></td>
            <td style="text-align: left; width:50%">
                <h3>Reparation-Location-Vente</h3>imprimantes- lecteurs codes-barres<br>
                <a>www.recode.fr</a><br><br>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align: left;  width: 50% ; margin-left: 25%;">
                <h4>Fiche De travail - <?php echo $command->devis__id ?></h4>
                <barcode dimension="1D" type="C128" label="none" value="<?php echo $command->devis__id ?>" style="width:40mm; height:8mm; color: #3b3b3b; font-size: 4mm"></barcode><br>

                Commandé le : <strong><?php echo $formated_date ?></strong><br>
                Commercial : <strong><?php
                                        if (!empty($user)) {
                                            echo  $user->nom . ' ' . $user->prenom;
                                        } else {
                                            echo 'Non renseigné';
                                        }
                                        ?>
                </strong>
                <?php
                if (!empty($user->postefix)) {
                    echo ' (Tél: ' . $user->postefix . ')';
                }


                ?>


                <?php
                if (!empty($userCMD)) {
                    echo  '<br>Commandé par : <strong>' . $userCMD->nom . ' ' . $userCMD->prenom . '</strong> ';
                }
                ?>

                <?php
                if (!empty($userCMD->postefix)) {
                    echo ' (Tél: ' . $userCMD->postefix . ')';
                }


                ?>
            </td>
            <td style="text-align: left; width:50%"><strong><?php
                                                            if ($societeLivraison) {

                                                                if ($command->devis__contact__id) {
                                                                    // si un contact est présent dans l'adresse de facturation :
                                                                    $contact = $Contact->getOne($command->devis__contact__id);
                                                                    echo "<small>facturation : " . $contact->contact__civ . " " . $contact->contact__nom . " " . $contact->contact__prenom . "</small><strong><br>";
                                                                    echo Pdfunctions::showSociete($clientView) . " </strong> ";
                                                                    if (!empty($clientView->client__tel)) {
                                                                        echo '<br> TEL : ' . $clientView->client__tel . '';
                                                                    }

                                                                    if ($command->devis__contact_livraison) {
                                                                        //si un contact est présent dans l'adresse de livraison : 
                                                                        $contact2 = $Contact->getOne($command->devis__contact_livraison);
                                                                        echo "<br> <small>livraison : " . $contact2->contact__civ . " " . $contact2->contact__nom . " " . $contact2->contact__prenom . "</small><strong><br>";
                                                                        echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
                                                                        if (!empty($societeLivraison->client__tel)) {
                                                                            echo '<br> TEL : ' . $societeLivraison->client__tel . '';
                                                                        }
                                                                    } else {
                                                                        // si pas de contact de livraison : 
                                                                        echo "<br> <small>livraison :</small><strong><br>";
                                                                        echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
                                                                        if (!empty($societeLivraison->client__tel)) {
                                                                            echo '<br> TEL : ' . $societeLivraison->client__tel . '';
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo "<small>facturation :</small><strong><br>";
                                                                    echo Pdfunctions::showSociete($clientView) . " </strong>";
                                                                    if ($command->devis__contact_livraison) {
                                                                        $contact2 = $Contact->getOne($command->devis__contact_livraison);
                                                                        echo "<br> <small>livraison : " . $contact2->contact__civ . " " . $contact2->contact__nom . " " . $contact2->contact__prenom . "</small><strong><br>";
                                                                        echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
                                                                        if (!empty($societeLivraison->client__tel)) {
                                                                            echo '<br> TEL : ' . $societeLivraison->client__tel . '';
                                                                        }
                                                                    } else {
                                                                        echo "<br> <small>livraison :</small><strong><br>";
                                                                        echo Pdfunctions::showSociete($societeLivraison) . "</strong>";
                                                                        if (!empty($societeLivraison->client__tel)) {
                                                                            echo '<br> TEL : ' . $societeLivraison->client__tel . '';
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                if ($command->devis__contact__id) {
                                                                    $contact = $Contact->getOne($command->devis__contact__id);
                                                                    echo "<small>livraison & facturation : " . $contact->contact__civ . " " . $contact->contact__nom . " " . $contact->contact__prenom . "</small><strong><br>";
                                                                    echo Pdfunctions::showSociete($clientView)  . "</strong>";
                                                                    if (!empty($clientView->client__tel)) {
                                                                        echo '<br> TEL : ' . $clientView->client__tel . '';
                                                                    }
                                                                } else {
                                                                    echo "<small>livraison & facturation : </small><strong><br>";
                                                                    echo Pdfunctions::showSociete($clientView)  . "</strong>";
                                                                    if (!empty($clientView->client__tel)) {
                                                                        echo '<br>TEL : ' . $clientView->client__tel . '';
                                                                    }
                                                                }
                                                            }



                                                            if ($command->cmd__code_cmd_client) {
                                                                echo "<br> Code cmd: " . $command->cmd__code_cmd_client;
                                                            }
                                                            ?>
                </strong>
            </td>
        </tr>
    </table>


    <table CELLSPACING=0 style="width: 100%;  margin-top: 80px; ">
        <tr style=" margin-top : 50px; background-color: #dedede;">
            <td style="width: 21%; text-align: left;">Presta<br>Type<br>Gar.</td>
            <td style="width: 60%; text-align: left">Ref Tech<br>Désignation Client<br>Complement techniques</td>
            <td style="text-align: center; width: 7%"><strong>CMD</strong></td>
            <td style="text-align: center; width: 7%"><strong>Dispo</strong></td>
            <td style="text-align: center; width: 7%"><strong>Livré</strong></td>
        </tr>
        <?php
        foreach ($commandLignes as $item) {
            if ($item->cmdl__garantie_option > $item->devl__mois_garantie) {
                $temp = $item->cmdl__garantie_option;
            } else {
                if (!empty($item->devl__mois_garantie)) {
                    $temp = $item->devl__mois_garantie;
                } else {
                    $temp = "";
                }
            }

            if (!empty($item->cmdl__sous_ref)) {
                $background_color = 'background-color: #F1F1F1;';
            } else {
                $background_color = '';
            }

            echo "<tr style='font-size: 100%; " . $background_color . "'>
                        <td style='border-bottom: 1px #ccc solid'> " . $item->prestaLib . " <br> " . $item->kw__lib . " <br> " . $temp . " mois</td>
                        <td style='border-bottom: 1px #ccc solid; width: 55%;'> 
                            <br> <small>désignation :</small> <b>" . $item->devl__designation . "</b><br>"
                . $item->famille__lib . " " . $item->marque . " " . $item->modele . " " . $item->devl__modele  . " " . $item->devl__note_interne .
                "<br><span>
                                ".$ligne->devl__note_client."
                </span></td>
                         <td style='border-bottom: 1px #ccc solid; text-align: center'><strong> "  . $item->devl_quantite . " </strong></td>
                          <td style='border-bottom: 1px #ccc solid; border-left: 1px #ccc solid; text-align: right'><strong>  </strong></td>
                         <td style='border-bottom: 1px #ccc solid; border-left: 1px #ccc solid; text-align: right'><strong>  </strong></td>
                      </tr>";
        }
        ?>
    </table>

    <table style=" margin-top: 50px; width: 100%">
        <tr style=" margin-top: 200px; width: 100%">
            <td><small>Commentaire:</small></td>
        </tr>
        <tr>
            <td style='border-bottom: 1px black solid; border-top: 1px black solid; width: 100%'> <?php echo  $command->devis__note_interne ?> </td>
        </tr>
    </table>



</page>

<?php
$content = ob_get_contents();

try {
    $doc = new Html2Pdf('P', 'A4', 'fr');
    $doc->setDefaultFont('gothic');
    $doc->pdf->SetDisplayMode('fullpage');
    $doc->writeHTML($content);
    ob_clean();

    if ($_SERVER['HTTP_HOST'] != "localhost:8080") {
        $doc->output('O:\intranet\Auto_Print\FT\Ft_' . $command->devis__id . '.pdf', 'F');
    }

    //  $doc->output('C:\Users\abizien\Documents/'.$command->devis__id.'.pdf' , 'F'); 

    if ($_SESSION['user']->user__devis_acces < 10) {
        header('location: ficheTravail');
    } else {
        header('location: ficheTravail');
    }
} catch (Html2PdfException $e) {
    die($e);
}
