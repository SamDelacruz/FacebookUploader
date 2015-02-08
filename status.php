<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once __DIR__ . '/vendor/autoload.php';
session_start();
use Uploader\FBPostHandler;

if(!(strtoupper($_SERVER['REQUEST_METHOD']) === 'POST')) {
    http_response_code(403);
    echo '<h1>403 Forbidden</h1>';
} else {
    if(!isset($_SESSION['fb_access_token']) || !isset($_POST['fb_status'])) {
        http_response_code(400);
    } else {
        $status = $_POST['fb_status'];
        $response = (new FBPostHandler(
            $_SESSION['fb_access_token']
        ))->postStatus($status);
        $hasPosted = !strpos($response, '_') === false;
        if($hasPosted) {
            $split = explode('_', $response);
            $postUrl = "http://facebook.com/" . $split[0] . "/posts/" . $split[1];
            echo $postUrl;
        } else {
            echo $response;
        }
    }
}