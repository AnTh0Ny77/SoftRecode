<?php
namespace App\Api;
require_once  '././vendor/autoload.php';
use DateTime;
use App\Database;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use App\Methods\Pdfunctions;
use App\Methods\Devis_functions;
use App\Tables\Cmd;
use App\Tables\Contact;
use App\Apiservice\ApiTest;
use App\Api\ResponseHandler;
use App\Tables\Article;


class ApiBoutique{

    public static  function index($method){
        $responseHandler = new ResponseHandler;
        switch ($method) {
            case 'POST':
                return self::post();
                break;
            default:
                return $responseHandler->handleJsonResponse([
                    'msg' =>  'Aucune opération n est prévue avec cette méthode'
                ], 404, 'Unknow');
                break;
        }
    }


    public static function post(){
        $Database = new Database('devis');
        $Database->DbConnect();
        $Api = new ApiTest();
        $Article = new Article($Database);
        $responseHandler = new ResponseHandler;
        
        $token =  $Api->handleSessionToken2();

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['apn__pn'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  'apn__pn non renseigné'
            ], 404, 'Unknow');
        }

        $pn = $Article->get_pn_by_id_api($body['apn__pn']);

        if (empty($pn)) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  'pn inconu'
            ], 404, 'Unknow');
        }

        $name = '';
        if (!empty($pn->apn__image)) {
            $extension = self::getImageMimeType(base64_decode($pn->apn__image));
            file_put_contents('public/img/boutique/'.$pn->apn__pn. '.' .$extension ,base64_decode($pn->apn__image));
            $name = $pn->apn__pn. '.' .$extension;
        }

        $body = [
            'sar__model' => $pn->apn__pn_long , 
            'sar__ref_constructeur' => $pn->apn__pn_long , 
            'sar__description' => $pn->apn__desc_short  , 
            'sar__marque' => $pn->marque , 
            'sar__famille' => $pn->fam , 
            'sar__image' => $name
        ];

        $response  = $Api->postShopArticle($token , $body );

        return $responseHandler->handleJsonResponse([
            'data' =>  $response
        ], 200, 'Request send');
    
    }

    public static function getBytesFromHexString($hexdata){
        for($count = 0; $count < strlen($hexdata); $count+=2)
            $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

        return implode($bytes);
    }

    public static function getImageMimeType($imagedata){
        $imagemimetypes = array( 
            "jpeg" => "FFD8", 
            "png" => "89504E470D0A1A0A", 
            "gif" => "474946",
            "bmp" => "424D", 
            "tiff" => "4949",
            "tiff" => "4D4D"
        );

        foreach ($imagemimetypes as $mime => $hexbytes){
            $bytes = self::getBytesFromHexString($hexbytes);
            if (substr($imagedata, 0, strlen($bytes)) == $bytes)
            return $mime;
        };

        return null;
    }  

}