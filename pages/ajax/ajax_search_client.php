<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new App\Tables\Client($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) 
{
    header('location: login');
}

else 
{
    // requete table client:
    if (!empty($_POST['search']))
    {
        $search =  htmlentities($_POST['search']);
        $clientList = json_encode($Client->search_client($search));
        echo $clientList;  
    }

    else 
    {
        $clientList = [];
        $clientList = json_encode($clientList);
        echo  $clientList;
    }

}