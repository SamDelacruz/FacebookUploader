<?php
// Facebook app settings
$app_id       = '1409910789303690';
$app_secret   = '161ca6547ad7742302bba61a5c152f54';
$host_root    = 'http://localhost:8888/';
$redirect_uri = $host_root . 'login.php';

// Requested permissions for the app - optional.
$permissions = array(
    'email',
    'user_location',
    'user_birthday',
    'publish_actions'
);

// Define the root directory.
define( 'ROOT', dirname( __FILE__ ) . '/' );

// Autoload the required files
require_once( ROOT . 'vendor/autoload.php' );

use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;


session_start();

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
if ( isset( $session ) ) {

    // Retrieve & store the access token in a session.
    $_SESSION['fb_access_token'] = $session->getToken();
    
    $session = new FacebookSession($session->getToken());
    
    //Use the Graph API to retrieve basic user details
    $request = new FacebookRequest($session, 'GET', '/me');
    $response = $request->execute();
    $graphObject = $response->getGraphObject()->asArray();
    
    //Set session variables for user details
    $_SESSION['fb_loggedIn'] = true;
    $_SESSION['fb_name'] = $graphObject['name'];
    $_SESSION['fb_id'] = $graphObject['id'];
    $_SESSION['fb_first_name'] = $graphObject['first_name'];
    $_SESSION['fb_last_name'] = $graphObject['last_name'];
    
    //Get the user's profile picture
    $request = (new FacebookRequest($session, 'GET',
        '/me/picture?type=square&redirect=false'))->execute();
    $picture = $request->getGraphObject()->asArray();
    $_SESSION['fb_picture_url'] = $picture['url'];
    
    $_SESSION['fb_logoutURL'] = $helper->getLogoutUrl( $session, $host_root . 'logout.php' );
    
    header("Location: " . $host_root);
    
} else {
    // Generate the login URL for Facebook authentication.
    $loginUrl = $helper->getLoginUrl($permissions);
    //Redirect to fb for authentication
    header("Location: " . $loginUrl);
}