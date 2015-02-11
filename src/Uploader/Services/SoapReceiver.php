<?php

namespace Uploader\Services;

use Uploader\FBPostHandler;
use Uploader\Utils\iDatabaseAdapter;
use Exception;
use SoapFault;

class SoapReceiver {
    
    private $dbAdapter;
    
    public function __construct(iDatabaseAdapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
    }
    
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
                    $logSuccess = $this->dbAdapter->logToDatabase($message, $_SERVER['REMOTE_ADDR'], time());
                    if($logSuccess === true) {
                        return $postUrl;
                    } else throw new SoapFault("Server", $logSuccess);
                } else {
                    throw new SoapFault("Server", $response);
                }
            } catch (Exception $e) {
                throw new SoapFault("Server", $e->getMessage());
            }
        }
    }
    
    

}