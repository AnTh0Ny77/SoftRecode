
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
