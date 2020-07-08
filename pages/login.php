<?php
 require "./vendor/autoload.php";
 require "./App/twigloader.php";

 session_start();
    //si une session est ouverte :
    if (!empty($_SESSION['user'])) {
        header('location: dashboard');
    }
        
    // Connexion à la base de donnée et à la table Utilisateur :
        $Database = new App\Database('devisrecode');
        $Database->DbConnect();
        $Users = new App\Tables\User($Database,'utilisateur');

    if ( !isset($_SESSION['loginStatus'])) {
        $_SESSION['loginStatus'] = true;
    }  
    
    //si le formulaire a ete soumis : 
        if (isset($_POST['login'])){

            // verification des concordances en base de donnée : 
                $login  = $Users->login($_POST['login']);
                $hash = $_POST['pass'];
                $verif = password_verify($hash, $login->password_user);
                if ($login) {
                   
                        switch ($verif) {
                            // si erreur envoi info au template : 
                            case false:
                                $_SESSION['loginStatus'] = false;
                                break;
                            // sinon redirection de l'utilisateur vers "page d'acceuil"  : 
                            default:
                                $_SESSION['user'] = $login ;
                                header('location: dashboard');
                                break;
                            }
                }
                else{header('location: dashboard');}   
        }

    // Affichage du template Login :
    echo $twig->render('login.twig',['loginStatus'=>$_SESSION['loginStatus'],
    ]);
 
   

 


