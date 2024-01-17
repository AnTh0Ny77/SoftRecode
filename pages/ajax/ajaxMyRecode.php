<?php
require "./vendor/autoload.php";

session_start();
use App\Tables\UserGroup;
$Database = new App\Database('devis');
$Database->DbConnect();
$Article = new App\Tables\Article($Database);
use App\Apiservice\ApiTest;
$Api = new ApiTest();

if (!empty($_POST['id'])) {
        $groups = new UserGroup($Database);
        $token = $Api->handleSessionToken2();
        $user = $Api->getMyRecodeUser($token);
        
        $groups = $user['data']['user__groups'];
        $query_exemple = [
            'tk__id' =>  [],
            'tk__groupe' => [] ,
            'tk__lu' => [ 3 , 5 ], 
            'tkl__user_id_dest' => $user['data']['user__groups'],
            'tk__motif' => ['TKM'] , 
            'search' => '', 
            'RECODE__PASS' => "secret"
        ];
        $list = $Api->getTicketList($token , $query_exemple);
        $list = $list['data'];

        $non_lu = array_filter($list, function ($item) {
            return isset($item['tk__lu']) && $item['tk__lu'] == 3;
        });
        $lu = array_filter($list, function ($item) {
            return isset($item['tk__lu']) && $item['tk__lu'] == 5;
        });
        
        $array_results["1"]  = count($non_lu);
        $array_results["2"] = count($lu) + count($non_lu) ;
        
        echo  json_encode($array_results);
        // $array_results["1"] = $groups->get_myRecode_for_user($_SESSION['user']->id_utilisateur);
        // $array_results["2"] = $groups->get_all_MyRecode_for_user($_SESSION['user']->id_utilisateur , 0);

       
    } else {
        echo json_encode($_POST);
}

