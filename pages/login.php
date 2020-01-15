<?php
 require "./vendor/autoload.php";
 require "./App/twigloader.php";
 session_start();
    //si une session est ouverte :
    if (!empty($_SESSION['user'])) {
        header('location: home');
    }

    //si la connexion n'existe pas : 
    if (empty($Database)) {

       // Connexion à la base de donnée et à la table Utilisateur :
        $Database = new App\Database('devisrecode');
        $Database->DbConnect();
        $Users = new App\Tables\Users($Database,'utilisateur');
       
        // Affichage du template Login :
        echo $twig->render('login.twig');
    }

    //si le formulaire a ete soumis : 
    if (!empty($_POST['login'] && !empty($_POST['pass']))){

        // verification des concordances en base de donnée : 
       $login = $Users->login($_POST['login'],$_POST['pass']);
            
       switch ($login) {
           // si erreur envoi info au template : 
           case false:
               echo $twig->render('login.twig',['login'=>'$login']);
               break;
           // sinon redirection de l'utilisateur vers "home"  : 
           default:
               $_SESSION['user'] = $login ;
               header('location: home');
               break;
       }

    }
 


 


