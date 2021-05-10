<?php
require "./vendor/autoload.php";


session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Contact = new App\Tables\Contact($Database);
$Contact_totoro = new App\Tables\Contact($Database);


if (empty($_SESSION['user']->id_utilisateur)) 
{
	 echo 'no no no .... ';
}
 else {

// requete table client:
 if (!empty($_POST['inputStateContact']))
 {
	 $response = $Contact->insertOne($_POST['inputStateContact'],$_POST['inputCiv'],$_POST['nomContact'],$_POST['prenomContact'],
	 $_POST['telContact'],$_POST['faxContact'],$_POST['mailContact'],$_POST['societeLiaison']);
	 $contact = $Contact->getOne(intval($response));
	 $Contact_totoro->insertOne($_POST['inputStateContact'], $_POST['inputCiv'],$_POST['nomContact'],$_POST['prenomContact'] , $_POST['telContact'], '', $_POST['faxContact'],$_POST['mailContact'] );
	 echo  json_encode($contact);
 }
 else {
	 echo 'request failed';
 }


}