<?php
require "./vendor/autoload.php";
require "./App/twigloader.php";
session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$Client = new \App\Tables\Client($Database);
$Contact = new \App\Tables\Contact($Database);
$Keyword = new \App\Tables\Keyword($Database);
$Cmd = new \App\Tables\Cmd($Database);
$Stats = new App\Tables\Stats($Database);
$_SESSION['user']->commandes_cours = $Stats->get_user_commnandes($_SESSION['user']->id_utilisateur);


//URL bloqué si pas de connexion :
if (empty($_SESSION['user']->id_utilisateur)) 
{
        header('location: login');
}


//switch sur la variable de recherche : 
if (!empty($_GET['id_user'])) 
{
        $cmd_list =  $Cmd->get_user_status($_GET['id_user'],$_GET['status'] );
        foreach ($cmd_list as $cmd) {
                $date =  new DateTime($cmd->cmd__date_devis);
                $cmd->cmd__date_devis =  $date->format('d/m/Y');
        }
        // Donnée transmise au template : 
        echo $twig->render(
                'consult_client_list.twig',
                [
                        'user' => $_SESSION['user'],
                        'commande_list' => $cmd_list
                ]
        );
      

}
else
{
        header('location: dashboard');
}

