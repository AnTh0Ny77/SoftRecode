<?php
require "./vendor/autoload.php";

session_start();
use App\Tables\UserGroup;
$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);

if (!empty($_POST['id'])) {
        $groups = new UserGroup($Database);
        $array_results["1"] = $groups->get_ticket_for_user($_SESSION['user']->id_utilisateur);
        $array_results["2"] = $groups->get_all_ticket_for_user($_SESSION['user']->id_utilisateur , 0);

        echo  json_encode($array_results);
    } else {
        echo json_encode($_POST);
}

