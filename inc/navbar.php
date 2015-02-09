<?php
require_once( __DIR__ . '/../vendor/autoload.php' );
use Uploader\FBUser;

if(isset($_SESSION['fb_access_token'])) {
    $user = new FBUser($_SESSION['fb_access_token']);
}

include __DIR__ . '/../views/navbar.html';