<?php

namespace App\Methods;

require "./vendor/autoload.php";

use App\Tables\Cmd;
use App\Tables\Client;
use App\Database;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;


class Abonnements_functions
{
        public static function contrat_double_exemplaire_location($id_commande, $presta)
        {
                $Database = new Database('devis');
                $Database->DbConnect();
                $Client = new Client($Database);
                $Cmd = new Cmd($Database);
                $temp =   $Cmd->GetById($id_commande);
                $clientView = $Client->getOne($temp->client__id);
                $formate = date("d/m/Y");


                //imprime 2 examplaires du contrat: 
                for ($i = 0; $i < 2; $i++) {
                        ob_start();
?>

                        <style type="text/css">
                                .page_header {
                                        margin-left: 30px;
                                        margin-top: 30px;
                                }

                                table {
                                        font-size: 13;
                                        font-style: normal;
                                        font-variant: normal;
                                        border-collapse: separate;
                                }

                                strong {
                                        color: #000;
                                }

                                h3 {
                                        color: #666666;
                                }

                                h2 {
                                        color: #3b3b3b;
                                }
                        </style>


                        <page backtop="40mm" backleft="10mm" backright="10mm" backbottom="35mm" footer="page">

                                <page_header>
                                        <table class="page_header" style="width: 100%;">
                                                <tr>
                                                        <td style="text-align: left;  width: 50%"><img style=" width:65mm" src="public/img/recodeDevis.png" /></td>
                                                        <td style="text-align: left; width:50%">
                                                                <h3>REPARATION-LOCATION-VENTE</h3>imprimantes-lecteurs codes-barres<br><a style="color: green;">www.recode.fr</a><br><br>
                                                        </td>
                                                </tr>
                                        </table>
                                </page_header>
                                <page_footer>
                                <hr>
                                        <table class="page_footer" style="text-align: center; margin: auto; font-size: 85%; ">
                                                <tr>
                                                        <td style="text-align: left; ">
                                                                TVA: FR33 397 934 068<br>
                                                                Siret 397 934 068 00016 - APE 9511Z<br>
                                                                SAS au capital 38112.25 €
                                                        </td>


                                                        <td style="text-align: right; ">
                                                                BPMED NICE ENTREPRISE<br>
                                                                <strong>IBAN : </strong>FR76 1460 7003 6569 0218 9841 804<br>
                                                                <strong>BIC : </strong>CCBPFRPPMAR
                                                        </td>
                                                </tr>

                                                <tr>

                                                        <td style=" font-size: 100%; width: 100%; text-align: center; " colspan=2><br><br>
                                                                <strong>RECODE by eurocomputer - 112 allée François Coli - 06210 Mandelieu - +33 4 93 47 25 00 - contact@recode.fr<br>
                                                                        Ateliers en France - 25 ans d'expertise - Matériels neufs & reconditionnés </strong>
                                                        </td>
                                                </tr>
                                        </table>
                                </page_footer>
                                <table class="page_header" style="width: 100%; font-size: 85%;">
                                        <tr>
                                                <td style="text-align: center;  width: 100%">
                                                        <h4>
                                                                <?php
                                                                if ($presta == 'MNT') {
                                                                        echo 'CONTRAT DE MAINTENANCE<br>
                                                                DE MATERIEL INFORMATIQUE';
                                                                } else {
                                                                        echo 'CONTRAT DE LOCATION<br>
                                                                        DE MATERIEL INFORMATIQUE';
                                                                }
                                                                ?>

                                                        </h4>
                                                        <h4>
                                                                CONDITIONS GENERALES
                                                        </h4>

                                                </td>


                                        </tr>
                                        <tr>
                                                <td style="text-align: left;  width: 100%">
                                                        <h4>
                                                                CONTRAT N°: <b><?php echo $temp->devis__id ?></b>
                                                        </h4>

                                                </td>


                                        </tr>
                                        <tr>
                                                <td>

                                                        <h5>ENTRE</h5>
                                                        <table>
                                                                <tr>
                                                                        <td style="text-align: left; margin-left: 35%; padding-top: 10px;">

                                                                                <?php
                                                                                echo "<div style=' padding: 15px 15px; width: 280px; font-weight: bold;'>";
                                                                                echo Pdfunctions::showSociete($clientView)  . "</div>";
                                                                                ?>
                                                                                <div style=' padding: 5px 15px ; width: 280px;'>
                                                                                        Ci-dessous dénommé<br>
                                                                                        ‘’LOCATAIRE’’
                                                                                </div>
                                                                        </td>
                                                                        <td style="text-align: left; margin-left: 35%; padding-top: 10px;">
                                                                                <div style=' padding: 15px 15px; width: 280px; font-weight: bold;'>
                                                                                        Recode - Eurocomputer<br>
                                                                                        PA de la Siagne - Technology Center<br>
                                                                                        06210 MANDELIEU
                                                                                </div>
                                                                                <div style=' padding: 5px 15px ; width: 280px;'>
                                                                                        Ci-dessous dénommé<br>
                                                                                        ‘’LOUEUR’’
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                        </table>






                                                </td>
                                                <td>

                                                </td>


                                        </tr>
                                </table>

                                <table>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 1 - VALIDITE ET OBJET</h6>
                                                        <p style=" font-size: 85%;">
                                                                Le présent contrat a pour objet la location d'un équipement informatique dont la désignation
                                                                figure aux conditions particulières ci-annexées. Le locataire se déclare être un utilisateur averti et
                                                                en aura la garde au sens de l’article 1384 du code civil. Le matériel est mis à la disposition du
                                                                locataire par le loueur, et sa prise en charge entraîne pour le locataire son acceptation tel qu’il lui
                                                                est livré et la parfaite connaissance de ses conditions d’utilisation et d’entretien. Les
                                                                consommables ne sont pas fournis par le loueur.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 2 - DUREE DE LA LOCATION</h6>
                                                        <p style=" font-size: 85%;">
                                                                La durée de la location est fixée aux conditions particulières.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 3 - DATE D'EFFET DE LA LOCATION</h6>
                                                        <ul style=" font-size: 85%;">
                                                                <li>
                                                                        Installation dans la 1° quinzaine : facturation au 1er
                                                                </li>
                                                                <li>
                                                                        Installation dans la 2° quinzaine : facturation au 15
                                                                </li>
                                                        </ul>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 4 - LOYERS</h6>
                                                        <p style=" font-size: 85%;">
                                                                4.1. Les loyers sont payés trimestriellement terme à échoir par virement bancaire. Ils sont
                                                                portables et non quérables. En cas de changement de domicile du preneur ou de changement de
                                                                domiciliation bancaire, le loueur devra en être informé 20 jours au moins avant la prochaine
                                                                échéance, les frais afférents à ces changements étant à la charge du locataire.<br>
                                                                4.2. Le premier loyer est exigible à la date de prise d'effet de la location, telle que définie à
                                                                l'article 3. Si cette prise d'effet de location intervient après le premier jour du mois, il est calculé
                                                                prorata temporis. Les loyers suivants sont payés le premier jour du premier mois de chaque
                                                                trimestre civil (01/04/07/10) de chaque mois.<br>
                                                                4.3. Même si les prix mentionnés aux conditions particulières sont hors taxes, tous droits et taxes
                                                                sont à la charge du locataire et lui sont facturés. Toute modification légale de ces droits et taxes
                                                                s'applique de plein droit et sans avis.<br>
                                                                4.4. Les loyers non payés à leur échéance, porteront intérêt au profit du loueur, de plein droit et
                                                                sans qu'il soit besoin d'une quelconque mise en demeure, au taux conventionnel maximal autorisé
                                                                par la loi, à compter de leur date d'exigibilité.<br>
                                                                4.5. Le loueur est expressément autorisé par le locataire à recouvrer le montant des loyers,
                                                                directement ou par l'intermédiaire de l'établissement financier ou bancaire de son choix, par
                                                                présentation d'avis de prélèvement en compte ou d'effets de commerce domiciliés sur son compte
                                                                bancaire, indiqué par le locataire. A cet effet, le locataire s'engage à aviser l'établissement chargé
                                                                de la tenue de son compte et à autoriser le paiement à présentation desdits effets ou avis de
                                                                prélèvement en compte.<br>
                                                                4.6. Une caution est impérative, validé par la direction commerciale et en fonction de la valeur du
                                                                matériel. Ce chèque est non encaissé et sera rendu suite à la restitution du matériel ou sera
                                                                conservée par le loueur, en cas d'annulation de commande, en cas de restitution du matériel en
                                                                mauvais état ou en cas d’arrêt du paiement des loyers pendant la durée du contrat.<br>

                                                                4.7 En cas de panne temporaire d'un matériel loué lié à une mauvaise utilisation, le locataire
                                                                renonce expressément à réclamer toute indemnité ou réduction de loyer de ce fait.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 5 - LIVRAISON, REPARATION, EXPLOITATION.</h6>
                                                        <p style=" font-size: 85%;">
                                                                5.1. Les frais de livraison ainsi que les frais d'installation sont à la charge du locataire. Le premier
                                                                jour suivant l’expiration du contrat initial ou de ses avenants, le locataire devra restituer le
                                                                matériel dans les locaux désignés par le loueur.<br>
                                                                Tout retard dans la restitution donnera lieu au versement d'une indemnité d’utilisation au moins
                                                                égale au loyer précédemment fixé ou pouvant être déterminée aux conditions particulières, de
                                                                plus le preneur supportera les frais consécutifs à cette restitution tardive.<br>

                                                                5.2. Dans le cas où RECODE prend à sa charge la maintenance des matériels, pendant
                                                                la période de location, conformément aux conditions particulières, les dépannages sont réalisés
                                                                aux conditions suivantes :<br>
                                                                A) Le client a l'obligation d'appeler RECODE BY EUROCOMPUTEUR au tel : 04.93.47.25.00,
                                                                fax : 04.93.47.01.16 en cas d'incident sur le matériel désigné aux conditions particulières.<br>
                                                                B) RECODE BY EUROCOMPUTEUR sera à ce moment, seul juge des moyens à mettre en œuvre, pour réaliser
                                                                toute réparation dans les meilleurs délais et conditions, elle pourra être amenée à procéder, au
                                                                remplacement du matériel par échange du matériel défectueux, par un matériel opérationnel,<br>
                                                                et/ou fournir son service technique sur le site du client, pour mener à bien le dépannage.
                                                                C) Les garanties ne couvrent, en aucun cas, les problèmes ou les sinistres du transport ni la
                                                                mauvaise installation des matériels, ni la mauvaise utilisation par le locataire, ni les sinistres
                                                                habituellement couverts par des contrats bris de machines ou assurance pour les risques tels que
                                                                les dégâts des eaux, incendie, foudre, vol, ...<br>
                                                                D) Les temps d'indisponibilité et le préjudice subi par le locataire dû aux pannes et aux
                                                                dépannages ne pourra constituer aucune indemnité pour le locataire.<br>
                                                                5.3. Le locataire s'engage à utiliser l'équipement suivant les spécifications du constructeur
                                                                (notamment en ce qui concerne l'environnement et les fournitures, la climatisation et
                                                                l'alimentation électrique), à prendre toutes les dispositions pour qu'il soit maintenu en bon état de
                                                                marche pendant toute la durée de la location.<br>
                                                                5.4. L'équipement ne pourra être déplacé, même dans les locaux du locataire, sans l'accord écrit
                                                                du loueur. Toutes les opérations de déplacement seront effectuées sous contrôle du personnel du
                                                                constructeur et aux frais du locataire.
                                                                Les loyers resteront dus pendant le déplacement.<br>
                                                                5.5. Une plaque de propriété sera apposée sur l'équipement et laissée en place par le locataire.
                                                        </p>
                                                </td>
                                        </tr>

                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 6- SOUS-LOCATION, CESSION, DELEGATION, NANTISSEMENT.</h6>
                                                        <p style=" font-size: 85%;">
                                                                6.1. Le locataire ne pourra ni sous-louer, ni prêter, ni mettre à la disposition de quiconque, à
                                                                quelque titre et sous quelque forme que ce soit, tout ou une partie du matériel, sans l'accord écrit
                                                                du loueur.<br>
                                                                6.2. Le locataire reconnaît que le loueur l'a tenu informé de l'éventualité d'une cession, d'un
                                                                nantissement ou d'une délégation du présent contrat, au profit de toute personne physique ou
                                                                morale de son choix.<br>
                                                                Il consent, dès à présent et sans réserve, à une telle opération et s'engage à signer à la première
                                                                demande du loueur, tout document nécessaire à la régularisation juridique et administrative de
                                                                l'opération.
                                                                Cette opération pourra, le cas échéant, lui être simplement signifiée par lettre recommandée, avec
                                                                accusé de réception, ou par acte extrajudiciaire.<br>
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 7 - DOMMAGES AU MATERIEL LOUE, RESPONSABILITE.</h6>
                                                        <p style=" font-size: 85%;">
                                                                7.1. A compter de la date de réception de l'équipement et même après la fin de location, tant que
                                                                ledit équipement restera sous sa garde, le locataire est responsable de tous dommages causés par
                                                                l'équipement loué. En conséquence, le locataire est tenu de souscrire une police garantissant sa
                                                                responsabilité civile. Le locataire devra s’assurer que sont notifiés à la compagnie d’assurance,
                                                                les droits du loueur et le fondement de la propriété juridique de celui-ci sur le matériel.
                                                                Outre l'obligation de déclarer tout sinistre ou vol à sa compagnie d'assurance, le locataire devra
                                                                en informer le loueur dans les mêmes délais par lettre recommandée avec avis de réception, lui
                                                                adresser une déclaration détaillée, et devra faire tout ce qui sera nécessaire pour permettre
                                                                l'expertise.
                                                                En cas de vol, il devra joindre à sa déclaration le récépissé de dépôt de plainte auprès des
                                                                autorités compétentes.
                                                                Si le matériel est irréparable ou ne peut être restitué pour quelque cause que ce soit, le locataire
                                                                devra au loueur une indemnité dont la valeur égale au prix de remplacement du matériel.
                                                                Dans tous les cas, les loyers continueront à courir jusqu'au règlement complet de l'indemnité à
                                                                recevoir.
                                                                Au cas où le montant de l'indemnité versée par la compagnie ne couvrirait pas la totalité des
                                                                sommes dues au loueur, en raison notamment de l'application d'une franchise ou pour tout autre
                                                                motif, la différence en résultant serait supportée par le locataire. De même tout sinistre qui
                                                                n'aurait pas été pris en charge par la compagnie d'assurance, ou qui n'aurait pas été déclaré, reste
                                                                à la charge exclusive du locataire.<br>
                                                                7.2 Cas de vol ou sinistre total
                                                                En cas de sinistre total ou de vol le locataire s’engage à subroger le loueur dans le bénéfice de
                                                                l’indemnité d’assurance visant le remplacement du matériel. Il lui appartiendra d’obtenir au titre
                                                                de l’indemnité d’usage le montant de la facturation pendant le temps où il n’a pas eu la
                                                                disposition du matériel. La survenance de tels événements n’arrêtera pas la facturation.<br>
                                                                7.3. Le locataire a choisi le matériel sous sa seule responsabilité. Il est également rappelé que le
                                                                loueur n'est pas constructeur du matériel loué. En conséquence, le loueur ne saurait en aucun cas,
                                                                être responsable de dommages résultants d'un vice de construction.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 8 - EVOLUTION DU CONTRAT.</h6>
                                                        <p style=" font-size: 85%;">
                                                                Le contrat peut évoluer par avenants successifs pendant toute la durée prévue aux conditions
                                                                particulières sans donner lieu à un nouvel échelonnement général du contrat.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 9 - RESILIATION DU CONTRAT.</h6>
                                                        <p style=" font-size: 85%;">
                                                                Le contrat est conclu et accepté irrévocablement par les parties dès sa signature. Sauf condition
                                                                expresse prévue aux conditions particulières, la durée minimale est de 12 mois. Le service de
                                                                location RECODE BY EUROCOMPUTEUR n'est pas résiliable en cours de période, il pourra être résilié par le client à la fin de
                                                                chaque période, par lettre recommandée avec AR, avec un préavis de 3 mois, avant la date de
                                                                renouvellement.
                                                                En cas d'annulation du contrat signifiée avant son terme, le locataire sera redevable envers le
                                                                loueur d'une indemnité d'annulation du contrat égale à la moitié des loyers restant dus.
                                                                L'annulation du contrat ne sera reconnue effective qu'à la date de règlement de l'indemnité définie
                                                                ci-dessus.
                                                                Le contrat de location peut être résilié de plein droit par le loueur, sans qu'il n’ait besoin de
                                                                remplir aucune formalité judiciaire, huit jours après mise en demeure en cas de non-paiement, à
                                                                l'échéance d'un seul terme de loyer ou en cas de non-exécution par le locataire, d'une seule des
                                                                conditions générales ou particulières de location et sans que des offres de payer ou d'exécuter
                                                                ultérieures, le paiement ou l'exécution après le délai imparti, puissent enlever au loueur le droit
                                                                d'exiger la résiliation encourue.
                                                                Dans cette éventualité, le locataire doit restituer le matériel au loueur, au lieu fixé par lui et devra
                                                                verser la totalité des loyers restant à courir.
                                                                Dans le cas où le locataire refuserait de restituer le matériel, il suffirait pour l'y contraindre d'une
                                                                ordonnance rendue par Monsieur le Président du Tribunal de Commerce sur simple requête ou
                                                                par voie de référé y compris sous astreinte
                                                                Tous les frais occasionnés au loueur par la résiliation du contrat, ainsi que tous les frais afférents
                                                                au démontage, à l'emballage ou au transport du matériel en retour, sont à la charge exclusive du
                                                                locataire.
                                                                En outre, la résiliation sera acquise de plein droit au loueur, aux torts du locataire sans formalité
                                                                en cas de diminution des garanties et notamment cession totale ou partielle par le locataire de son
                                                                fonds de commerce, mise en location gérance, dissolution de sa société ou de décès du locataire,
                                                                ou de saisie, vente ou confiscation des matériels loués.
                                                        </p>
                                                </td>
                                        </tr>

                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 10 - RESTITUTION DU MATERIEL</h6>
                                                        <p style=" font-size: 85%;">
                                                                10.1. Le locataire doit, en fin de période, restituer l'équipement en bon état d'entretien et de
                                                                fonctionnement. Tous les frais éventuels de remise en état seraient à sa charge. En cas de non
                                                                restitution au-delà de la durée précisée aux conditions particulières, après l’expiration de la date
                                                                butoir pour exercer le préavis le contrat est prolongé aux mêmes conditions par tacite
                                                                reconduction pour une durée identique.<br>
                                                                10.2. Les frais de déconnexion sont à la charge du locataire.
                                                        </p>
                                                </td>

                                        </tr>

                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 11 - GENERALITES</h6>
                                                        <p style=" font-size: 85%;">
                                                                Il incombe au client de mettre en place les procédures de sécurité pour assurer la protection de ses
                                                                propres données.
                                                                Dans le cas où pour une raison indépendante de sa volonté ou en un cas de force majeure, le
                                                                loueur ne peut pas fournir le service demandé par le client, il ne peut en être tenu pour
                                                                responsable.
                                                                Le client convient que les présentes dispositions et conditions générales, constituent l'intégralité
                                                                de l'accord entre les parties et remplacent toutes dispositions orales ou écrites antérieures.
                                                        </p>
                                                </td>

                                        </tr>

                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 12 - COMPETENCES</h6>
                                                        <p style=" font-size: 85%;">
                                                                Le présent contrat de service est régi par le droit français. En cas de contestation sur
                                                                l'interprétation ou l'exécution de ce dernier, le tribunal de commerce de Grasse sera seul
                                                                compétent.
                                                        </p>
                                                </td>

                                        </tr>
                                </table>
                                <table style="margin-top: 20px; width: 100%;">
                                        <tr>
                                                <td style=" font-size: 95%;  text-align: left;">
                                                        <p style=" font-size: 95%;">
                                                                Fait en double exemplaire<br>
                                                                Fait à Mandelieu le:<b> <?php echo $formate; ?></b><br><br>
                                                                Pour le locataire:
                                                        </p>
                                                </td>
                                                <td style=" font-size: 95%;  text-align: left; padding-left: 150px;">
                                                        <p style=" font-size: 95%;">
                                                                <br>
                                                                <br>
                                                                <br>
                                                                Pour le loueur:
                                                        </p>
                                                </td>

                                        </tr>

                                </table>




                        </page>


                <?php
                        $content = ob_get_contents();
                        $num_ex = $i + 1;
                        try {
                                $doc = new Html2Pdf('P', 'A4', 'fr');
                                $doc->setDefaultFont('gothic');
                                $doc->pdf->SetDisplayMode('fullpage');
                                $doc->writeHTML($content);
                                ob_clean();
                                $doc->output('O:\intranet\Auto_Print\CT\contrat' . $temp->devis__id . '.pdf', 'F');
                                // $doc->output(__DIR__ . '' . $temp->devis__id . '.pdf', 'F');
                        } catch (Html2PdfException $e) {
                                die($e);
                        }
                }
        }


        public static function contrat_double_exemplaire_maintenace($id_commande, $presta)
        {
                $Database = new Database('devis');
                $Database->DbConnect();
                $Client = new Client($Database);
                $Cmd = new Cmd($Database);
                $temp =   $Cmd->GetById($id_commande);
                $clientView = $Client->getOne($temp->client__id);
                $formate = date("d/m/Y");


                //imprime 2 examplaires du contrat: 
                for ($i = 0; $i < 2; $i++) {
                        ob_start();
?>

                        <style type="text/css">
                                .page_header {
                                        margin-left: 30px;
                                        margin-top: 30px;
                                }

                                table {
                                        font-size: 13;
                                        font-style: normal;
                                        font-variant: normal;
                                        border-collapse: separate;
                                }

                                strong {
                                        color: #000;
                                }

                                h3 {
                                        color: #666666;
                                }

                                h2 {
                                        color: #3b3b3b;
                                }
                        </style>


                        <page backtop="40mm" backleft="10mm" backright="10mm" backbottom="35mm" footer="page">

                                <page_header>
                                        <table class="page_header" style="width: 100%;">
                                                <tr>
                                                        <td style="text-align: left;  width: 50%"><img style=" width:65mm" src="public/img/recodeDevis.png" /></td>
                                                        <td style="text-align: left; width:50%">
                                                                <h3>REPARATION-LOCATION-VENTE</h3>imprimantes-lecteurs codes-barres<br><a style="color: green;">www.recode.fr</a><br><br>
                                                        </td>
                                                </tr>
                                        </table>
                                </page_header>
                                <page_footer>
                                <hr>
                                        <table class="page_footer" style="text-align: center; margin: auto; font-size: 85%; ">
                                                <tr>
                                                        <td style="text-align: left; ">
                                                                TVA: FR33 397 934 068<br>
                                                                Siret 397 934 068 00016 - APE 9511Z<br>
                                                                SAS au capital 38112.25 €
                                                        </td>


                                                        <td style="text-align: right; ">
                                                                BPMED NICE ENTREPRISE<br>
                                                                <strong>IBAN : </strong>FR76 1460 7003 6569 0218 9841 804<br>
                                                                <strong>BIC : </strong>CCBPFRPPMAR
                                                        </td>
                                                </tr>

                                                <tr>

                                                        <td style=" font-size: 100%; width: 100%; text-align: center; " colspan=2><br><br>
                                                                <strong>RECODE by eurocomputer - 112 allée François Coli - 06210 Mandelieu - +33 4 93 47 25 00 - contact@recode.fr<br>
                                                                        Ateliers en France - 25 ans d'expertise - Matériels neufs & reconditionnés </strong>
                                                        </td>
                                                </tr>
                                        </table>
                                </page_footer>
                                <table class="page_header" style="width: 100%; font-size: 85%;">
                                        <tr>
                                                <td style="text-align: center;  width: 100%">
                                                        <h4>
                                                                CONTRAT DE MAINTENANCE<br>
                                                                DE MATERIEL INFORMATIQUE
                                                        </h4>
                                                        <h4>
                                                                CONDITIONS GENERALES
                                                        </h4>

                                                </td>


                                        </tr>
                                        <tr>
                                                <td style="text-align: left;  width: 100%">
                                                        <h4>
                                                                CONTRAT N°: <b><?php echo $temp->devis__id ?></b>
                                                        </h4>

                                                </td>


                                        </tr>
                                        <tr>
                                                <td>

                                                        <h5>ENTRE</h5>
                                                        <table>
                                                                <tr>
                                                                        <td style="text-align: left; margin-left: 35%; padding-top: 10px;">

                                                                                <?php
                                                                                echo "<div style=' padding: 15px 15px; width: 280px; font-weight: bold;'>";
                                                                                echo Pdfunctions::showSociete($clientView)  . "</div>";
                                                                                ?>
                                                                                <div style=' padding: 5px 15px ; width: 280px;'>
                                                                                        Ci-dessous dénommé<br>
                                                                                        ‘’CLIENT’’
                                                                                </div>
                                                                        </td>
                                                                        <td style="text-align: left; margin-left: 35%; padding-top: 10px;">
                                                                                <div style=' padding: 15px 15px; width: 280px; font-weight: bold;'>
                                                                                        Recode - Eurocomputer<br>
                                                                                        PA de la Siagne - Technology Center<br>
                                                                                        06210 MANDELIEU
                                                                                </div>
                                                                                <div style=' padding: 5px 15px ; width: 280px;'>
                                                                                        Ci-dessous dénommé<br>
                                                                                        ‘’Recode by Eurocomputer’’
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                        </table>
                                                </td>
                                                <td>
                                                </td>
                                        </tr>
                                </table>

                                <table>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 1 - OBJET</h6>
                                                        <p style=" font-size: 85%;">
                                                        Le présent contrat a pour objet la fourniture au client d'un service de garantie, pièces, main d’œuvre, échange standard, transport et
                                                        déplacement, pour les machines auditées ou révisées par Recode by Eurocomputer, portées en annexe, moyennant le paiement,
                                                        par le client, d'une redevance.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 2 - OBLIGATIONS DU CLIENT</h6>
                                                        <p style=" font-size: 85%;">
                                                        Pour pouvoir bénéficier du service de garantie, le client devra impérativement procéder de la manière suivante : 
                                                        - Contacter le Centre de support technique national Recode by Eurocomputer, à l'exclusion de tout autre intervenant :
                                                        - par téléphone au 04.93.47.25.00, 
                                                        - via la plateforme Euronet en ouvrant un ticket pour le SAV
                                                        - Indiquer le numéro de son contrat, le type, le modèle et le numéro de série du matériel en panne, 
                                                        - Décrire la nature de l'incident. 
                                                        En dehors de cette procédure, les frais correspondants à toute intervention non réalisée par Recode by Eurocomputer, ne pourra
                                                        être pris en charge par Recode by Eurocomputer.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 3 - DISPONIBILITE DU SERVICE DE GARANTIE.</h6>
                                                        <p style=" font-size: 85%;">
                                                        La période de couverture du service est assurée de 9h00 à 12h30 et de 14h00 à 18h00 du lundi au vendredi inclus, exception faite des
                                                        jours fériés légaux. En dehors de cette période ou de toute couverture complémentaire, toute intervention effectuée à la demande du
                                                        client sera facturable, selon le tarif en vigueur.
                                                        </p>
                                                </td>
                                        </tr>

                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 4 - DESCRIPTION DU SERVICE DE GARANTIE.</h6>
                                                        <p style=" font-size: 85%;">
                                                        Recode by Eurocomputer s'engage à fournir à son client, le service de garantie suivant :
                                                        A) UN SERVICE D'ASSISTANCE TECHNIQUE TELEPHONIQUE COMPRENANT : 
                                                        - la prise en charge immédiate de l'incident par un technicien spécialisé, 
                                                        - le diagnostic téléphonique de l'incident, 
                                                        - le dépannage, par téléphone, des pannes de premier niveau, 
                                                        - la gestion des mises à niveau techniques nécessaires à chaque machine, 
                                                        - la tenue et le suivi d'un carnet de santé informatisé pour chaque machine, permettant l'entretien préventif. 
                                                        B) UN SERVICE DE DEPANNAGE : 
                                                        Après cette prise en charge, le dépannage est réalisé, afin d'assurer la meilleure disponibilité et le meilleur service au client, selon les 
                                                        machines, soit par échange standard, soit en retour atelier, soit sur intervention technique sur le site du client. 
                                                        C) PROCEDURE D'ECHANGE STANDARD : 
                                                        Pour les matériels facilement transportables , par exemple les terminaux , Recode by Eurocomputer procède au dépannage , par
                                                        échange standard de pièces ou matériel complet sur le site du client, en remplaçant ces derniers par des pièces ou machines du
                                                        même type et du même modèle , parfaitement opérationnelles et intégralement révisées en laboratoire , en moins de 24 heures
                                                        ouvrées, à compter de l'appel du client, sauf cas de force majeure.
                                                        D) INTERVENTIONS TECHNIQUES SUR LE SITE DU CLIENT : 
                                                        Pour ce qui concerne les opérations de dépannage nécessitant une intervention technique sur le site du client, Recode by
                                                        Eurocomputer réalise la prise en charge de l'appel du client, en moins de 4 heures ouvrées.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 5 - PRISE EN CHARGE DES FRAIS DE DEPANNAGE.</h6>
                                                        <p style=" font-size: 85%;">
                                                        Tous les frais correspondants à la fourniture de pièces détachées, main-d’œuvre, échange standard, transport aller et déplacement,
                                                        dans le cadre du présent contrat de garantie, et pendant la durée du contrat, sont intégralement pris en charge par Recode by
                                                        Eurocomputer.
                                                        A) Connexion :
                                                        Les divers branchements et connexions simples sont effectués par le client. Dans le cas où le matériel l'exigerait, les frais de connexion
                                                        et de déconnexion sont à la charge de Recode by Eurocomputer.
                                                        B) Pièces :
                                                        Pour son service de garantie , Recode by Eurocomputer peut être amené à fournir , soit des pièces ou machines nouvellement
                                                        fabriquées, soit des pièces et machines ayant déjà servi, en tout état de cause, intégralement révisées et rigoureusement testées en
                                                        conditions de travail intensif , leur permettant les mêmes qualités et performances que si elles étaient neuves , hormis ravivage des
                                                        peintures.
                                                        C) Transfert de propriété :
                                                        Dans le cadre de la procédure d'Echange Standard , les pièces ou les machines opérationnelles échangées par Recode by
                                                        Eurocomputer deviennent la propriété du client, les pièces ou machines défectueuses deviennent la propriété de Recode by
                                                        Eurocomputer. Les pièces et machines opérationnelles échangées par Recode by Eurocomputer ne deviendront la propriété du
                                                        client, qu'après la récupération effective par Recode by Eurocomputer, des pièces et machines défectueuses.
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>ARTICLE 6 : EXCLUSIONS.</h6>
                                                        <p style=" font-size: 85%;">
                                                        Le service de garantie Recode by Eurocomputer exclut toutes les pannes liées aux transports, aux modifications de l'équipement non
                                                        acceptées par Recode by Eurocomputer , à une utilisation impropre du matériel , au défaut de fourniture de l'environnement
                                                        convenable prescrit par le constructeur, connexion, branchement électrique, climatisation, utilisation de consommables compatibles,
                                                        incidents liés à l’utilisation d’étiquettes adhésives, aux accidents liés aux catastrophes naturelles, bris de machines, dégâts des
                                                        eaux, incendie, vol, foudre, explosion, vandalisme , conséquences nucléaires , les risques majeurs, ainsi que la peinture ou le
                                                        ravivage des machines et la fourniture de consommables (rubans, papiers, toner, disquettes, chaînes et têtes d'impression, etc...).
                                                        </p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>Article 7 - DUREE DU CONTRAT DE GARANTIE.</h6>
                                                        <p style=" font-size: 85%;">
                                                        Le client bénéficie de la garantie Recode by Eurocomputer , pour la
                                                        durée fixée à l'annexe
                                                        </p>
                                                </td>
                                        </tr>

                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>ARTICLE 9 : RESILIATION ET RETRAIT</h6>
                                                        <p style=" font-size: 85%;">
                                                        Le service de garantie Recode by Eurocomputer est conclu pour une période minimale de 12 mois, et à l'échéance, renouvelé par tacite
                                                        reconduction.
                                                        Le service de garantie Recode by Eurocomputer n'est pas résiliable en cours de période, il pourra être résilié par le client à la fin de
                                                        chaque période, par lettre recommandée avec AR, avec un préavis de 3 mois, avant la date de renouvellement.
                                                        Recode by Eurocomputer peut, à condition de donner un préavis de 1 mois au client, retirer toute machine du présent contrat, à partir
                                                        de la fin de la première année suivant la date de commencement du service d'entretien.
                                                        Dans le cas où le montant mensuel du contrat devient inférieur à 55.00 euros HT, Recode by Eurocomputer peut, à condition de
                                                        donner un préavis de 1 mois au client par lettre recommandée avec AR, résilier le contrat.
                                                        </p>
                                                </td>

                                        </tr>

                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>ARTICLE 10 : DELAIS</h6>
                                                        <p style=" font-size: 85%;">
                                                        Recode by Eurocomputer s'engage à tout mettre en oeuvre pour fournir à son client, les meilleurs délais pour le dépannage des
                                                        matériels, en particulier, l'échange standard et l'envoi de pièces détachées sur stock Recode by Eurocomputer sont effectués en
                                                        moins de 24 heures ouvrées , par service de messagerie rapide , à compter de l'appel du client ; pour les interventions sur site,
                                                        Recode by Eurocomputer réalise la prise en charge de l'appel du client, en moins de 4 heures ouvrées.
                                                        </p>
                                                </td>

                                        </tr>

                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>ARTICLE 11 : LIMITATION DE RESPONSABILITES</h6>
                                                        <p style=" font-size: 85%;">
                                                        Les parties conviennent expressément que toute indisponibilité et préjudice financier ou commercial subi par le client (par exemple, 
                                                        perte de bénéfices, perte de commandes, perte d'exploitation, trouble commercial quelconque, etc...) dus aux pannes et aux dépannages ou toute action dirigée contre le client par un tiers, constitue un dommage indirect et par conséquent, n'ouvre pas droit à
                                                        réparation, même si Recode by Eurocomputer a été avisé de la possibilité de la survenance de tels dommages.
                                                        Pour tout autre préjudice subi par le client, dû au manquement par Recode by Eurocomputer à l'une quelconque de ses obligations
                                                        aux termes du présent contrat, l'indemnité réparatrice due au client en cas de faute prouvée de Recode by Eurocomputer , par un
                                                        expert agréé auprès des tribunaux en matière compétente, ne pourra dépasser la somme de 38.000 Euros.
                                                        Pour les réclamations relatives aux dommages corporels et aux dommages causés aux biens matériels (mobiliers ou immobiliers),
                                                        Recode by Eurocomputer sera responsable dans les conditions du droit commun.
                                                        Tous les logiciels, supports de logiciels, de données et de mémoire, doivent être sauvegardés par le Client, avant toute intervention du
                                                        Service de Dépannage Recode by Eurocomputer. Dans le cas contraire, et dans le cas où ces éléments auraient été endommagés
                                                        ou perdus par Recode by Eurocomputer, le Client ne pourra exiger aucune indemnité de Recode by Eurocomputer.
                                                        </p>
                                                </td>

                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>ARTICLE 12 : GENERALITES</h6>
                                                        <p style=" font-size: 85%;">
                                                        Les machines sont éligibles au contrat Recode by Eurocomputer, dans la mesure où elles ont été auditées, révisées ou fournies par
                                                        Recode by Eurocomputer.
                                                        Dans le cadre de l'échange standard, le client garantit qu'au moment où la machine défectueuse deviendra la propriété de Recode
                                                        by Eurocomputer, elle sera libre de tout gage, nantissement ou autres privilèges.
                                                        Au moment de la livraison et de l'échange standard de pièces ou machines, le client devra s'assurer de la facilité d'accès à l'équipement
                                                        défectueux. Le client devra s'assurer de la présence d'un membre de son personnel, représentant l'entreprise, au moment de l'échange
                                                        standard, afin de valider la procédure.
                                                        Les dispositions prises par Recode by Eurocomputer, pour fournir les services décrits dans le présent contrat, peuvent inclure la
                                                        collaboration d'autres fournisseurs choisis et agréés par Recode by Eurocomputer, y compris le Constructeur.
                                                        Le client devra également informer Recode by Eurocomputer de toute modification apportée sur le matériel (adjonction, augmentation
                                                        de capacité, retrait ou ajout de machines, etc...).
                                                        </p>
                                                </td>

                                        </tr>
                                        <tr>
                                                <td style=" font-size: 95%; width: 100%; text-align: left;">
                                                        <h6>ARTICLE 13 : DROIT APPLICABLE/FOR JURIDIQUE</h6>
                                                        <p style=" font-size: 85%;">
                                                        Par le surplus, les dispositions générales du droit commercial français régissent le présent contrat ; pour tout litige, le for juridique est 
                                                        CANNES. 
                                                        </p>
                                                </td>

                                        </tr>
                                </table>
                                <table style="margin-top: 20px; width: 100%;">
                                        <tr>
                                                <td style=" font-size: 95%;  text-align: left;">
                                                        <p style=" font-size: 95%;">
                                                                Fait en double exemplaire<br>
                                                                Fait à Mandelieu le:<b> <?php echo $formate; ?></b><br><br>
                                                                Pour le  RECODE BY EUROCOMPUTER:
                                                        </p>
                                                </td>
                                                <td style=" font-size: 95%;  text-align: left; padding-left: 150px;">
                                                        <p style=" font-size: 95%;">
                                                                <br>
                                                                <br>
                                                                <br>
                                                                Pour le CLIENT:
                                                        </p>
                                                </td>

                                        </tr>

                                </table>




                        </page>


                <?php
                        $content = ob_get_contents();
                        $num_ex = $i + 1;
                        try {
                                $doc = new Html2Pdf('P', 'A4', 'fr');
                                $doc->setDefaultFont('gothic');
                                $doc->pdf->SetDisplayMode('fullpage');
                                $doc->writeHTML($content);
                                ob_clean();
                                $doc->output('O:\intranet\Auto_Print\CT\contrat' . $temp->devis__id . '.pdf', 'F');
                                // $doc->output(__DIR__ . '' . $temp->devis__id . '.pdf', 'F');
                        } catch (Html2PdfException $e) {
                                die($e);
                        }
                }
        }

        public static function piece_jointe($id_commande)
        {
                $Database = new Database('devis');
                $Database->DbConnect();
                $Client = new Client($Database);
                $Cmd = new Cmd($Database);
                $temp =   $Cmd->GetById($id_commande);
                $clientView = $Client->getOne($temp->client__id);
                $formate = date("d/m/Y");
                ob_start();
                ?>

                <style type="text/css">
                        .page_header {
                                margin-left: 30px;
                                margin-top: 30px;
                        }

                        table {
                                font-size: 13;
                                font-style: normal;
                                font-variant: normal;
                                border-collapse: separate;
                        }

                        strong {
                                color: #000;
                        }

                        h3 {
                                color: #666666;
                        }

                        h2 {
                                color: #3b3b3b;
                        }
                </style>


                <page backtop="40mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page">

                        <page_header>
                                <table class="page_header" style="width: 100%;">
                                        <tr>
                                                <td style="text-align: left;  width: 50%"><img style=" width:65mm" src="public/img/recodeDevis.png" /></td>
                                                <td style="text-align: left; width:50%">
                                                        <h3>REPARATION-LOCATION-VENTE</h3>imprimantes-lecteurs codes-barres<br><a style="color: green;">www.recode.fr</a><br><br>
                                                </td>
                                        </tr>
                                </table>
                        </page_header>
                        <page_footer>
                                <hr>
                                        <table class="page_footer" style="text-align: center; margin: auto; font-size: 85%; ">
                                                <tr>
                                                        <td style="text-align: left; ">
                                                                TVA: FR33 397 934 068<br>
                                                                Siret 397 934 068 00016 - APE 9511Z<br>
                                                                SAS au capital 38112.25 €
                                                        </td>


                                                        <td style="text-align: right; ">
                                                                BPMED NICE ENTREPRISE<br>
                                                                <strong>IBAN : </strong>FR76 1460 7003 6569 0218 9841 804<br>
                                                                <strong>BIC : </strong>CCBPFRPPMAR
                                                        </td>
                                                </tr>

                                                <tr>

                                                        <td style=" font-size: 100%; width: 100%; text-align: center; " colspan=2><br><br>
                                                                <strong>RECODE by eurocomputer - 112 allée François Coli - 06210 Mandelieu - +33 4 93 47 25 00 - contact@recode.fr<br>
                                                                        Ateliers en France - 25 ans d'expertise - Matériels neufs & reconditionnés </strong>
                                                        </td>
                                                </tr>
                                        </table>
                                </page_footer>
                        <table class="page_header" style="width: 100%;">
                                <tr>
                                        <td style="text-align: left; width: 50%; padding-top: 10px;">


                                        </td>
                                        <td style="text-align: left; padding-top: 10px;">
                                                <h4>
                                                        <?php
                                                        echo "<div style='  width: 280px; '>";
                                                        echo Pdfunctions::showSociete($clientView)  . "</div>";
                                                        ?>
                                                </h4>
                                                <br>
                                                <br>
                                                Fait à Mandelieu le : <b> <?php echo $formate; ?></b>
                                        </td>
                                </tr>
                        </table>
                        <table style="width: 100%;">
                                <tr>
                                        <td style="text-align: left; width: 100%; padding-top: 30px;">
                                                <p style="font-size: 15;">Objet : contrat N°: <b><?php echo $temp->devis__id ?></b></p>
                                                <p>
                                                        Vous trouverez ci-joint votre contrat N° <b><?php echo $temp->devis__id ?></b> selon
                                                        votre demande en deux exemplaires.<br>
                                                        Merci de nous retourner les deux exemplaires du contrat signé et tamponné
                                                        aux endroits indiqués sur chaque page ainsi que les conditions générales.<br>
                                                        Dès réception, votre exemplaire vous sera contresigné et retourné.<br>
                                                        Dans cette attente, je vous prie de croire, Monsieur, à l’expression de mes
                                                        salutations distinguées.<br>
                                                        <br>
                                                        <br>
                                                        Debosschere Marie
                                                </p>
                                        </td>

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


                        $doc->output('O:\intranet\Auto_Print\CT\CTP\piece_jointe_' . $temp->devis__id . '.pdf', 'F');
                } catch (Html2PdfException $e) {
                        die($e);
                }
        }
}
