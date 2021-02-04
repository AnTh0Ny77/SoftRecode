<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new App\Tables\Client($Database);
$Article = new App\Tables\Article($Database);

// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) 
{
        header('location: login');
} 
else 
{        
        // requete table client:
        if (!empty($_POST['idfmm'])) 
        {
                $response_article = json_encode($Article->get_article_devis($_POST['idfmm']));
                echo $response_article;
        } 
}
