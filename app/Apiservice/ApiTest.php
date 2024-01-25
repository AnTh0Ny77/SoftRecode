<?php 
namespace App\Apiservice;

require_once  '././vendor/autoload.php';
use App\Database;
use App\Tables\Client as TablesClient;
use \GuzzleHttp\Client;
use App\Controller\BasicController;
use \GuzzleHttp\ClientException;
use ZipArchive;


if (session_status() === PHP_SESSION_NONE) {
	session_start();
  }


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

	

	public static function handleSessionToken2(){

		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		
		$backdoor = $config->api->backdoor;

		if (property_exists($_SESSION['user'], 'refresh_token')) {

			$refresh = self::refresh($_SESSION['user']->refresh_token);

			$message = $refresh['code'] != 200 ? true : false;
			
			if ($message){

				$token = self::login('anthony@recode.fr',$backdoor);

				$message = $token['code'] != 200 ? true : false;

				if ($message) return $message ;
		
				$_SESSION['user']->refresh_token = $token['data']['refresh_token']; 
		
				return  $token['data']['token'];
			}
			
            return  $refresh['token']['token'];
		}
		
        $token = self::login('anthony@recode.fr',  $backdoor );

        $message = $token['code'] != 200 ? true : false;

		if ($message) return $message ;

        $_SESSION['user']->refresh_token = $token['data']['refresh_token']; 

        return  $token['data']['token'];
	}

	public static function refresh($refreshToken){
		$config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false) , 'http_errors' => false]);
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

	public static function getPlanning($token){
        $config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false) , 'http_errors' => false]);
		try {
			$response = $client->get(
				$env_uri . '/planning' , 
				['headers' => self::makeHeaders($token) ]
				
			);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
			$response = $exeption->getResponse();
			exit();
		}
		return $response->getBody()->read(12047878);
    }

	public static function login($username, $password){
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false) , 'http_errors' => false]);
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


	public static function getShopConditions($token, $sco__cli_id)
	{
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->get(
				$env_uri . '/ShopConditions',
				[
					'headers' => self::makeHeaders($token),
					'query' =>  ['sco__cli_id' => $sco__cli_id]
				]
			);
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

	function nom_fichier_propre($nom_fichier){
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
			['headers' => self::makeHeaders($token),
			 'json' => $ligne]);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
			$response = $exeption->getResponse();
		}
		return self::handleResponse($response);
	}


	public static function postMachine($token , $ligne){
		$config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		
		try {
			$response = $client->post(
				$env_uri . '/materiel',  
			['headers' => self::makeHeaders($token),
			 'json' => $ligne,
			 'http_errors' => false]);
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


	public static function getAdd($token , $query){
		$config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->get(  $env_uri . '/add', 
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

	public static function getShopVendre($token, $json){
		$config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->post( $env_uri . '/boutiqueSossuke', 
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

	public static function postShopArticle($token, $json){
		$config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->post( $env_uri . '/ShopArticle', 
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

	public static function createGet($path,$token,$query)
	{
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->get($env_uri . $path, [
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

	
	// les parapametres de query peuvent etre :  sar__famille[] , search ( texte de recherche )  , sav__id[] qui est la uniquement pour les recherche par id , les sav__cli_id[] sont retrouvés uniquement a partir des client dispo pour l utilisateur connecté 
	//exemple de query  [
	//        'sar__famille[]' => 'ITH' ,
	//         'search'  => 'test de recherche'
	//]
	public static function getShopAVendre($token, $query){
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {

			$response = $client->get($env_uri . '/ShopAVendre', [
				'headers' => self::makeHeaders($token),
				'query' => $query,
				'http_errors' => false
			]);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
			$response = $exeption->getResponse();
		}
		return self::handleResponse($response);
	}

	//contient post une shopCMd /
	// pas besoin de préciser la date et le user ( auto ) pour reste respecter le champs de la base de donnée dans le body : ex :
	// $body = [ 'scm__client_id_livr' => 213458 , 'scm__client_id_fact' => 5166546 , 'scm__prix_port' => 32.59 , 'scm__ref_client' => 'blablabla ]; 
	public static function PostShopCmd($token, $body)
	{
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->post($env_uri .  '/Shopcmd', [
				'headers' => self::makeHeaders($token),
				'json' => $body,
				'http_errors' => false
			]);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
			$response = $exeption->getResponse();
		}
		return self::handleResponse($response);
	}

	//contient post une shopCMdLigne /
	// respecter le champs de la base de donnée dans le body : ex :
	// $body = [ 'scl__scm_id' => 213458 , 'scl__ref_id' => 5166546 , 'scl__prix_unit' => 32.59 , 'scl__qte' => 3 ]; 
	public static function PostShopCmdLigne($token, $body)
	{
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->post($env_uri .  '/ShopcmdLigne', [
				'headers' => self::makeHeaders($token),
				'json' => $body,
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


	public static function getMyRecodeUser($token) {
		$config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' =>$base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
		  $response = $client->get(  $env_uri .  '/user', [
			'headers' => self::makeHeaders($token),
			'http_errors' => false
		  ]);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
		  $response = $exeption->getResponse();
		}
		return self::handleResponse($response);
	}

	public static function PostListClient($token,  $one) {
		$config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$body = ["secret" => "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528"] ; 
		if (!empty($one) ) { $body = ["secret" => "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528" ,"one" => $one ];}
		$client = new \GuzzleHttp\Client(['base_uri' =>$base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->post(  $env_uri .  '/sossuke', [
				'headers' => self::makeHeaders($token),
				'json' => $body,
				'http_errors' => false
			]);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
		  $response = $exeption->getResponse();
		}
		return self::handleResponse($response);
	}


	public static function PostListBoutique($token)
	{
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$body = ["secret" => "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528" , 'shop_avendre' => true ];
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->post($env_uri .  '/boutiqueSossuke', [
				'headers' => self::makeHeaders($token),
				'json' => $body,
				'http_errors' => false
			]);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
			$response = $exeption->getResponse();
		}
		return self::handleResponse($response);
	}


	public static function getMatAbn($token , $mat__sn){
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$body = ["secret" => "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528" , 'mat__sn' =>  $mat__sn ];
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->get($env_uri .  '/materielSossuke', [
				'headers' => self::makeHeaders($token),
				'query' => $body
			]);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
			$response = $exeption->getResponse();
		}
		return self::handleResponse($response);
	}

	public static function updateMatAbn($token , $body){
		$config = json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
		try {
			$response = $client->post($env_uri .  '/materielSossuke', [
				
				'json' => $body
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
		// if(empty($_POST['user__mail'])){
		//     self::init();
		//     self::security();
		//     return self::$twig->render(
		//         'transfertMyRecode.html.twig',[
		//             'user' => $_SESSION['user'] , 
		//             'client' =>  $clientSoft
		//         ]
		//     );
		// }
		$config =json_decode(file_get_contents(__DIR__ . '/apiConfig.json'));
		$base_uri = $config->api->host;
		$env_uri = $config->api->env_uri;
		$client = new \GuzzleHttp\Client(['base_uri' => $base_uri, 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);

		if (!empty($clientSoft->client__id_vendeur) and $clientSoft->client__id_vendeur == 78 ) {
			$body = [
				"cli__id" => $clientSoft->client__id,
				"cli__nom" => $clientSoft->client__societe,
				"cli__id_mere" => $clientSoft->client__id,
				"cli__adr1" => $clientSoft->client__adr1,
				"cli__adr2" => $clientSoft->client__adr2,
				"cli__cp" => $clientSoft->client__cp,
				"cli__ville" => $clientSoft->client__ville,
				"cli__pays" => $clientSoft->client__pays,
				'cli__tel' => $clientSoft->client__tel , 
				'cli__com1' => $clientSoft->client__id_vendeur, 
				'cli__com2' => 32
			];
		}else{
			$body = [
				"cli__id" => $clientSoft->client__id,
				"cli__nom" => $clientSoft->client__societe,
				"cli__id_mere" => $clientSoft->client__id,
				"cli__adr1" => $clientSoft->client__adr1,
				"cli__adr2" => $clientSoft->client__adr2,
				"cli__cp" => $clientSoft->client__cp,
				"cli__ville" => $clientSoft->client__ville,
				"cli__pays" => $clientSoft->client__pays,
				'cli__tel' => $clientSoft->client__tel , 
				'cli__com1' => $clientSoft->client__id_vendeur
			];
		}
		
		try {
			$response = $client->post($env_uri . '/transfert', [
				'headers' => self::makeHeaders($token),
				'json' => $body,
				'http_errors' => false
			]);
		} catch (GuzzleHttp\Exception\ClientException $exeption) {
			$response = $exeption->getResponse();
		}

		header('location: displaySocieteMyRecode?cli__id='. $clientSoft->client__id.'');
		die();
	}


	public static function transfertClient2($id , $token){
		
	   
		$database = new Database('devis');
		$database->DbConnect();
		$clientTable = new TablesClient($database);
		$clientSoft = $clientTable->getOne($id);

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
	}

	public static function les_fichiers($dirname, $option=false){
		// recherche les fichiers dans un repertoire
		$icones_fichiers = "";
		$small = $return = $poubelle = $visu = FALSE;
		$fa_taille       = 'fa-2x';
		if (strpos($option, 'VISU')     !== FALSE) $visu = TRUE;
		if (strpos($option, 'SMALL')    !== FALSE) $small = TRUE;
		if (strpos($option, 'RETURN')   !== FALSE) $return = TRUE;
		if (strpos($option, 'POUBELLE') !== FALSE) $poubelle = TRUE;
		if ($small) $fa_taille = '';
		$_SESSION['from_page'] = $_SERVER['REQUEST_URI']; //page d'appel
		$from_page = $_SERVER['REQUEST_URI'];
		if (is_dir($dirname)) // c'est bien un dossier qui existe
		{
			$dir = opendir($dirname);
			while($file = readdir($dir)){
				if($file != '.' && $file != '..' && !is_dir($dirname.$file)){
					if($visu)
					{ // j'affiche l'image et non une iconne
						$icones_fichiers .= '<img src="/'.$dirname.$file.'" width=100 > ';
					}
					else{
						// recerche le type de fichier pour y mettre le bon icone.
						$nom_ico = '<i class="fa fa-file-o '.$fa_taille.'"></i>'; // par defaut
						$pos = (strlen($file) - (strrpos($file, '.') + 1));  // recherche le dernier . et renvoie la long de l'extention
						$extention = strtoupper(substr($file,$pos*-1)); // retourne les derniers caracteres apres le .
						switch ($extention)
						{
							case "DOC": case "DOCX":        $nom_ico = '<i class="fas fa-file-word       '.$fa_taille.'"></i>'; break;
							case "XLS": case "XLSX":        $nom_ico = '<i class="fas fa-file-excel      '.$fa_taille.'"></i>'; break;
							case "JPG": case "JPEG":        $nom_ico = '<i class="fas fa-image      '.$fa_taille.'"></i>'; break;
							case "GIF": case "PNG":            $nom_ico = '<i class="fas fa-image      '.$fa_taille.'"></i>'; break;
							case "PDF":                        $nom_ico = '<i class="fas fa-file-pdf        '.$fa_taille.'"></i>'; break;
							case "PPT":                        $nom_ico = '<i class="fas fa-file-powerpoint '.$fa_taille.'"></i>'; break;
							case "ZIP":                        $nom_ico = '<i class="fas fa-file-zipper     '.$fa_taille.'"></i>'; break;
						}
						$icones_fichiers .= '<a href="'.$dirname. '/' .$file.'" target=_blank data-toggle=tooltip title="'.$file.'">'.$nom_ico.'</a>&nbsp;';
					}
					if ($poubelle) $icones_fichiers .= "<a href='supprim_fic.php?file=/$dirname".$file."&from=$from_page'><span class='text-warning glyphicon glyphicon-trash' aria-hidden=true></span></a> - ";
				}
			}
			closedir($dir);
		} // je ne fait rien si ce n'est pas un dossier (c'est qu'il n'y a pas de fichier en pieces jointes)
		if ($icones_fichiers > "" and $return) $icones_fichiers .= '<br>';
		return $icones_fichiers;
	}
}