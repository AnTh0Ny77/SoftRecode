<?php
namespace App\Api;

require_once  '././vendor/autoload.php';

use App\Api\ResponseHandler;
use \GuzzleHttp\Client;
use \GuzzleHttp\ClientException;

class Demo {

			public static function testFilesRequest(){
				$debug = fopen("path_and_filename.txt", "a+");
			$client = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.1.105:80/',  'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
				try {
					$response = $client->get('api/documents',[
							'query' => [
							'cmd__id' => '29377',
							'cmd__etat' => 'LST',
							'cli__id' => '22089'
							]
						]
					);
				} catch (GuzzleHttp\Exception\ClientException $exeption) {
					$response = $exeption->getResponse();
				}
				
				$data = $response->getBody()->read(10248578);
				return	json_decode($data , true) ;
			}

	
}

