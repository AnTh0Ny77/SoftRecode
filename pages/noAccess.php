<?php
 session_start();
 require "./App/twigloader.php";
 echo $twig->render('noAccess.twig', [
    'user'=>$_SESSION['user']
 ]);

