<?php
 session_start();
 require "./App/twigloader.php";
 echo $twig->render('dashboard.twig', [
    'user'=>$_SESSION['user']
 ]);
