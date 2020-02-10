<?php
session_start();

// Si un devis a été validé : 
if (!empty($_POST)) {
    var_dump($_POST["dataDevis"]) ;
  
    }

    else{ var_dump($_POST);}

   