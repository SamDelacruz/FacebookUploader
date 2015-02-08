<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once __DIR__ . '/vendor/autoload.php';
session_start();
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;

$app_id       = '1409910789303690';
$app_secret   = '161ca6547ad7742302bba61a5c152f54';

FacebookSession::setDefaultApplication( $app_id, $app_secret );

if(!$_SESSION['fb_loggedIn'] || !isset($_POST['fb_status'])) {
    //Redirect unless POST request contains status, and user is logged in
    header('Location: index.php');
} else {
    
    $status = $_POST['fb_status'];
    try {
        $session = new FacebookSession($_SESSION['fb_access_token']);
    } catch(FacebookRequestException $ex) {
        print_r($ex);
    } catch(Exception $ex) {
        print_r($ex);
    }
    
    if(isset($session)) {
        $message = $_POST['fb_status'];
        
        if(!is_string($message)) {
            echo '400 BAD REQUEST';
        } else {
            $response = (new FacebookRequest(
                $session, 'POST', '/me/feed', array(
                    'message' => $message
                )
            ))->execute()->getGraphObject()->getProperty('id');
            print_r($response);

        }

    } else {
        echo '401 UNAUTHORIZED';
    }

    
}