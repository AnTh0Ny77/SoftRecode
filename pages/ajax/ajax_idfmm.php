<?php
require "./vendor/autoload.php";

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new App\Tables\Client($Database);
$Article = new App\Tables\Article($Database);


if (empty($_SESSION['user']->id_utilisateur)) 
{
        header('location: login');
} 
else 
{        
        // requete table client:
        if (!empty($_POST['idfmm'])) 
        {

                $response_article = $Article->get_article_devis($_POST['idfmm']);
                if (!empty($response_article->afmm__image)) 
                {
                        $response_article->afmm__image = base64_encode($response_article->afmm__image); 
                }
                echo json_encode($response_article);
        } 
}
