<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Keyword;
use App\Tables\User;
use DateTime;
use App\Tables\Tickets;
use App\Apiservice\ApiTest;
use App\Tables\Article;
use App\Database;


class MyRecodeBoutiqueController extends BasicController {

    public static function displayList(){
        self::init();
        self::security();
        $Database = new Database('devis');
        $Database->DbConnect();
        $Api = new ApiTest();
        $Article = new Article($Database);

        if (empty($_SESSION['user']->refresh_token)) {
            $token = $Api->login($_SESSION['user']->email , 'test');
            if ($token['code'] != 200) {
                echo 'Connexion LOGIN à L API IMPOSSIBLE';
                die();
            }
            $_SESSION['user']->refresh_token = $token['data']['refresh_token'] ; 
            $token =  $token['data']['token'];
        }else{
            $refresh = $Api->refresh($_SESSION['user']->refresh_token);
            if ( $refresh['code'] != 200) {
                echo 'Rafraichissemnt de jeton API IMPOSSIBLE';
                die();
            }
            $token =  $refresh['token']['token'];
        }

        $list = $Article->get_pn_for_myrecode();

      

    //    foreach ($list as $key => $value) {
    //     $pn = $Article->get_pn_by_id_api($value->apn__pn);
    //     if(!empty($pn)){
    //             if (!empty($pn->apn__image)) {
    //                 $extension = self::getImageMimeType(base64_decode($pn->apn__image));
    //                 file_put_contents('public/img/boutique/' . $pn->apn__pn . '.' . $extension, base64_decode($pn->apn__image));
    //                 $name = $pn->apn__pn . '.' . $extension;
    //             }
    //             $body = [
    //                 'sar__model' => $pn->apn__pn_long,
    //                 'sar__ref_constructeur' => $pn->apn__pn_long,
    //                 'sar__description' => $pn->apn__desc_short,
    //                 'sar__marque' => $pn->marque,
    //                 'sar__famille' => $pn->fam,
    //                 'sar__image' => $name
    //             ];

    //             $response  = $Api->postShopArticle($token, $body);
    //     }
    //    }
    //    die();

        return self::$twig->render(
            'display_boutique_myrecode.html.twig',[
                'user' => $_SESSION['user'] , 
                'pn_list' => $list 
            ]
        );
    }

    public static function getBytesFromHexString($hexdata)
    {
        for ($count = 0; $count < strlen($hexdata); $count += 2)
            $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

        return implode($bytes);
    }

    public static function getImageMimeType($imagedata)
    {
        $imagemimetypes = array(
            "jpeg" => "FFD8",
            "png" => "89504E470D0A1A0A",
            "gif" => "474946",
            "bmp" => "424D",
            "tiff" => "4949",
            "tiff" => "4D4D"
        );

        foreach ($imagemimetypes as $mime => $hexbytes) {
            $bytes = self::getBytesFromHexString($hexbytes);
            if (substr($imagedata, 0, strlen($bytes)) == $bytes)
                return $mime;
        };

        return null;
    }  

}