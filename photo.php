<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once __DIR__ . '/vendor/autoload.php';
session_start();
use Uploader\FBPhotoHandler;
use Facebook\FacebookRequestException;

if(!(strtoupper($_SERVER['REQUEST_METHOD']) === 'POST')) {
    http_response_code(403);
    echo '<h1>403 Forbidden</h1>';
} else {
    if(!isset($_SESSION['fb_access_token'])) {
        http_response_code(403);
    } else {
        if(empty($_FILES['file_data'])) {
            http_response_code(401);
        }
        $status = empty($_POST['fb_status'])? '' : $_POST['fb_status'];
        $images = $_FILES['file_data'];

        $ext = explode('.', basename($images['name']));
        $target = "uploads" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);
        $success = null;

        if(move_uploaded_file($images['tmp_name'], $target)) {
            $success = true;
        } else{
            $success = false;
        }

        if ($success === true) {

            try{
                $response = (new FBPhotoHandler(
                    $_SESSION['fb_access_token']
                ))->postPhoto($status, $target);
            } catch (FacebookRequestException $ex) {
                echo json_encode($ex->asArray());
            } catch (RuntimeException $ex) {
                echo json_encode($ex->asArray());
            }
            $hasPosted = !strpos($response, '_') === false;
            if($hasPosted) {
                $split = explode('_', $response);
                $postUrl = "http://facebook.com/" . $split[0] . "/posts/" . $split[1];
                $success = ['url' => $postUrl];
                $output =  json_encode($success);
            } else {
                $error = ['error' => $response];
                $output = json_encode($error);
            }

        } elseif ($success === false) {
            $output = ['error'=>'Error while uploading images.'];
            // delete any uploaded files
            unlink($target);
        } else {
            $output = ['error'=>'No files were processed.'];
        }

        echo json_encode($output);

    }
}