<?php
require "./vendor/autoload.php"; 
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
    $Article = new App\Tables\Article($Database);
    $date_now     = date("Y-m-d");
    $datetime_now = date("Y-m-d H:i:s");
    // traitement du formulaire :
    $message = "";
    // recuperation des paramettres
    $creat = $modif = $GrpModele = $GrpMarque = $GrpPN = FALSE;
    if (isset($_POST['Creat']))     $creat = TRUE;
    if (isset($_POST['Modif']))     $modif = TRUE;
    // variables
    $accents = array('/[áàâãªäÁÀÂÃÄ]/u'=>'a','/[ÍÌÎÏíìîï]/u'=>'i','/[éèêëÉÈÊË]/u'=>'e','/[óòôõºöÓÒÔÕÖ]/u'=>'o','/[úùûüÚÙÛÜ]/u'=>'u','/[çÇ]/u' =>'c'); // pour supprimer accent (Attention ICI ca transforme en minuscule)


 /*""b8 88""Yb 888888    db    888888 
dP   `" 88__dP 88__     dPYb     88   
Yb      88"Yb  88""    dP__Yb    88   
 YboodP 88  Yb 888888 dP""""Yb   8*/   
    if($creat)
    { // création de FMM (Model avec famille et marque)
        $msg_info = '';
        // Upload de l'image (exemple complet sur https://phpcodeur.net/articles/php/upload)
        // Image
        $root_image = './public/_Documents_/Modele_Image/'; // repertoires pour image modele
        $nom_image  = basename($_FILES['modele_image']['name']); // nom brut de destination
        $nom_image  = preg_replace(array_keys($accents), array_values($accents), $nom_image); // enlever les accents
        $nom_image  = strtoupper($nom_image); // mise en majuscule
        $nom_image  = preg_replace('/([^.a-z0-9]+)/i', '-', $nom_image); // suppression des caractères autres que lettre chiffres . et remplacement par - 
        $blob_image = file_get_contents($_FILES['modele_image']['tmp_name']);
        // Doc
        $root_doc = './public/_Documents_/Modele_Doc/'; // repertoires pour doc modele
        $nom_doc  = basename($_FILES['modele_doc']['name']); // nom brut de destination
        $nom_doc  = preg_replace(array_keys($accents), array_values($accents), $nom_doc); // enlever les accents
        $nom_doc  = strtoupper($nom_doc); // mise en majuscule
        $nom_doc  = preg_replace('/([^.a-z0-9]+)/i', '-', $nom_doc); // suppression des caractères autres que lettre chiffres . et remplacement par - 
        // ecriture dans la base
        $last_id_fmm = $Article->create($_POST['famille'], $_POST['marque'], $_POST['modele'], $blob_image, $nom_doc);
        // prefixage des nom de image te doc avec le id du model (format 00000-) (ID complété par zero)
        $last_id_fmm = substr('00000'.$last_id_fmm.'-',-6); // pour completer a zero sur 5 positions et - a la fin
        // Upload de Image
        $upload_image = $root_image.$last_id_fmm.$nom_image; // nom complet de destination dir et nom de fichier validé
        if (move_uploaded_file($_FILES['modele_image']['tmp_name'], $upload_image)) 
            $msg_info .= "Fichier Image Ajouté<br>"; else $msg_info .= "!Fichier Image Absent ou trop volumineux.<br>";
        // Upload de Doc
        $upload_doc = $root_doc.$last_id_fmm.$nom_doc; // nom complet de destination dir et nom de fichier validé
        if (move_uploaded_file($_FILES['modele_doc']['tmp_name'], $upload_doc)) 
            $msg_info .= "Fichier Doc Ajouté<br>"; else $msg_info .= "!Fichier Doc Absent ou trop volumineux.<br>";
        //print $_POST['famille'].'<br>'.$_POST['marque'].'<br>'.$_POST['modele'].'<br>'.$nom_image.'<br>'.$blob_image.'<br>'.$nom_doc.'<br>'; // pour debug

        header('location: ArtCatalogueModele');
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
