<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();

//mailer : 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './vendor/phpmailer/phpmailer/src/Exception.php';
require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/phpmailer/src/SMTP.php';

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) {
    header('location: login');
} else {

    if ($_SESSION['user']->user__devis_acces < 10) {
        header('location: noAccess');
    }

    $user = $_SESSION['user'];
   
    //connexion et requetes :
    $Database = new App\Database('devis');
    $Database->DbConnect();
    $Client = new App\Tables\Client($Database);
    $Keywords = new App\Tables\Keyword($Database);
    $User = new App\Tables\User($Database);
    $General = new App\Tables\General($Database);
    $Pisteur = new App\Tables\Pistage($Database);
    $Article = new App\Tables\Article($Database);
    $Contact = new App\Tables\Contact($Database);
    $Stats = new App\Tables\Stats($Database);
    $_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);
    $_SESSION['user']->devis_cours = $Stats->get_user_devis($_SESSION['user']->id_utilisateur);
    $clientList = $Client->get_client_devis();
    $articleList = $Article->getModels();

    $alert = false;
    $alertSuccess = false;
    

   
    if (!empty($_POST['client']) && !empty($_POST['modele']) && !empty($_POST['matiere']) && !empty($_POST['Thermique']) &&  !empty($_POST['matiere-ruban'])) 
    {

        $client = $Client->getOne($_POST['client']);


        $modele = $Article->get_article_devis(intval($_POST['modele']));

        $longueur = floatval($_POST['longueur']);
        if (!empty($_POST['hauteur'])) 
        {
                $hauteur = floatval($_POST['hauteur']);
        }
        else 
        {
                $hauteur = 'papier continue';
        }
       

        $content = '
        <ul>
            <li>
                    client N : '. $_POST['client'] . '<br> ' . $client->client__societe . '<br> ' . $client->client__cp . ' ' .  $client->client__ville . '
            </li>


            <li>
                    Modele :  ' . $modele->kw__lib.' '. $modele->afmm__modele .' ' . $modele->am__marque. '
            </li>


            <li>
                    Matiere : '. $_POST['matiere'].'
            </li>


            <li>
                    Thermique :  '. $_POST['Thermique'].'
            </li>


            <li>
                    largeur en millimetre :   '.$longueur.'<br>
                    Hauteur en millimetre  :    '.$hauteur.'
            </li>


            <li> 
                    Matiere-ruban :  '.$_POST['matiere-ruban'].'
            </li>


            <li> 
                    Colle : '.$_POST['colle'].'
            </li>


            <li>
                    Specifications : '.$_POST['text'].'
            </li>


            <li>
                    Envoye par : ' . $_SESSION['user']->nom . '
            </li>
        </ul>';
        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host       = 'mail01.one2net.net';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'crm@recode.fr';
            $mail->Password   = '@_YeaJL4';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            //Recipients
            $mail->setFrom('crm@recode.fr', 'Etiquettes');
            $mail->addAddress('demande.etiquette@recode.fr', '');
            //Content
            $mail->isHTML(true);

             $mail->Subject = 'Demande d etiquette de ' . $_SESSION['user']->nom . '';
             $mail->Body    =  $content;
            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        $alert = 'Votre demande d \' étiquettes à bien été transmise par mail à : demande.etiquette@recode.fr !   '; 
    }



    // Donnée transmise au template : 
    echo $twig->render('etiquettes.twig', [
        'user' => $user,
        'alert' => $alert,
        'alertSucces' => $alertSuccess , 
        'client_list' => $clientList , 
        'article_list' => $articleList
       
    ]);
}
