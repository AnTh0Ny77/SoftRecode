<?php
 session_start();
 require "./App/twigloader.php";
 if (empty($_SESSION['user'])) 
 {
   header('location: login');
 }
 echo $twig->render('dashboard.twig', [
    'user'=>$_SESSION['user']
 ]);
