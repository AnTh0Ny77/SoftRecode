<?php
 session_start();



 if (empty($_SESSION['user'])) 
 {
   header('location: login');
 }
 require "./App/twigloader.php";
 echo $twig->render('noAccess.twig', [
    'user'=>$_SESSION['user']
 ]);

