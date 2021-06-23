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
    

   
    if (!empty($_POST['client']) && !empty($_POST['modele']) && !empty($_POST['matiere']) && !empty($_POST['Thermique']) &&  !empty($_POST['matiere'])) 
    {
        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'mail01.one2net.net';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'anthony.bizien@recode.fr';
            $mail->Password   = '3Tr9cFG8';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            //Recipients
            $mail->setFrom('etiquette@recode.fr', 'Etiquettes');
            $mail->addAddress('francois.buineau@recode.fr', '');
            $mail->addBCC('crm@recode.fr');
            //Content
            $mail->isHTML(true);

             $mail->Subject = 'Demande d\' étiquette de ' . $_SESSION['user']->nom . '';
             $mail->Body    = '
             ';
            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
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
