<?php

namespace Uploader;

use Facebook\FacebookSession;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;

use Uploader\Utils\FBConfig;

/**
 * Class FBPhotoHandler
 * Used to post photos to Facebook, abstracting away the details of Facebook's php SDK.
 * @package Uploader
 */
class FBPhotoHandler {

    private $session;

    /**
     * @param $accessToken A valid Facebook Access Token
     */
    public function __construct($accessToken) {
        FacebookSession::setDefaultApplication(FBConfig::APP_ID, FBConfig::APP_SECRET);
        $this->setSession($accessToken);
    }

    /**
     * @param $message String message to post alongside photo
     * @param $imagePath Filepath to image to post
     * @return string PhotoId of photo posted, or error details
     */
    public function postPhoto($message, $imagePath) {
        if(isset($this->session) && is_string($message) && is_string($imagePath)) {
            if(!$this->session->validate()) {
                throw new \RuntimeException("Invalid Session");
            }
            try {
                $photoId = (new FacebookRequest(
                    $this->session, 'POST', '/me/photos', array(
                        'message' => $message,
                        'source' => new \CURLFile($imagePath)
                    )
                ))->execute()->getGraphObject()->getProperty('id');
                
                $recentPosts = (new FacebookRequest(
                    $this->session, 'GET', '/me/feed'
                ))->execute()->getGraphObjectList();

                $response = "photoId:" . $photoId;
                
            } catch(FacebookRequestException $ex) {
                $response = $ex->getMessage();
            }
            
            return $response;
        } else {
            throw new \RuntimeException("Invalid Message");
        }
    }

    /**
     * Function for setting session object
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
}