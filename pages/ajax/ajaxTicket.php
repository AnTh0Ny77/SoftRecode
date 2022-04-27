<?php
require "./vendor/autoload.php";

session_start();
use App\Tables\UserGroup;
$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);

if (!empty($_POST['id'])) {
        $groups = new UserGroup($Database);
        $array_results = $groups->ticket_notifier($_SESSION['user']->id_utilisateur);
        echo  json_encode($array_results);
    } else {
        echo json_encode($_POST);
}

