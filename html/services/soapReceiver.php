<?php
require_once( __DIR__ . '/../../vendor/autoload.php' );
use Uploader\Services\SoapReceiver;
use Uploader\Utils\DatabaseAdapter;

/**
 * Class SoapReceiverWrapper a thin wrapper around Uploader\Services\SoapReceiver
 * Needed to pass class by name to SoapServer.
 */
class SoapReceiverWrapper {
    
    private $dbAdapter;
    
    public function __construct($dbadapter) {
        $this->dbAdapter = $dbadapter;
    }
    
    public function echoMessage($inmessage) {
        return (new SoapReceiver($this->dbAdapter))->echoMessage($inmessage);
    }

    public function facebookPost($token, $message) {
        return (new SoapReceiver($this->dbAdapter))->facebookPost($token, $message);
    }
}

/*
 * Load database configuration
 */
$dbconfig = parse_ini_file(__DIR__ . "/../../config/dbconfig.ini");
$dbHost = $dbconfig['host'];
$dbPort = $dbconfig['port'];
$dbName = $dbconfig['database_name'];
$dbTable = $dbconfig['table_name'];
$dbUser = $dbconfig['username'];
$dbPass = $dbconfig['password'];

if(isset($dbconfig)) {
    ini_set("soap.wsdl_cache_enabled", "0");
    //Load WSDL and set message handler
    $server = new SoapServer("wsdl/receiver.wsdl");
    $server->setClass("SoapReceiverWrapper", new DatabaseAdapter($dbHost, $dbPort, $dbUser, $dbPass, $dbName, $dbTable));
    
    // Respond to POST request
    if (isset($_SERVER['REQUEST_METHOD']) &&
        $_SERVER['REQUEST_METHOD'] === 'POST') {
        $server->handle();
    } else {
        // Output WSDL when ?WSDL is appended to GET request
        if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'wsdl') === 0) {
            $wsdl = @implode('', file('wsdl/receiver.wsdl'));
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
}