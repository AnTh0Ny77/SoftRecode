<?php 
namespace App\Apiservice;

require_once  '././vendor/autoload.php';
use App\Database;
use \GuzzleHttp\Client;
use \GuzzleHttp\ClientException;

class ApiTest {


    public static function makeHeaders($token){
        $headers = ['Authorization' => 'Bearer ' .$token, 'Accept' => 'application/json'];
        return $headers;
    }

    public static  function handleResponse($response){
        if($response->getStatusCode() <300){
        return [
        'code' => $response->getStatusCode(),
        'data' => json_decode($response->getBody()->read(163840),true)['data']
        ];
        }
        return [
        'code' => $response->getStatusCode(),
        'msg' => json_decode($response->getBody()->read(16384),true)['msg']
        ];
    }

    public static function login($username, $password){

        $client = new Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/login',  ['json' => ['user__mail' => $username, 'user__password' => $password]]);
        } catch (ClientException $exeption){
            $response = $exeption->getResponse();
        }
        $response =  [
                'code' => $response->getStatusCode(),
                'data' => (array) json_decode($response->getBody()->read(16384), TRUE), 
                'http_errors' => false
            ];

        return $response;
    }

    public static function postTicketLigne(){
        
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/login',  ['json' => ['user__mail' => $username, 'user__password' => $password]]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        $response =  [
            'code' => $response->getStatusCode(),
            'data' => (array) json_decode($response->getBody()->read(16384), TRUE), 
            'http_errors' => false
        ];
    }


    public static function getMateriel($token , $query){

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get('/api/materiel', 
            ['headers' => self::makeHeaders($token) ,
             'query' => $query,
            'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
            exit();
        }
        
        return self::handleResponse($response);
    }


    public static function getClient($token, $query)
    {

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get('/api/client',[
            'headers' => self::makeHeaders($token),
            'query' => $query,
            'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
      
        return self::handleResponse($response);
    }

    public static function getTicketList($token , $query){



        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);

        try {
            $debug = fopen("path_and_filename.txt", "a+");
          
            $response = $client->get('/api/ticket', [
                'headers' => self::makeHeaders($token) ,
                 'query' => $query, 
                 'http_errors' => false
                ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        
        return self::handleResponse($response);
    }
}