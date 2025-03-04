<?php

$apiServers = [];

function isFrontEnd(): bool {
    global $apiServers;
    $server =  $_SERVER["HTTP_HOST"];
    return isset($apiServers[$server]);
}

function logToTest(string $txt)
{
    return;
    $myfile = fopen("test.txt", "a+") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
}

function forwardRequestToPwamangeApi()
{
    global $apiServers;
    $server =  $_SERVER["HTTP_HOST"];
    $apiUrl = $apiServers[$server] ?? $server;

    $url = $apiUrl . $_SERVER['REQUEST_URI'];
    if (!empty($_SERVER['QUERY_STRING'])) {
        //   $url .= '?' . $_SERVER['QUERY_STRING'];
    };

    $txt = "\n\nxxxxxxxxxxxxxxxxxxxxxxx\n";
    $txt .= "\nURL : " . $url;
    $txt .= "\nREQUEST : " . json_encode($_REQUEST);
    $txt .= "\nREQUEST : " . json_encode($_REQUEST);
    $txt .= "\nQueryString : " . $_SERVER['QUERY_STRING'];
    $txt .= "\nHEADERS : " . json_encode(getallheaders());
    $txt .= "\nMETHOD : " . $_SERVER['REQUEST_METHOD'];
    $txt .= "\nBODY : \n" . file_get_contents('php://input');

    logToTest($txt);
    $curl = curl_init();;
    $postFields = file_get_contents('php://input');

    $headers = ['Content-Type: application/json'];

    foreach (getallheaders() as $key => $value) {
        if ($key == 'Authorization') {
            $headers[] = $key . ': ' . $value;
        }
    }

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $_SERVER['REQUEST_METHOD'],
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => $headers,
    ));


    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    $status = $info['http_code'];

    logToTest("\n\n" . $response);


    http_response_code($status);
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    die($response);
}

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        $t = substr($haystack, 0, 4) === "/api";
        return $t;
    }
}

if (str_starts_with($_SERVER ['REQUEST_URI'], '/api')) {

    if (isFrontEnd()) {
        forwardRequestToPwamangeApi();
    }
    require_once(__DIR__ . '/api_/App/Lib/php8.php');
    require_once 'api_/public/index.php';
    die;
}


include 'index.html';