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

        $non_lu = array_filter($non_lu, function ($item) use ($groups) {
            $lastLigne = end($item['lignes']);
            reset($item['lignes']);
            return in_array($lastLigne['tkl__user_id_dest']['user__id'], $groups);
        });

        $lu = array_filter($lu, function ($item) use ($groups) {
            $lastLigne = end($item['lignes']);
            reset($item['lignes']);
            return in_array($lastLigne['tkl__user_id_dest']['user__id'], $groups);
        });
        
        $array_results["1"]  = count($non_lu);
        $array_results["2"] = count($lu) + count($non_lu) ;
        
        echo  json_encode($array_results);
       
    } else {
        echo json_encode($_POST);
}

