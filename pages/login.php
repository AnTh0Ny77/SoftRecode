<?php
 require "./vendor/autoload.php";
 require "./App/twigloader.php";

    //si la connexion n'existe pas : 
    if (!isset($Database)) {

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
               echo 'raté';
               break;
           // sinon je dirige l'utilisateur vers "home" et déclenche sa session : 
           default:
               header('location: home');
               break;
       }

    }
 


 


