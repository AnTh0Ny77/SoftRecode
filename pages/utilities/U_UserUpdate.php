<?php
require "./vendor/autoload.php"; // FLM: utile ? il est deja en appel dans twigloader ca ne fait pas doublon ?
require "./App/twigloader.php";
session_start();

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']) ) 
    { header('location: login'); }
else
{
    //Connexion et requetes :
    $Database = new App\Database('devis');
    $Database->DbConnect();
    $User = new App\Tables\User($Database);
    $date_now     = date("Y-m-d");
    $datetime_now = date("Y-m-d H:i:s");
    // traitement du formulaire :
    $message = "";
    // recuperation des paramettre pour savoir si c'est Creat ou Modif
    $creat = $modif = FALSE;
    if (isset($_POST['Creat'])) $creat = TRUE;
    if (isset($_POST['Modif'])) $modif = TRUE;

 /*""b8 88""Yb 888888    db    888888 
dP   `" 88__dP 88__     dPYb     88   
Yb      88"Yb  88""    dP__Yb    88   
 YboodP 88  Yb 888888 dP""""Yb   8*/   
    if($creat)
    { // création de user
        if (!empty($_POST['idUser']) &&  !empty($_POST['loginUser']) && !empty($_POST['passwordUser'])) 
        {
            $password = password_hash($_POST['passwordUser'], PASSWORD_DEFAULT);
            // ordre des valeur de function create($id, $login, $date_arrive, $prenom, $nom, $log_nec, $email, $postefix, $gsm, $t_crm, $type_user, $fonction,
            // $devis_access, $cmd_access ,$saisie_access, $facture_access, $admin_access, $ticket_access, $password)
            $user = $User->create(
                intval($_POST['idUser']), $_POST['loginUser'],$_POST['dateArrive'],$_POST['prenomUser'],$_POST['nameUser'],$_POST['lognecUser'], $_POST['emailUser'],
                $_POST['posteUser'], $_POST['gsmUser'], $_POST['crmUser'], $_POST['typeUser'], $_POST['fonctionUser'],
                $_POST['devisUser'],$_POST['cmdUser'],$_POST['sasieUser'], $_POST['factureUser'], $_POST['adminUser'], $_POST['ticketUser'], $password);
            header('location: User');
        }
        else 
            header('location: User');
    }

/*    d8  dP"Yb  8888b.  88 888888 
88b  d88 dP   Yb  8I  Yb 88 88__   
88YbdP88 Yb   dP  8I  dY 88 88""   
88 YY 88  YbodP  8888Y"  88 8*/     
    if($modif)
    { // Modification de user
        if ($_POST['idUser']) // pour bien verifier qu'il y a un ID
        {
            if ($_POST['passwordUser']) 
                $password = password_hash($_POST['passwordUser'], PASSWORD_DEFAULT);
            else 
                $password = FALSE; // si pas de password ja passe FALSE pour ne pas mettre a jour via TABLES/USER.PHP
            // ordre des valeur de function modify($id, $login, $date_arrive, $prenom, $nom, $log_nec, $email, $postefix, $gsm, $t_crm, $type_user, $fonction,
            // $devis_access, $cmd_access ,$saisie_access, $facture_access, $admin_access, $ticket_access, $password)
            $user = $User->modify(
                intval($_POST['idUser']), $_POST['loginUser'],$_POST['dateArrive'],$_POST['prenomUser'],$_POST['nameUser'],$_POST['lognecUser'], $_POST['emailUser'],
                $_POST['posteUser'], $_POST['gsmUser'], $_POST['crmUser'], $_POST['typeUser'], $_POST['fonctionUser'],
                $_POST['devisUser'],$_POST['cmdUser'],$_POST['sasieUser'], $_POST['factureUser'], $_POST['adminUser'], $_POST['ticketUser'], $password);
            header('location: User');
        }
        else 
        {
            $message = "Mise a jour NON Effectué, PAS de IdUser";
            header('location: User');
        }
    }

}

?>
