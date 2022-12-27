
<?
//////////////////////////////////////////////////////////////////////
// fonction de recupération des documents 
function testFilesRequest($token, $cmd, $etat, $client){
    $client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost/', 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
    try {
        $response = $client->get('RESTapi//documents',[
            'headers' => makeHeaders($token), 'stream' => true ,
                'query' => [
                'cmd__id' => $cmd,
                'cmd__etat' => $etat,
                'cli__id' => $client
                ]
            ]
        );
    } catch (GuzzleHttp\Exception\ClientException $exeption) {
        $response = $exeption->getResponse();
    }
    $data = $response->getBody()->getContents();
    return $data;
}
/////////////////////////////////////////////////////////////////
//////////////////// fonction d" envoi de fichier //////////////
function postFile($token, $tkl_id, $file) {
    try {
      $client = new \GuzzleHttp\Client();
      $response = $client->request('POST', 'http://192.168.1.105:80/api/files', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token
        ],
        'multipart' => [
          [
            'name' => 'tkl_id',
            'contents' => $tkl_id
          ],
          [
            'name' => 'file',
            'contents' => fopen($file, 'r')
          ]
        ]
      ]);
      return $response;
    } catch (GuzzleHttp\Exception\GuzzleException $e) {
      return $e->getMessage();
    }
  }
///////////////////////////////////////////////////////////////////
////////////////////////// récupération de fichier ////////////////
function getFile($token, $tkl_id, $file) {
    try {
      $client = new \GuzzleHttp\Client();
      $response = $client->request('GET', 'http://192.168.1.105:80/api/files', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token
        ],
        'query' => [
          'tkl_id' => $tkl_id,
          'file' => $file
        ]
      ]);
      return $response;
    } catch (GuzzleHttp\Exception\GuzzleException $e) {
      return $e->getMessage();
    }
  }
///////////////////////////////////////////////////////////////
/////////////////////////Appel de fonction et affichage ////////////////////////////////
testFilesRequest(
    $token,
    '3212544',
    'VLD',
    '29377'
);
header('Content-Type: application/pdf');
echo $test;
die();
