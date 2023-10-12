<?php 
namespace App\Apiservice;

require_once  '././vendor/autoload.php';
use App\Database;
use App\Tables\Client as TablesClient;
use \GuzzleHttp\Client;
use App\Controller\BasicController;
use \GuzzleHttp\ClientException;
use ZipArchive;

class ApiGenerique extends BasicController {

    public static function makeHeaders($token){
		$headers = ['Authorization' => 'Bearer ' .$token, 'Accept' => 'application/json'];
		return $headers;
	}

	public static  function handleResponse($response , $http_error){
		
		if($response->getStatusCode() <300){
			return [
			'code' => $response->getStatusCode(),
			'data' => json_decode($response->getBody()->read(16384087),true)['data'] , 
			'http_errors' => $http_error
			];
		}
		
            return [
            'code' => $response->getStatusCode(),
            'data' => json_decode($response->getBody()->read(16384),true)['msg'] , 
            'http_errors' => $http_error
            ];
	}

    public static function Build( string $method, string $url, string $token, array $params  , bool $http_error ){

        $config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);

        switch ($method) {
            case 'POST':

                try { $response = $client->post( $env_uri . $url,  ['headers' => self::makeHeaders($token), 'json' => $params ]);} 
                catch (GuzzleHttp\Exception\ClientException $exeption) {$response = $exeption->getResponse();}
                return self::handleResponse($response , $http_error);
                break; 

            case 'GET':

                try { $response = $client->get( $env_uri . $url ,  ['headers' => self::makeHeaders($token), 'query' => $params ]);} 
                catch (GuzzleHttp\Exception\ClientException $exeption) {$response = $exeption->getResponse();}
                return self::handleResponse($response , $http_error);
                break;
        }
    }

}