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
        $client = new Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/refresh',  ['json' => ['refresh_token' => $refreshToken]]);
        } catch (ClientException $exeption){
            $response = $exeption->getResponse();
        }
       return  $response =  [
            'code' => $response->getStatusCode(),
            'token' => (array) json_decode($response->getBody()->read(16384), TRUE), 
            'http_errors' => false
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


    public static function getFiles($token){

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get('/RESTapi//files', 
            ['headers' => self::makeHeaders($token) ,
             'query' =>  ['tkl__id' => 12117 ],
            'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
            exit();
        }
        $myfile = fopen("test.zip", "w") or die("Unable to open file!");
        fwrite($myfile, $response->getBody()->read(163840708));
        fclose($myfile);
        $zip = new ZipArchive;
        
        // Zip File Name
        if ($zip->open('test.zip') === TRUE) {
        
            // Unzip Path
            $zip->extractTo('public/img');
            $zip->close();
            echo 'Unzipped Process Successful!';
        } else {
            echo 'Unzipped Process failed';
        }
        
    }

    public static function postFile($token, $files , $ligne){

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/files',  
            ['headers' => self::makeHeaders($token),
             'multipart' => [
                 'tkl__id' => $ligne, 
                 'file' => $files
             ]]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        return self::handleResponse($response);
    }

    public static function postTicketLigne($token , $ligne){
        
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/ticketligne',  
            ['headers' => self::makeHeaders($token)  ,
             'json' => $ligne]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        return self::handleResponse($response);
    }

 
    public static function postTicketLigneChamps($token , $ligne){
        
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/ticketchamps',  
            ['headers' => self::makeHeaders($token)  ,
             'json' => $ligne]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        
        return self::handleResponse($response);
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

    public static function postRelation($token, $json){
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->post('/api/usersites', 
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

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->get('/api/add', 
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

    public static function updateTicket($token , $json){

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
            $response = $client->put('/api/ticket',  
            ['headers' => self::makeHeaders($token)  ,
             'json' => $json]);
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

    //contient user__password et confirm__key dans le body ///////////////
    public static function PostForgotPassword($token, $body) {
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        try {
          $response = $client->post('/api/ticket', [
            'headers' => makeHeaders($token),
            'json' => $body,
            'http_errors' => false
          ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
          $response = $exeption->getResponse();
        }
        return handleResponse($response);
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

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
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
            $response = $client->post('/api/transfert', [
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
            $response = $client->post('/api/user', [
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

    public static function transfertClientGpt(){
        // Si l'ID du client n'est pas défini, on redirige vers la page de recherche
        if (empty($_POST['client__id'])) {
            header('location: search_switch');
            die();
        }
        // On récupère le client en question dans la base de données
        $database = new Database('devis');
        $database->DbConnect();
        $clientTable = new TablesClient($database);
        $clientSoft = $clientTable->getOne($_POST['client__id']);
        // Si les informations du nouvel utilisateur n'ont pas été postées, on affiche le formulaire de transfert
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
        //On initialise un client Guzzle pour effectuer des requêtes HTTP
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        // On prépare le corps de la requête avec les informations du client
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
        //On envoie une requête POST à l'URL '/api/transfert' avec les informations du client
        try {
            $response = $client->post('/api/transfert', [
                'headers' => self::makeHeaders($token),
                'json' => $body,
                'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        //On prépare le corps de la requête avec les informations du nouvel utilisateur
        $body_client = [
            "user__password" => $_POST['user__password'],
            "user__mail" => $_POST['user__mail'], 
            "user__nom" => $_POST['user__nom'] , 
            "user__prenom" => $_POST['user__prenom']

        ];
        //On envoie une requête POST à l'URL '/api/user' avec les informations du nouvel utilisateur
        try {
            $response = $client->post('/api/user', [
                'headers' => self::makeHeaders($token),
                'json' => $body_client,
                'http_errors' => false
            ]);
        } catch (GuzzleHttp\Exception\ClientException $exeption) {
            $response = $exeption->getResponse();
        }
        //Si la réponse a un statut supérieur à 300 (erreur), on affiche à nouveau le formulaire de transfert
        if ($response->getStatusCode() > 300){
            self::init();
            self::security();
            return self::$twig->render(
                'transfertMyRecode.html.twig',[
                    'user' => $_SESSION['user'] , 
                    'client' =>  $clientSoft
                ]
            );
        }
        //On affiche les 158962 premiers octets du corps de la réponse
        
        //On enregistre l'ID du client dans la variable de session 'search_switch' et on redirige vers la page de recherche
        $_SESSION['search_switch'] = $clientSoft->client__id ;
        header('location: search_switch');
    }
}