<?php
require_once( __DIR__ . '/../vendor/autoload.php' );
use Uploader\FBPostHandler;

class SoapReceiver {
    public function echoMessage($inmessage) {
        if(isset($inmessage) && is_string($inmessage)) {
            if(strcasecmp($inmessage, 'hello world') === 0) {
                return "Well hello!";
            } else if(strcasecmp($inmessage, 'BOO!') === 0) {
                return "ARGH";
            }
        }
        return $inmessage;
    }
    
    public function facebookPost($token, $message) {
        $postHandler = new FBPostHandler($token);
        if(isset($postHandler) && is_string($message)){
            try {
                $response = $postHandler->postStatus($message);
                $hasPosted = !strpos($response, '_') === false;
                if($hasPosted) {
                    $split = explode('_', $response);
                    $postUrl = "http://facebook.com/" . $split[0] . "/posts/" . $split[1];
                    return $postUrl;
                } else {
                    throw new SoapFault("Server", $response);
                }
            } catch (Exception $e) {
                throw new SoapFault("Server", $e->getMessage());
            }
        }
    }
    
    
}

ini_set("soap.wsdl_cache_enabled", "0");
$server = new SoapServer("wsdl/receiver.wsdl");
$server->setClass("SoapReceiver");

if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST') {
    $server->handle();
}
else {
    if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'wsdl') === 0) {
        $wsdl = @implode('', @file('wsdl/receiver.wsdl'));
        if (strlen($wsdl) > 1) {
            header("Content-type: text/xml");
            echo $wsdl;
        } else {
            header("Status: 500 Internal Server Error");
            http_response_code(500);
            header("Content-type: text/html");
            echo "<h1>500 Internal Server Error</h1>";
        }
    } else {
        header("Status: 400 Bad Request");
        http_response_code(400);
        header("Content-type: text/html");
        echo "<h1>400 Bad Request</h1>";
    }
}