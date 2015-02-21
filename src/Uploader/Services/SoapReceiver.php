<?php

namespace Uploader\Services;

use Uploader\FBPostHandler;
use Uploader\Utils\iDatabaseAdapter;
use Exception;
use SoapFault;

/**
 * Class SoapReceiver
 * Implementation for SOAP service.
 * Handles operations: echoMessage(String message) and facebookPost(String accessToken, String message)
 * Logs requests to database via iDatabaseAdapter
 * @package Uploader\Services
 */
class SoapReceiver {
    
    private $dbAdapter;
    
    public function __construct(iDatabaseAdapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * Echos request message, unless the message is 'hello world'
     * or 'BOO!', which have their own responses.
     * @param $inmessage the message to echo
     * @return string response
     */
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

    /**
     * Posts a user defined message to their Facebook Timeline
     * Logs the user's message, IP, and timestamp to Database.
     * @param $token User's Facebook Access Token
     * @param $message Message to post to User's Timeline
     * @return string Post URL
     * @throws SoapFault If invalid arguments are passed, or Facebook SDK error
     */
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