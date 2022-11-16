<?php 
namespace App\Apiservice;

require_once  '././vendor/autoload.php';
session_start();
use App\Database;
use GuzzleHttp\Client;

class ApiTest {

    public function handleResponse($response){
        if($response->getStatusCode() <300){
        return [
        'code' => $response->getStatusCode(),
        'data' => json_decode($response->getBody()->read(16384),true)['data']
        ];
        }
        return [
        'code' => $response->getStatusCode(),
        'msg' => json_decode($response->getBody()->read(16384),true)['msg']
        ];
    }

    public static function login($username, $password){

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://82.65.12.112:59085', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/login',  ['json' => ['user__mail' => $username, 'user__password' => $password]]);
        } catch (GuzzleHttp\Exception\ClientException $exeption){
            $response = $exeption->getResponse();
        }
        $response =  [
                'code' => $response->getStatusCode(),
                'data' => (array) json_decode($response->getBody()->read(16384), TRUE)
            ];

        var_dump($response);
    }

    public static function postTicketLigne(){
        
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://82.65.12.112:59085', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/login',  ['json' => ['user__mail' => $username, 'user__password' => $password]]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        $response =  [
            'code' => $response->getStatusCode(),
            'data' => (array) json_decode($response->getBody()->read(16384), TRUE)
        ];
    }
}