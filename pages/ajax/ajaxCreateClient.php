<?php
require "./vendor/autoload.php";


session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new App\Tables\Client($Database);


if (empty($_SESSION['user']->id_utilisateur)) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['societeNameCreate'])){
      $response = $Client->insertOne($_POST['societeNameCreate'],$_POST['inputAddress'],$_POST['inputAddress2'],$_POST['inputZip'],$_POST['inputCity']);
      $client = $Client->getOne($response);
      echo  json_encode($client);
 }
 else {
    echo 'request failed';
 }


}