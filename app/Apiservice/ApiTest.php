<?php 
namespace App\Apiservice;

require_once  '././vendor/autoload.php';
use App\Database;
use App\Tables\Client as TablesClient;
use \GuzzleHttp\Client;
use App\Controller\BasicController;
use \GuzzleHttp\ClientException;
use ZipArchive;

class ApiTest extends BasicController {


    public static function makeHeaders($token){
        $headers = ['Authorization' => 'Bearer ' .$token, 'Accept' => 'application/json'];
        return $headers;
    }

    public static  function handleResponse($response){
        if($response->getStatusCode() <300){
        return [
        'code' => $response->getStatusCode(),
        'data' => json_decode($response->getBody()->read(16384087),true)['data'] , 
        'http_errors' => false
        ];
        }
       
        return [
        'code' => $response->getStatusCode(),
        'msg' => json_decode($response->getBody()->read(16384),true)['msg'] , 
        'http_errors' => false
        ];
    }

    public static function refresh($refreshToken){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post($env_uri .'/refresh',  ['json' => ['refresh_token' => $refreshToken]]);
        } catch (ClientException $exeption){
            $response = $exeption->getResponse();
        }
       return  $response =  [
            'code' => $response->getStatusCode(),
            'token' => (array) json_decode($response->getBody()->read(16384), TRUE)
        ];
    }

    public static function login($username, $password){
        $config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post($env_uri . '/login',  ['json' => ['user__mail' => $username, 'user__password' => $password]]);
        } catch (ClientException $exeption){
            $response = $exeption->getResponse();
        }
        $response =  [
                'code' => $response->getStatusCode(),
                'data' => (array) json_decode($response->getBody()->read(16384), TRUE)
            ];

        return $response;
    }


    public static function getListFiles($token , $id_ligne){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get(
                $env_uri . '/files', 
            ['headers' => self::makeHeaders($token) ,
             'query' =>  ['tkl__id' => $id_ligne  , 'list' => true]
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
            exit();
        }
        return $response->getBody()->read(12048);  
    }

    public static function getFiles($token , $id_ligne , $name){
        $config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get(
                $env_uri . '/files',
                [
                    'headers' => self::makeHeaders($token),
                    'query' =>  ['tkl__id' => $id_ligne, 'name' => $name]
                ]
            );
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
            exit();
        }

        return $response->getBody()->getContents();
    }

    public static function postFile($token, $files , $ligne){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post(
                $env_uri . '/files',  
            ['headers' => self::makeHeaders($token),
             'multipart' => [
                [
                    'name' =>  'tkl__id',
                    'contents' => $ligne] ,
                [
                    'name' =>  'file',
                    'contents' => $files]
                ]]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        return self::handleResponse($response);
    }

    public static function postTicketLigne($token , $ligne){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post(
                $env_uri . '/ticketligne',  
            ['headers' => self::makeHeaders($token)  ,
             'json' => $ligne]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        return self::handleResponse($response);
    }

 
    public static function postTicketLigneChamps($token , $ligne){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post(  $env_uri . '/ticketchamps',  
            ['headers' => self::makeHeaders($token)  ,
             'json' => $ligne]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        
        return self::handleResponse($response);
    }

    public static function getMateriel($token , $query){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get(  $env_uri . '/materiel', 
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

    public static function postRelation($token, $json){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post( $env_uri . '/usersites', 
            ['headers' => self::makeHeaders($token),
            'json' =>  $json,
            'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
            exit();
        }
        
        return self::handleResponse($response);
    }

    public static function getPromo($token ){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' =>$base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get(
                $env_uri .  '/add', 
            ['headers' => self::makeHeaders($token) ,
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
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get($env_uri . '/client',[
            'headers' => self::makeHeaders($token),
            'query' => $query,
            'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
      
        return self::handleResponse($response);
    }

    public static function updateTicket($token , $json){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->put(
                $env_uri . '/ticket',  
            ['headers' => self::makeHeaders($token)  ,
             'json' => $json]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        return self::handleResponse($response);
    }

    public static function getTicketList($token , $query){
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri'=> $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
          
            $response = $client->get($env_uri . '/ticket', [
                'headers' => self::makeHeaders($token) ,
                 'query' => $query, 
                 'http_errors' => false
                ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }  
        return self::handleResponse($response);
    }

    //contient user__password et confirm__key dans le body ///////////////
    public static function PostForgotPassword($token, $body) {
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' =>$base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
          $response = $client->post(  $env_uri .  '/ticket', [
            'headers' => self::makeHeaders($token),
            'json' => $body,
            'http_errors' => false
          ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
          $response = $exeption->getResponse();
        }
        return self::handleResponse($response);
    }

    public static function transfertClient(){
        
        if (empty($_SESSION['user']->refresh_token)) {
            $token = self::login($_SESSION['user']->email , 'test');
            if ($token['code'] != 200) {
                echo 'Connexion LOGIN à L API IMPOSSIBLE';
                die();
            }
            $_SESSION['user']->refresh_token = $token['data']['refresh_token'] ; 
            $token =  $token['data']['token'];
        }else{
            $refresh = self::refresh($_SESSION['user']->refresh_token);
            if ( $refresh['code'] != 200) {
                echo 'Rafraichissemnt de jeton API IMPOSSIBLE';
                die();
            }
            $token =  $refresh['token']['token'];
        }
        if (empty($_POST['client__id'])) {
            header('location: search_switch');
            die();
        }
        $database = new Database('devis');
        $database->DbConnect();
        $clientTable = new TablesClient($database);
        $clientSoft = $clientTable->getOne($_POST['client__id']);

        //on affiche le formulaire : 
        if(empty($_POST['user__mail'])){
            self::init();
            self::security();
            return self::$twig->render(
                'transfertMyRecode.html.twig',[
                    'user' => $_SESSION['user'] , 
                    'client' =>  $clientSoft
                ]
            );
        }
        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
        $base_uri = $config->api->host;
        $env_uri = $config->api->env_uri;
        $client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        $body = [
            "cli__id" => $clientSoft->client__id,
            "cli__nom" => $clientSoft->client__societe,
            "cli__id_mere" => $clientSoft->client__id,
            "cli__adr1" => $clientSoft->client__adr1,
            "cli__adr2" => $clientSoft->client__adr2,
            "cli__cp" => $clientSoft->client__cp,
            "cli__ville" => $clientSoft->client__ville,
            "cli__pays" => $clientSoft->client__pays,
            'cli__tel' => $clientSoft->client__tel
        ];
        try {
            $response = $client->post($env_uri . '/transfert', [
                'headers' => self::makeHeaders($token),
                'json' => $body,
                'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }

        $body_client = [
            "user__password" => $_POST['user__password'],
            "user__mail" => $_POST['user__mail'], 
            "user__nom" => $_POST['user__nom'] , 
            "user__prenom" => $_POST['user__prenom'] , 
            "user__fonction" => $_POST['user__fonction'] 
        ];

        try {
            $response = $client->post($env_uri . '/user', [
                'headers' => self::makeHeaders($token),
                'json' => $body_client,
                'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        } 

        $response = self::handleResponse($response);
     
        $body = [
            'luc__user__id' => intval($response['data']) , 
            'luc__cli__id' => intval($clientSoft->client__id) , 
            'luc__order' => 1 
        ];
        $response = self::postRelation($token,  $body);
        $_SESSION['transfert'] = ' Opérations effectuées avec succès !';
        $_SESSION['search_switch'] = $clientSoft->client__id ;
        header('location: search_switch');
        die();
    }
}