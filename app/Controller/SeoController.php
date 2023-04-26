<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use \GuzzleHttp\Client;
use \GuzzleHttp\ClientException;
use App\Tables\User;

class SeoController extends BasicController {

    public static function home(){
        self::init();
        self::security();
        $base_uri = 'https://searchconsole.googleapis.com/v1/token';
        $auth_uri = 'https://sts.googleapis.com/v1/token';
        $auth_client = new \GuzzleHttp\Client(['base_uri' => $auth_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $auth = $auth_client->post( 
                '',[ 
                    'json' => [
                        "grantType" =>"urn:ietf:params:oauth:grant-type:token-exchange",
                        "audience" => "//iam.googleapis.com/projects/361909/premium-buckeye/global/workloadIdentityPools/113353974971642481580/providers/113353974971642481580",
                        "scope" => "http://localhost:8080/SoftRecode/seo",
                        "requestedTokenType" => "urn:ietf:params:oauth:token-type:access_token",
                        "subjectToken" => "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",
                        "subjectTokenType" => "urn:ietf:params:oauth:token-type:jwt",
                    ],
                ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $auth= $exeption->getResponse();
        }

        var_dump($auth->getBody()->getContents());
        die();
        
        $json =  [
            "startDate"=> "2022-04-01",
            "endDate"=> "2022-05-01",
            "dimensions"=> ["country","device"]
        ];
        try {
            $response = $client->get( 
                'webmasters/v3/sites',[ 
                    'headers' => [
                        'Authorization' => 'Bearer 972001495621-civq9u6q32v48evsnkeef817d5o2nemj.apps.googleusercontent.com', 
                    ], 
                    'query' => [
                        'OAuth2' => '972001495621-civq9u6q32v48evsnkeef817d5o2nemj.apps.googleusercontent.com' , 
                        'siteUrl' => 'https://recode.fr'
                    ],

                ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        var_dump($response->getBody()->read(12048));
        die();  

        return self::$twig->render(
            'seo.html.twig',[
                'user' => $_SESSION['user']
            ]
        );
    }
}