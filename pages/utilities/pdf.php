<?php
session_start();

// Si un devis a été validé : 
if (!empty($_POST)) {
    $devisData = json_decode ($_POST["dataDevis"], true);
    var_dump($devisData);
    }
    else{ var_dump($_POST);}

   