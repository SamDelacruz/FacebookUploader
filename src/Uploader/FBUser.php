<?php

namespace Uploader;

use Uploader\Utils\FBConfig;

use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\FacebookRequestException;

/**
 * Class FBUser
 * Class represents a Facebook user's profile
 * @package Uploader
 */
class FBUser {
    private $session;
    private $loggedIn = false;
    private $userName;
    private $id;
    private $firstName;
    private $lastName;
    private $pictureUrl;

    /**
     * @param $accessToken Valid Facebook Access Token
     * @throws FacebookRequestException
     */
    public function __construct($accessToken) {
        FacebookSession::setDefaultApplication(FBConfig::APP_ID, FBConfig::APP_SECRET);
        $this->setSession($accessToken);
        
        $response = (new FacebookRequest(
            $this->session, 'GET', '/me')
        )->execute()->getGraphObject()->asArray();
        
        $this->userName = $response['name'];
        $this->id = $response['id'];
        $this->firstName = $response['first_name'];
        $this->lastName = $response['last_name'];
        $this->loggedIn = true;
        $this->pictureUrl = (new FacebookRequest(
            $this->session, 'GET', '/me/picture?type=square&redirect=false'
        ))->execute()->getGraphObject()->asArray()['url'];
    }


    /**
     * Sets session object
     * @param $accessToken Valid Facebook Access Token
     */
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

    /**
     * @return boolean
     */
    public function isLoggedIn() {
        return $this->loggedIn;
    }

    /**
     * @return mixed
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getPictureUrl() {
        return $this->pictureUrl;
    }

}