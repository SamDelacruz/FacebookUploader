<?php
// Autoload the required files
require_once(__DIR__ . '/../vendor/autoload.php');

use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Uploader\FBUser;
use Uploader\Utils\FBConfig;

/*
 * Logs the user in via Facebook PHP SDK v4
 * If there is no session active, the user will be redirected to Facebook to log in
 * The user will be redirected back to login.php, the session stored, and user
 * redirected back to the application homepage (index.php).
 */

// Facebook app settings
$app_id = FBConfig::APP_ID;
$app_secret = FBConfig::APP_SECRET;
$host_root = FBConfig::HOST_ROOT;
$redirect_uri = $host_root . 'login.php';
$permissions = FBConfig::getPermissions();

if(!isset($_SESSION)) {
    session_start();
}

// Initialize the Facebook SDK.
FacebookSession::setDefaultApplication( $app_id, $app_secret );
$helper = new FacebookRedirectLoginHelper( $redirect_uri );

// Authorize the user.
try {
    if (isset($_SESSION['fb_access_token'])) {
        // Check if an access token has already been set.
        $session = new FacebookSession($_SESSION['fb_access_token']);
        
        //Check that the access token is still valid
        try {
            if(!$session->validate()) {
                $session = null;
            }
        } catch(\Exception $ex) {
            $session = null;
        }
    } else {
        // Get access token from the code parameter in the URL.
        $session = $helper->getSessionFromRedirect();
    }
} catch(FacebookRequestException $ex) {

    // When Facebook returns an error.
    print_r($ex);
} catch(\Exception $ex) {

    // When validation fails or other local issues.
    print_r($ex);
}

//Now check that we have a session
if (isset($session)) {
    // Retrieve & store the access token in a session.
    $_SESSION['fb_access_token'] = $session->getToken();
    $_SESSION['fb_logout_url'] = $helper->getLogoutUrl($session, $host_root . 'logout.php');
    // Redirect to main page
    header("Location: index.php");
    
} else {
    // Generate the login URL for Facebook authentication.
    $loginUrl = $helper->getLoginUrl($permissions);
    //Redirect to fb for authentication
    header("Location: " . $loginUrl);
}