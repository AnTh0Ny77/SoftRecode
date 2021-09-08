<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Keyword = new \App\Tables\Keyword($Database);
$Cmd = new \App\Tables\Cmd($Database);
$Pistage = new App\Tables\Pistage($Database);
$Article = new App\Tables\Article($Database);
$General = new App\Tables\General($Database);
$Stocks = new App\Tables\Stock($Database);

//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) {
    header('location: login');
}



switch ($_SERVER['REQUEST_URI']) {
    case "/SoftRecode/recherche-articles-familles":

        $famille_list = $Article->getFAMILLE();

        echo $twig->render(
            'recherches_famille.twig',
            [
                'user' => $_SESSION['user'],
                'famille_list' => $famille_list
            ]
        );
        break;

    case "/SoftRecode/recherche-articles-specs":

            if (empty($_POST['famille'])) 
            {
                header('location: recherche-articles-specs');
                break;
            }
            else
            {

                $forms_data = $Stocks->get_famille_forms($_POST['famille']);
                $object = $Keyword->get_kw_by_typeAndValue('famil', $_POST['famille'] );

                echo $twig->render(
                    'recherches_specs.twig',
                    [
                        'user' => $_SESSION['user'],
                        'forms_data' => $forms_data,
                        'object' => $object

                    ]
                );
                break;
            }

            
        
}
