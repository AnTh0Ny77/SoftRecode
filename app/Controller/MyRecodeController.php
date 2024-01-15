<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Keyword;
use App\Tables\User;
Use App\Tables\UserGroup;
Use App\Tables\TotoroRequest;
Use App\Totoro;
use DateTime;
use App\Tables\Tickets;
use App\Apiservice\ApiTest;


class MyRecodeController extends BasicController {
    public static function displayList(){
        self::init();
        self::security();
        $Api = new ApiTest();
        $groups = new UserGroup(self::$Db);
        $me = false ;
        $token =  $Api->handleSessionToken2();

        // $test = $Api->getFiles($token , 105333 , 'scoreJPG.jpg');
        // var_dump($test);
        // die();
        // return var_dump($test);
        // die();
        // $_SESSION['user']->refreshToken = $token['data']['refresh_token'];
        // $token = self::handleToken($Api);
        $query_exemple = [
            'tk__id' =>  [],
            'tk__groupe' => [] ,
            'tk__lu' => [], 
            'tkl__user_id_dest' => [],
            'tk__motif' => ['TKM'] , 
            'search' => '', 
            'RECODE__PASS' => "secret"
        ];
        if (!empty($_GET)){
            if (!empty($_GET['tk__lu'])) {
                foreach ($_GET['tk__lu']  as  $value) {
                    array_push($query_exemple['tk__lu'], $value);
                }
            }
            if (!empty($_GET['search'])) {
                    if (is_numeric($_GET['search']) and strlen($_GET['search']) ==  5 ) {
                        array_push( $query_exemple['tk__id'] , $_GET['search']);
                    }
                    elseif (is_numeric($_GET['search']) and strlen($_GET['search']) ==  4 ) {
                        array_push( $query_exemple['tk__groupe'] ,$_GET['search']);
                    }else{
                        $query_exemple['search'] = $_GET['search'] ;
                    }
            }

            if(!empty($_GET['AuthorFilter'])){
                if ($_GET['AuthorFilter'] == 2 ){
                    $groups_array = $groups->get_groups($_SESSION['user']->id_utilisateur);
                        if (!empty($groups_array)) {
                            foreach ($groups_array as  $value) {
                                array_push( $query_exemple['tkl__user_id_dest'] ,$value->id_groupe);            
                            }
                        }
                    array_push( $query_exemple['tkl__user_id_dest'] ,$_SESSION['user']->id_utilisateur);       
                    $me = true ;   
                }
            }
        }
        if (!empty($_GET['nonLu'])) {
            $nonLus = [
                'tk__id' => $_GET['nonLu'] , 
                'tk__lu' => 3
            ];
            $Api->updateTicket($token , $nonLus);
            header('location: myRecode');
            die();
        }
        $query_exemple['RECODE__PASS'] = "secret" ;
        $list = $Api->getTicketList($token , $query_exemple);
        $list = $list['data'];
        $definitive_edition = [];
        $t_lu = 0;
        $t_nlu = 0;
        $t_clo = 0 ;

        foreach ($list as $ticket){
            switch ($ticket['tk__lu']) {
                case 5:
                    $t_lu ++;
                    break;
                case 9:
                    $t_clo ++;
                    break;
                default:
                    $t_nlu ++;
                    break;
            }
            $ticket['user'] = reset($ticket['lignes']);
            $ticket['user'] = $ticket['user']['tkl__user_id'];
            $ticket['dest'] = end($ticket['lignes']);
            $ticket['last'] =  $ticket['dest']['tkl__user_id'];
            $ticket['dest'] =  $ticket['dest']['tkl__user_id_dest'];
            $ticket['info'] = end($ticket['lignes']);
            $ticket['memo']  =  $ticket['info']['tkl__memo'];
            $mat_request = $Api->getMateriel($token, ['mat__id[]' =>  $ticket['tk__motif_id'] , 'RECODE__PASS' => 'secret']);
            if ($mat_request['code'] == 200) {
                $ticket['mat'] =  $mat_request['data'][0];
                $ticket['cli'] =  $Api->getClient($token, ['cli__id' => $ticket['mat']['mat__cli__id']])['data'];
            }
            $date_time = new DateTime($ticket['info']['tkl__dt']);
			$ticket['date'] = $date_time->format('d/m/Y H:i');
            array_push($definitive_edition , $ticket );
        }
        //////////////////filters
        $filters = [];
        $filters['lu'] = false ;
        $filters['nonLu'] = false;
        $filters['cloture'] = false ;
        $filters['search'] = false ;
        $filters['tk__id'] = false ;
        $filters['tk__groupe'] = false ;
        foreach ($query_exemple['tk__lu'] as $value) {
           switch ( $value) {
               case  3 :
                   $filters['nonLu'] = true;
                   break;
                case  5 :
                    $filters['lu'] = true;
                    break;
                case  9 :
                    $filters['cloture'] = true;
                    break; 
           }
        }
        if (!empty($_GET['search']))
            $filters['search'] = $_GET['search'];
        
        if(!empty($_GET['AuthorFilter'])){
            if ($_GET['AuthorFilter'] == 2 ){
                $def_list = [];
                $temp = [];
                $groups_array = $groups->get_groups($_SESSION['user']->id_utilisateur);
                if(!empty($groups_array)){
                        foreach ($groups_array as  $value) {
                            array_push( $temp ,$value->id_groupe);            
                        }
                }
                array_push($temp,$_SESSION['user']->id_utilisateur); 
                foreach($definitive_edition as $entry){
                    if (in_array($entry['dest'] , $temp)) {
                        array_push($def_list , $entry );
                    }
                }
                $definitive_edition =  $def_list ;
            }
        }
        
        $total = count($definitive_edition);
        if ($total  == 0  and  $t_lu > 0 ) {
            $nb_resultats = $total . ' RESULTATS' ;
        }else{
            $nb_resultats = $t_nlu . ' NON LUS - ' . $t_lu . ' LUS - ' . $t_clo . ' CLOTURES SUR ' . $total . ' RESULTATS' ;
        }
      
        return self::$twig->render(
            'display_ticket_myrecode_list.html.twig',[
                'user' => $_SESSION['user'],
                'list' => $definitive_edition ,
                "me" => $me , 
                'filters' => $filters , 
                'results' => $nb_resultats
            ]
        );
    }


    public static function nom_fichier_propre($nom_fichier){
        $nom_fichier = trim($nom_fichier);
        $nom_fichier = str_replace(" ",          '_', $nom_fichier);
        $nom_fichier = str_replace("-",          '_', $nom_fichier);
        $nom_fichier = str_replace("'",          '_', $nom_fichier);
        $nom_fichier = str_replace("iso-8859-1", '',  $nom_fichier);
        $nom_fichier = str_replace('=E9',        'e', $nom_fichier);
        $nom_fichier = str_replace('=Q',         '',  $nom_fichier);
        $nom_fichier = str_replace('=',          '',  $nom_fichier);
        $nom_fichier = str_replace('?',          '',  $nom_fichier);
        $search =array('À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ');
        $replace=array('A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y');
        $nom_fichier = str_replace($search, $replace, $nom_fichier); // supprime les accents
        $nom_fichier = preg_replace('/([^_.a-zA-Z0-9]+)/', '', $nom_fichier);
        return $nom_fichier;

    }



    public static function displayTickets(){
        self::init();
        self::security();
        $totoro = new Totoro();
        $totoro->DbConnect();
        $totoro_request = new TotoroRequest($totoro);
        $Users = new User(self::$Db);
        $groups = new UserGroup(self::$Db);
        $Api = new ApiTest();
        $alert = false;
        
        $token =  $Api->handleSessionToken2();
       
        if (!empty($_GET['tk__id'])){
            
            $query_exemple = [
                'tk__id' => [],
                'RECODE__PASS' => "secret"
             ];
            
            if (is_numeric($_GET['tk__id']) and strlen($_GET['tk__id']) ==  5 ){
                array_push( $query_exemple['tk__id'] ,$_GET['tk__id']);
                $query_exemple['RECODE__PASS'] = "secret";
                $list = $Api->getTicketList($token , $query_exemple);
                $list = $list['data'];
                $definitive_edition = [];
                foreach ($list as $ticket){
                    $ticket['user'] = reset($ticket['lignes']);
                    $ticket['user'] = $ticket['user']['tkl__user_id'];
                    $ticket['dest'] = end($ticket['lignes']);
                    $ticket['last'] =  $ticket['dest']['tkl__user_id'];
                    $ticket['dest'] =  $ticket['dest']['tkl__user_id_dest'];
                    $ticket['info'] = end($ticket['lignes']);
                    $ticket['memo']  =  $ticket['info']['tkl__memo'];

                    if ($ticket['tk__lu'] == 9 ) {

                        self::updateTicket($ticket , $token , 9 , $Api );

                    }else{
                        if ($_SESSION['user']->id_utilisateur ==  $ticket['last']  and $ticket['tk__lu'] != 9 ) {
                            self::updateTicket($ticket , $token , 5 , $Api );
                        }
                        $groups_array = $groups->get_groups($_SESSION['user']->id_utilisateur);
                        if (!empty($groups_array)) {
                            foreach ($groups_array as  $value) {
                                    if ( intval($value->id_groupe) ==  intval($ticket['last']) and $ticket['tk__lu'] != 9 ) {
                                        self::updateTicket($ticket , $token , 5 , $Api );
                                    }
                            }
                        }
                    }
                    
                    $mat_request = $Api->getMateriel($token, ['mat__id[]' =>  $ticket['tk__motif_id'] , 'RECODE__PASS' => 'secret']);
                    
                    if ($mat_request['code'] == 200){
                        $ticket['mat'] =  $mat_request['data'][0];
                        $ticket['cli'] =  $Api->getClient($token, ['cli__id' => $ticket['mat']['mat__cli__id']])['data'];
                    }

                    foreach ($ticket['lignes'] as $key  => $entry) {
                        $ticket['lignes'][$key]['logos'] = $Api->les_fichiers('public/img/tickets/'.$entry['tkl__id'] , null);
                    }
                    
                    $date_time = new DateTime($ticket['info']['tkl__dt']);
                    $ticket['date'] = $date_time->format('d/m/Y H:i');
                    array_push($definitive_edition , $ticket);
                }

                if (empty($definitive_edition)){
                  
                    header('location: myRecode');
                    exit;
                }
               
                $ticket = $definitive_edition[0];
                switch ($ticket['mat']['mat__kw_tg']) {
                    case 'AUT':
                        $gar = 'Autre';
                        break;
                    case 'GCO':
                        $gar = 'Garantie constructeur';
                        break;
                    case 'GNO':
                        $gar = 'NON garantie';
                        break;
                    case 'GRE':
                        $gar = 'Garantie RECODE';
                        break;
                    case 'LOC':
                        $gar = 'Location RECODE';
                        break;
                    case 'MNT':
                        $gar = 'Maintenance RECODE';
                        break;
                    default:
                        $gar = 'NON garantie';
                        break;
                }
                $date_sortie = '';
                if (!empty($ticket['mat']['mat__sn'])) {
                    $date_sortie = $totoro_request->get_sortie_sn($ticket['mat']['mat__sn']);
                    
                    if (!empty($date_sortie)) {
                        if (!empty($date_sortie['sortie'])) {
                            $date_sortie = new DateTime($date_sortie['sortie']);
                            $date_sortie = $date_sortie->format('d/m/Y');
                            $date_sortie =  'Dernière sortie le ' . $date_sortie;
                        }else{
                            $date_sortie = '';
                        }
                    }
                }
                $ticket['lignes'][0]['entities'][0] = [
                    "gar" => $gar ,
                    'dateof' => $ticket['mat']['mat__date_offg'],
                    "name" => $ticket['mat']['mat__model'], 
                    "label" => $ticket['mat']['mat__pn'], 
                    "sortie" => $date_sortie ,
                    'bl' => $ticket['mat']['mat__idnec'],
                    'dt_off' => date("d/m/Y", strtotime($ticket['mat']['mat__date_offg'])),
                    "additionals" => $ticket['mat']['mat__sn'], 
                    "alternative" => "public/img/pn2.jpg",
                ];
                $ticket['lignes'][0]['entities'][1] = [
                    "identifier" => $ticket['cli']['cli__id'],
                    "name" => $ticket['cli']['cli__nom'], 
                    "label" => $ticket['cli']['cli__adr1'], 
                    "additionals" => $ticket['cli']['cli__cp'] . ' ' . $ticket['cli']['cli__ville'] , 
                    "alternative" => "public/img/client_image.png"
                ];

                if (!empty($_POST['tk__id']) and !empty($_POST['what'])){ 
                    switch ($_POST['what']) {
                        case 'RPD':
                            $dest = intval($ticket['last']['user__id']);
                            break;
                        case 'RPC':
                            $dest = intval($ticket['user']['user__id']);
                            $_POST['what'] = 'RPD';
                            break;
                        case 'CIN':
                            $dest = intval($_POST['dest']);
                            break;
                        case 'CLO':
                            $dest = intval($ticket['user']['user__id']);
                            break;
                    }

                    if (!empty($_FILES)){
                        $fileName = self::nom_fichier_propre($_FILES['file']['name']);
                        $tempPath = $_FILES['file']['tmp_name'];
                        $fileSize = $_FILES['file']['size'];
                       
                        $fileExtension = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
                        $validExtension = array('jpeg','jpg','png','gif','pdf','txt');
                        if (!in_array($fileExtension, $validExtension) and $fileSize > 111) {
                            $_SESSION['file_alert']  = '  Merci de télécharger un fichier au format : jpeg , jpg , png , gif , pdf ou txt';
                            header('location: myRecode-ticket?tk__id='.$_GET['tk__id']);
                            die();
                        }
                        if ($fileSize > 10000000) {
                             $_SESSION['file_alert']  = 'Fichier trop volumineux';
                             header('location: myRecode-ticket?tk__id='.$_GET['tk__id']);
                             die();
                        }
                    }
                   
                        $id_ligne =  self::PostLigne($_POST ,$dest , $Api, $token);
                        $ticket = self::PostChamps($id_ligne,$_POST,$Api,$token);


                    if ($fileSize > 111){
                        move_uploaded_file($tempPath, __DIR__ .'/' .$fileName);
                        $file = $Api->postFile($token, fopen(__DIR__ . '/' .$fileName , 'r') ,$id_ligne);
                        unlink(__DIR__ .'/' .$fileName);
                    }
                    header('location: myRecode');
                    exit;
                }

                if (!empty($_SESSION['file_alert'])){
                    $alert = $_SESSION['file_alert'];
                    $_SESSION['file_alert'] = '';
                }

                return self::$twig->render(
                    'display_ticket_myrecode.html.twig',[
                        'user' => $_SESSION['user'],
                        'ticket' => $ticket , 
                        'users_list' => $Users->getAll() , 
                        'alert' => $alert
                    ]
                );
            }else{
                header('location: myRecode');
                exit;
            }
        }else{
            header('location: myRecode');
            exit;
        }
    }

    public static function PostLigne($post , $dest , $api , $token){
        $visible = 0 ;
        if ($post['what'] == 'CIN') {
            $visible = 1 ;
        }
        $tlk_memo = 'Réponse Recode';
        switch ($post['what']) {
            case 'CIN':
                $tlk_memo = 'Echange interne';
                break;
            case 'RPC':
                $post['what'] ='RPD';
                break;
            case 'CLO':
                $tlk_memo = 'Cloture du ticket';
                break;
        }
        $tkl = [
            'tkl__tk_id' => $post['tk__id'],
            'tkl__motif_ligne' => $post['what'], 
            'tkl__memo' => $tlk_memo ,
            'tkl__user_id' => $_SESSION['user']->id_utilisateur,
            'tkl__user_id_dest' => $dest , 
            'tkl__visible' => $visible
        ];  
        
        return $api->postTicketLigne($token,  $tkl)['data']['tkl__id'];
    }

    public static function PostChamps($ligne , $post , $api , $token){
        $tklc = [
            'tklc__id' =>  $ligne, 
            'tklc__nom_champ' => 'INF', 
            'tklc__ordre' =>  1 , 
            'tklc__memo' => $post['content']
        ];
        return $api->postTicketLigneChamps($token , $tklc);
    }

    public static function updateTicket($ticket , $token , $lu , $api){
        $ticket['tk__lu'] = $lu ;
        return $api->updateTicket($token , $ticket);
    }
}