<?php

namespace Uploader;

use Facebook\FacebookSession;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;

use Uploader\Utils\FBConfig;

class FBPhotoHandler {

    private $session;

    public function __construct($accessToken) {
        FacebookSession::setDefaultApplication(FBConfig::APP_ID, FBConfig::APP_SECRET);
        $this->setSession($accessToken);
    }

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

                $response = "";
                foreach($recentPosts as $post) {
                    $postArray = $post->asArray();
                    if(isset($postArray['object_id'])) {
                        if($postArray['object_id'] === $photoId) {
                            $response = $postArray['id'];
                            break;
                        }
                    }
                }
                
            } catch(FacebookRequestException $ex) {
                $response = $ex->getMessage();
            }
            
            return $response;
        } else {
            throw new \RuntimeException("Invalid Message");
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