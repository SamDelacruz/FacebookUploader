<?php

namespace Uploader;

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Uploader\Utils\FBConfig;

/**
 * Class FBPostHandler
 * Handles posting to a user's Facebook feed via the Graph API
 * @package Uploader
 */
class FBPostHandler {
    private $session;
    
    public function __construct($accessToken) {
        FacebookSession::setDefaultApplication(FBConfig::APP_ID, FBConfig::APP_SECRET);
        $this->setSession($accessToken);
    }

    /**
     * Post a Facebook status using the Graph API
     * @param $message string message to post
     * @return string id of the posted message
     * @throws \Facebook\FacebookRequestException if Facebook API returns error
     * @throws \BadMethodCallException if non-string passed as message
     * @throws \RuntimeException if session is invalidated
     */
    public function postStatus($message) {
        if(isset($this->session) && is_string($message)) {
            if(!$this->session->validate()) {
                throw new \RuntimeException("Invalid Session");
            }
            $response = (new FacebookRequest(
                $this->session, 'POST', '/me/feed', array(
                    'message' => $message
                )
            ))->execute()->getGraphObject()->getProperty('id');
            return $response;
        } else {
            throw new \BadMethodCallException("Invalid Message");
        }
    }
    
    public function setSession($accessToken) {
        if(is_string($accessToken)) {
            try {
                $this->session = new FacebookSession($accessToken);
            } catch(FacebookRequestException $ex) {
                print_r($ex);
            } catch(\Exception $ex) {
                print_r($ex);
            }
        } else {
            throw new \RuntimeException("Invalid Access Token");
        }
    }
}