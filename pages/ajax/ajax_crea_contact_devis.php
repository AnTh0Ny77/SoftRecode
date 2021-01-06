<?php
require "./vendor/autoload.php";


session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Contact = new App\Tables\Contact($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) {
    echo 'no no no .... ';
 }
 else {

// requete table client:
 if (!empty($_POST['fonction']) && !empty($_POST['nom']))
 {

    //   $Database = null;
    //   $Totoro = new App\Totoro('euro');
    //   $Totoro->DbConnect();
    //   $Database = new App\Database('devis');
    //   $Database->DbConnect();
    //   $ContactTotoro = new App\Tables\ContactTotoro($Totoro);

       //questions sur les controles de tout
         $newClientTotoro = $ContactTotoro->insertOne(
         $_POST['fonction'] , $_POST['civilite'],
         $_POST['nom'] ,$_POST['prenom'], 
         $_POST['tel'] , "" ,  $_POST['fax'], 
         $_POST['mail'] , $_POST['societe']);

         $newId = '0000000' . $newClientTotoro ;
         $newId = substr($newId , -6 );
         
        //  $Contact = new App\Tables\Contact($Database);
        //  $newLocalContact = $Contact->insertTotoro(
        //  $newClientTotoro , $_POST['fonction'] , $_POST['civilite'],
        //  $_POST['nom'] ,$_POST['prenom'], 
        //  $_POST['tel'] , "" , 
        //  $_POST['mail'] , $_POST['societe']);

        echo  json_encode($newClientTotoro);
 }
 else {
    echo 'request failed';
 }


}